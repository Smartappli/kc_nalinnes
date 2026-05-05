<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

session_start();

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash(string $message, string $type = 'info'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_classes(?array $flash): string {
    $type = $flash['type'] ?? 'info';
    switch ($type) {
        case 'success': return 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200';
        case 'error':   return 'border-red-500/40 bg-red-500/10 text-red-200';
        default:        return 'border-sky-500/40 bg-sky-500/10 text-sky-200';
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$flashMsg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

try {
    $db = new \PDO('mysql:dbname=my-database;host=127.0.0.1;charset=utf8mb4', 'my-username', 'my-password', [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);

    $auth = new \Delight\Auth\Auth($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        if ($auth->isLoggedIn()) {
            $auth->logOut();
        }

        flash('Vous êtes déconnecté.', 'success');
        header('Location: /membres.php', true, 303);
        exit;
    }

    if (!$auth->isLoggedIn()) {
        flash('Veuillez vous connecter pour accéder au dashboard membre.', 'error');
        header('Location: /membres.php', true, 303);
        exit;
    }

    $userId = (string)($auth->getUserId() ?? '');
    $email = (string)($auth->getEmail() ?? '');
    $user = (string)($auth->getUsername() ?? '');
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<pre style='white-space:pre-wrap'>500 ERROR\n" . e($e->getMessage()) . "</pre>";
    exit;
}
?>
<!doctype html>
<html lang="fr"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><title>Dashboard membre — KC Nalinnes</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><main class="mx-auto max-w-5xl px-4 py-10">
<h1 class="text-3xl font-extrabold">Dashboard membre</h1>
<p class="mt-1 text-slate-400">Espace membre connecté</p>
<form method="post" action="/member/dashboard.php" class="mt-4"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="logout"><button class="rounded-xl bg-red-600 px-4 py-2 font-semibold">Se déconnecter</button></form>
<?php if (is_array($flashMsg) && !empty($flashMsg['message'])): ?><div class="mt-6 rounded-xl border px-4 py-3 <?= e(flash_classes($flashMsg)) ?>"><?= e((string)$flashMsg['message']) ?></div><?php endif; ?>
<div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6"><dl class="space-y-2 text-sm"><div class="flex justify-between"><dt>User ID</dt><dd><?= e($userId) ?></dd></div><div class="flex justify-between"><dt>Email</dt><dd><?= e($email) ?></dd></div><div class="flex justify-between"><dt>Username</dt><dd><?= e($user !== '' ? $user : '—') ?></dd></div></dl></div>
</main></body></html>
