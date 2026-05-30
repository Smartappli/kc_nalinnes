<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/pdf_access.php';
require __DIR__ . '/meal_reservation.php';
require __DIR__ . '/../manager/admin_access.php';
require __DIR__ . '/../config/database.php';

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
    $db = create_database_connection();

    $auth = new \Delight\Auth\Auth($db);

    $db->exec('CREATE TABLE IF NOT EXISTS member_dependents (id INT AUTO_INCREMENT PRIMARY KEY, guardian_user_id INT NOT NULL, full_name VARCHAR(255) NOT NULL, birthdate DATE NULL, is_minor TINYINT(1) NOT NULL DEFAULT 1)');
    $db->exec('CREATE TABLE IF NOT EXISTS meal_reservations (id INT AUTO_INCREMENT PRIMARY KEY, member_user_id INT NOT NULL, profile_type VARCHAR(20) NOT NULL, dependent_id INT NULL, profile_name VARCHAR(255) NOT NULL, adult_qty INT NOT NULL DEFAULT 0, child_qty INT NOT NULL DEFAULT 0, total_amount DECIMAL(10,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');

    $loginBypassEnabled = is_temp_bypass_login_enabled();

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

    if (!$auth->isLoggedIn() && !$loginBypassEnabled) {
        flash('Veuillez vous connecter pour accéder au dashboard membre.', 'error');
        header('Location: /membres.php', true, 303);
        exit;
    }

    if ($loginBypassEnabled && !$auth->isLoggedIn()) {
        $userId = '1';
        $email = 'bypass@kc-nalinnes.be';
        $user = 'Bypass Temporaire';
    }
    else {
        $userId = (string)($auth->getUserId() ?? '');
        $email = (string)($auth->getEmail() ?? '');
        $user = (string)($auth->getUsername() ?? '');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'set_active_profile') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $activeType = (string)($_POST['active_type'] ?? 'self');
        $activeDependentId = (int)($_POST['active_dependent_id'] ?? 0);
        $_SESSION['active_profile'] = ['type' => 'self', 'dependent_id' => 0];

        if ($activeType === 'child' && $activeDependentId > 0) {
            $checkStmt = $db->prepare('SELECT id, is_minor FROM member_dependents WHERE id = :id AND guardian_user_id = :uid LIMIT 1');
            $checkStmt->execute([':id' => $activeDependentId, ':uid' => (int)$auth->getUserId()]);
            $row = $checkStmt->fetch();
            if ($row && (int)$row['is_minor'] === 1) {
                $_SESSION['active_profile'] = ['type' => 'child', 'dependent_id' => $activeDependentId];
            }
        }

        flash('Profil actif mis à jour.', 'success');
        header('Location: /member/dashboard.php', true, 303);
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'meal_reservation') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $adultQty = max(0, (int)($_POST['repas_adulte'] ?? 0));
        $childQty = max(0, (int)($_POST['repas_enfant'] ?? 0));
        $sendCopy = !empty($_POST['send_copy']) && $_POST['send_copy'] === '1';
        if ($adultQty === 0 && $childQty === 0) {
            flash('Veuillez sélectionner au moins un repas.', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $profileType = (string)($_POST['profile_type'] ?? 'self');
        $dependentId = (int)($_POST['dependent_id'] ?? 0);
        $profileName = $user !== '' ? $user : $email;

        if ($profileType === 'child') {
            $childStmt = $db->prepare('SELECT full_name, is_minor FROM member_dependents WHERE id = :id AND guardian_user_id = :uid LIMIT 1');
            $childStmt->execute([':id' => $dependentId, ':uid' => (int)$auth->getUserId()]);
            $child = $childStmt->fetch();
            if (!$child || (int)$child['is_minor'] !== 1) {
                flash('Profil enfant invalide pour réservation.', 'error');
                header('Location: /member/dashboard.php', true, 303);
                exit;
            }
            $profileName = (string)$child['full_name'];
        }

        $total = compute_meal_total($adultQty, $childQty, 19, 10);
        $stmt = $db->prepare('INSERT INTO meal_reservations (member_user_id, profile_type, dependent_id, profile_name, adult_qty, child_qty, total_amount) VALUES (:uid, :ptype, :did, :pname, :adult, :child, :total)');
        $stmt->execute([
            ':uid' => (int)$auth->getUserId(),
            ':ptype' => $profileType,
            ':did' => ($profileType === 'child' ? $dependentId : null),
            ':pname' => $profileName,
            ':adult' => $adultQty,
            ':child' => $childQty,
            ':total' => $total,
        ]);

        flash('Réservation repas enregistrée.', 'success');

        $to = (string)(getenv('RESERVATION_EMAIL_TO') ?: 'contact@kc-nalinnes.be');
        $subject = "Nouvelle réservation repas de fin d'année";
        $message = "Membre ID: " . (int)$auth->getUserId() . "\n"
            . "Profil: " . $profileName . " (" . $profileType . ")\n"
            . "Adultes: " . $adultQty . "\n"
            . "Enfants: " . $childQty . "\n"
            . "Total: " . $total . " EUR\n"
            . "Date: " . date('Y-m-d H:i:s') . "\n";
        @mail($to, $subject, $message, 'From: no-reply@kc-nalinnes.be');

        if ($sendCopy) {
            @mail($email, 'Copie de votre réservation repas', $message, 'From: no-reply@kc-nalinnes.be');
        }

        header('Location: /member/dashboard.php', true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'dependent_add') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $fullName = trim((string)($_POST['dependent_name'] ?? ''));
        $birthdate = trim((string)($_POST['dependent_birthdate'] ?? ''));
        $isMinor = !empty($_POST['dependent_is_minor']) ? 1 : 0;

        if ($fullName === '') {
            flash('Le nom de l\'enfant est obligatoire.', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $stmt = $db->prepare('INSERT INTO member_dependents (guardian_user_id, full_name, birthdate, is_minor) VALUES (:uid, :name, :birthdate, :is_minor)');
        $stmt->execute([':uid' => (int)$auth->getUserId(), ':name' => $fullName, ':birthdate' => ($birthdate !== '' ? $birthdate : null), ':is_minor' => $isMinor]);

        flash('Enfant ajouté.', 'success');
        header('Location: /member/dashboard.php', true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'dependent_delete') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $dependentId = (int)($_POST['dependent_id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM member_dependents WHERE id = :id AND guardian_user_id = :uid');
        $stmt->execute([':id' => $dependentId, ':uid' => (int)$auth->getUserId()]);

        flash('Enfant supprimé.', 'success');
        header('Location: /member/dashboard.php', true, 303);
        exit;
    }

    $depStmt = $db->prepare('SELECT id, full_name, birthdate, is_minor FROM member_dependents WHERE guardian_user_id = :uid ORDER BY id DESC');
    $depStmt->execute([':uid' => (int)$userId]);
    $dependents = $depStmt->fetchAll();
    $minorDependents = array_values(array_filter($dependents, static fn(array $d): bool => (int)$d['is_minor'] === 1));
    $manageableProfilesCount = 1 + count($minorDependents);

    $activeProfile = $_SESSION['active_profile'] ?? ['type' => 'self', 'dependent_id' => 0];

    $resStmt = $db->prepare('SELECT profile_name, adult_qty, child_qty, total_amount, created_at FROM meal_reservations WHERE member_user_id = :uid ORDER BY id DESC LIMIT 20');
    $resStmt->execute([':uid' => (int)$userId]);
    $reservations = $resStmt->fetchAll();
    $activeProfileName = $email;
    if (($activeProfile['type'] ?? 'self') === 'child') {
        foreach ($minorDependents as $child) {
            if ((int)$child['id'] === (int)($activeProfile['dependent_id'] ?? 0)) {
                $activeProfileName = (string)$child['full_name'];
                break;
            }
        }
    }

    $templatesDir = __DIR__ . '/../docs';
    $templateFiles = list_pdf_templates($templatesDir);
    $allowedTemplatesRaw = (string)(getenv('ALLOWED_PRECOMPLETED_PDFS') ?: 'mutualia-ac-sport-fr.pdf');
    $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $allowedTemplatesRaw))));
    if ($allowedTemplates !== []) {
        $templateFiles = array_values(array_filter($templateFiles, static fn(string $name): bool => in_array($name, $allowedTemplates, true)));
    }
    $downloadToken = build_download_token((string)$userId, (string)$_SESSION['csrf_token']);

    if (isset($_GET['download_mutuelle']) && $_GET['download_mutuelle'] === '1') {
        $profileType = (string)($_GET['profile'] ?? 'self');
        $dependentId = (int)($_GET['dependent_id'] ?? 0);
        $providedToken = (string)($_GET['token'] ?? '');

        if (!is_valid_download_token($providedToken, (string)$userId, (string)$_SESSION['csrf_token'])) {
            flash('Jeton de téléchargement invalide.', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $beneficiaryName = $user !== '' ? $user : $email;
        if ($profileType === 'child') {
            $childStmt = $db->prepare('SELECT full_name, is_minor FROM member_dependents WHERE id = :id AND guardian_user_id = :uid LIMIT 1');
            $childStmt->execute([':id' => $dependentId, ':uid' => (int)$userId]);
            $child = $childStmt->fetch();

            if (!$child) {
                flash('Profil enfant introuvable.', 'error');
                header('Location: /member/dashboard.php', true, 303);
                exit;
            }

            if ((int)$child['is_minor'] !== 1) {
                flash('Seuls les enfants mineurs sont éligibles à ce téléchargement.', 'error');
                header('Location: /member/dashboard.php', true, 303);
                exit;
            }

            $beneficiaryName = (string)$child['full_name'];
        }

        $requestedTemplate = basename((string)($_GET['template'] ?? 'mutualia-ac-sport-fr.pdf'));
        if (!is_allowed_template($requestedTemplate, $templateFiles)) {
            flash('Template PDF non autorisé.', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $templatePath = $templatesDir . '/' . $requestedTemplate;
        if (!is_file($templatePath)) {
            flash('Template PDF introuvable.', 'error');
            header('Location: /member/dashboard.php', true, 303);
            exit;
        }

        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($templatePath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tpl = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            if ($pageNo === 1) {
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(20, 35);
                $pdf->Cell(120, 6, utf8_decode('Bénéficiaire: ' . $beneficiaryName));
                $pdf->SetXY(20, 42);
                $pdf->Cell(120, 6, utf8_decode('Membre responsable: ' . ($user !== '' ? $user : $email)));
                $pdf->SetXY(20, 49);
                $pdf->Cell(120, 6, utf8_decode('Date: ' . date('d/m/Y')));
            }
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="mutuelle-precomplete-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($beneficiaryName)) . '.pdf"');
        echo $pdf->Output('S');
        exit;
    }

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
<p class="mt-1 text-slate-400">Espace membre connecté — profils gérables: <?= e((string)$manageableProfilesCount) ?></p>
<form method="post" action="/member/dashboard.php" class="mt-4"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="logout"><button class="rounded-xl bg-red-600 px-4 py-2 font-semibold">Se déconnecter</button></form>
<?php if (is_array($flashMsg) && !empty($flashMsg['message'])): ?><div class="mt-6 rounded-xl border px-4 py-3 <?= e(flash_classes($flashMsg)) ?>"><?= e((string)$flashMsg['message']) ?></div><?php endif; ?>
<div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6"><dl class="space-y-2 text-sm"><div class="flex justify-between"><dt>User ID</dt><dd><?= e($userId) ?></dd></div><div class="flex justify-between"><dt>Email</dt><dd><?= e($email) ?></dd></div><div class="flex justify-between"><dt>Username</dt><dd><?= e($user !== '' ? $user : '—') ?></dd></div></dl></div>



<section class="mt-6 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold">Profil actif</h2>
  <p class="mt-2 text-sm text-slate-400">Choisissez le profil (vous ou un enfant mineur) pour vos démarches.</p>
  <form method="post" action="/member/dashboard.php" class="mt-3 flex flex-wrap items-center gap-2">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="set_active_profile">
    <select name="active_type" id="activeType" class="rounded-lg bg-slate-800 border border-slate-700 px-2 py-1">
      <option value="self" <?= (($activeProfile['type'] ?? 'self') === 'self' ? 'selected' : '') ?>>Moi-même</option>
      <option value="child" <?= (($activeProfile['type'] ?? 'self') === 'child' ? 'selected' : '') ?>>Enfant mineur</option>
    </select>
    <select name="active_dependent_id" class="rounded-lg bg-slate-800 border border-slate-700 px-2 py-1">
      <option value="0">-- Choisir un enfant --</option>
      <?php foreach ($minorDependents as $child): ?>
      <option value="<?= e((string)$child['id']) ?>" <?= ((int)($activeProfile['dependent_id'] ?? 0) === (int)$child['id'] ? 'selected' : '') ?>><?= e((string)$child['full_name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500">Définir</button>
  </form>
</section>


<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold">Réservation repas de fin d'année</h2>
  <p class="mt-2 text-sm text-slate-400">Vous pouvez réserver pour vous-même ou pour un enfant mineur géré.</p>
  <form method="post" action="/member/dashboard.php" class="mt-4 grid gap-3 md:grid-cols-2">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="meal_reservation">
    <div>
      <label class="block text-sm">Profil</label>
      <select name="profile_type" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
        <option value="self">Moi-même</option>
        <option value="child">Enfant mineur</option>
      </select>
    </div>
    <div>
      <label class="block text-sm">Enfant (si profil enfant)</label>
      <select name="dependent_id" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
        <option value="0">-- Aucun --</option>
        <?php foreach ($minorDependents as $child): ?>
          <option value="<?= e((string)$child['id']) ?>"><?= e((string)$child['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm">Adultes (19€) — 1 brochette + 1 saucisse</label>
      <input type="number" min="0" name="repas_adulte" value="0" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm">Enfants (10€) — 1 saucisse ou 1 brochette + frites</label>
      <input type="number" min="0" name="repas_enfant" value="0" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
    </div>
    <div class="md:col-span-2">
      <label class="inline-flex items-center gap-2 text-sm mb-2"><input type="checkbox" name="send_copy" value="1"> Recevoir une copie de la réservation par email</label><br>
      <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Réserver</button>
    </div>
  </form>

  <div class="mt-5">
    <h3 class="font-semibold">Mes réservations</h3>
    <div class="mt-2 space-y-2 text-sm">
      <?php foreach ($reservations as $r): ?>
        <div class="rounded-lg border border-slate-800 p-2">
          <?= e((string)$r['created_at']) ?> — <?= e((string)$r['profile_name']) ?> — Adultes: <?= e((string)$r['adult_qty']) ?>, Enfants: <?= e((string)$r['child_qty']) ?>, Total: <?= e((string)$r['total_amount']) ?> €
        </div>
      <?php endforeach; ?>
      <?php if ($reservations === []): ?><p class="text-slate-400">Aucune réservation pour le moment.</p><?php endif; ?>
    </div>
  </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold">PDF mutuelle précomplétés</h2>
  <p class="mt-2 text-sm text-slate-400">Téléchargez les formulaires mutuelle préremplis pour vous et vos enfants mineurs.</p>
  <div class="mt-3 rounded-lg border border-slate-800 p-3 text-sm">Profil actif: <strong><?= e($activeProfileName) ?></strong></div>
  <div class="mt-4 space-y-2">
    <?php foreach ($templateFiles as $tpl): ?>
      <div class="rounded-lg border border-slate-800 p-3">
        <p class="text-sm font-semibold mb-2">Template: <?= e($tpl) ?></p>
        <?php if (($activeProfile['type'] ?? 'self') === 'self'): ?>
          <a class="inline-block rounded-lg bg-violet-600 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-500 mr-2" href="/member/dashboard.php?download_mutuelle=1&profile=self&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>">Télécharger pour le profil actif</a>
        <?php else: ?>
          <a class="inline-block rounded-lg bg-violet-600 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-500 mr-2" href="/member/dashboard.php?download_mutuelle=1&profile=child&dependent_id=<?= e((string)($activeProfile['dependent_id'] ?? 0)) ?>&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>">Télécharger pour le profil actif</a>
        <?php endif; ?>
        <a class="inline-block rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500" href="/member/dashboard.php?download_mutuelle=1&profile=self&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>">Télécharger pour moi</a>
        <?php foreach ($dependents as $child): ?>
          <?php if ((int)$child['is_minor'] === 1): ?>
          <div class="mt-2">
            <a class="inline-block rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500" href="/member/dashboard.php?download_mutuelle=1&profile=child&dependent_id=<?= e((string)$child['id']) ?>&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>">Télécharger pour <?= e((string)$child['full_name']) ?></a>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <?php if ($templateFiles === []): ?>
      <p class="text-sm text-red-300">Aucun PDF précomplété trouvé dans <code>docs</code>.</p>
    <?php endif; ?>
  </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold">Agir pour moi / mes enfants mineurs</h2>
  <p class="mt-2 text-sm text-slate-400">Vous pouvez effectuer vos démarches pour vous-même et pour vos enfants mineurs.</p>

  <div class="mt-4 rounded-xl border border-slate-800 p-4">
    <h3 class="font-semibold">Profils concernés</h3>
    <ul class="mt-2 space-y-2 text-sm">
      <li>• Vous : <strong><?= e($email) ?></strong></li>
      <?php foreach ($dependents as $child): ?>
        <li>• Enfant : <strong><?= e((string)$child['full_name']) ?></strong> <?= ((int)$child['is_minor'] === 1 ? '(mineur)' : '(majeur)') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="mt-6 grid gap-6 md:grid-cols-2">
    <form method="post" action="/member/dashboard.php" class="rounded-xl border border-slate-800 p-4 space-y-3">
      <h3 class="font-semibold">Ajouter un enfant</h3>
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="action" value="dependent_add">
      <div><label class="block text-sm">Nom complet</label><input name="dependent_name" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2" required></div>
      <div><label class="block text-sm">Date de naissance</label><input type="date" name="dependent_birthdate" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2"></div>
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="dependent_is_minor" value="1" checked> Enfant mineur</label>
      <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Ajouter</button>
    </form>

    <div class="rounded-xl border border-slate-800 p-4">
      <h3 class="font-semibold">Supprimer un enfant</h3>
      <div class="mt-3 space-y-2">
        <?php foreach ($dependents as $child): ?>
          <form method="post" action="/member/dashboard.php" class="flex items-center justify-between gap-2">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="dependent_delete">
            <input type="hidden" name="dependent_id" value="<?= e((string)$child['id']) ?>">
            <span class="text-sm"><?= e((string)$child['full_name']) ?></span>
            <button class="rounded-lg bg-red-600 px-2 py-1 text-xs font-semibold text-white hover:bg-red-500">Supprimer</button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

</main></body></html>
