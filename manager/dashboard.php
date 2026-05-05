<?php
declare(strict_types=1);

ini_set('display_errors', '1');         // à enlever en prod
ini_set('display_startup_errors', '1'); // à enlever en prod
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/admin_access.php';

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

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Flash
$flashMsg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

try {
    // DB (à adapter)
    $db = new \PDO(
        'mysql:dbname=my-database;host=127.0.0.1;charset=utf8mb4',
        'my-username',
        'my-password',
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]
    );

    $auth = new \Delight\Auth\Auth($db);

    // Logout (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash('Requête invalide (CSRF).', 'error');
            header('Location: /manager/dashboard.php', true, 303);
            exit;
        }

        if ($auth->isLoggedIn()) {
            $auth->logOut();
        }

        flash('Vous êtes déconnecté.', 'success');
        header('Location: /membres.php', true, 303);
        exit;
    }

    // Protection : si pas connecté => login
    if (!$auth->isLoggedIn()) {
        flash('Veuillez vous connecter pour accéder au dashboard.', 'error');
        header('Location: /membres.php', true, 303);
        exit;
    }

    // Infos user
    $userId = (string)($auth->getUserId() ?? '');
    $email  = (string)($auth->getEmail() ?? '');
    $user   = (string)($auth->getUsername() ?? '');

    // Autorisation admin (emails séparés par des virgules dans ADMIN_EMAILS)
    $adminEmails = parse_admin_emails((string) getenv('ADMIN_EMAILS'));
    $isAdmin = is_admin_email($email, $adminEmails);

    if (!$isAdmin) {
        flash('Compte membre connecté : redirection vers votre dashboard.', 'info');
        header('Location: /member/dashboard.php', true, 303);
        exit;
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<pre style='white-space:pre-wrap'>500 ERROR\n"
        . e($e->getMessage()) . "\n\n"
        . e($e->getFile()) . ":" . (int)$e->getLine()
        . "</pre>";
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard — KC Nalinnes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <style>body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;}</style>
</head>
<body class="bg-slate-950 text-slate-100">
<main class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Dashboard</h1>
            <p class="mt-1 text-slate-400">Espace membre</p>
        </div>

        <form method="post" action="/manager/dashboard.php">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="logout">
            <button class="rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 transition">
                Se déconnecter
            </button>
        </form>
    </div>

    <?php if (is_array($flashMsg) && !empty($flashMsg['message'])): ?>
        <div class="mt-6 rounded-xl border px-4 py-3 <?= e(flash_classes($flashMsg)) ?>">
            <?= e((string)$flashMsg['message']) ?>
        </div>
    <?php endif; ?>

    <section class="mt-8 grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-xl font-bold">Statut</h2>
            <p class="mt-2 text-slate-300">✅ Connecté</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-xl font-bold">Compte</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-400">User ID</dt><dd class="font-semibold"><?= e($userId) ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400">Email</dt><dd class="font-semibold"><?= e($email) ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400">Username</dt><dd class="font-semibold"><?= e($user !== '' ? $user : '—') ?></dd></div>
            </dl>
        </div>
    </section>


    <section class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-bold">Gestion du calendrier (admin)</h2>
            <button id="btnNewEvent" class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500">Nouvel événement</button>
        </div>
        <p class="mt-2 text-sm text-slate-400">Créer, modifier, déplacer et supprimer des événements.</p>
        <div id="adminCalendar" class="mt-6"></div>
    </section>

    <dialog id="eventDialog" class="rounded-xl p-0 backdrop:bg-black/70">
      <form method="dialog" id="eventForm" class="w-[92vw] max-w-lg bg-slate-900 text-slate-100 p-5 space-y-4">
        <h3 class="text-lg font-bold" id="dialogTitle">Nouvel événement</h3>
        <input type="hidden" id="eventId">
        <div><label class="block text-sm">Titre</label><input id="eventTitle" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2" required></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div><label class="block text-sm">Début</label><input type="datetime-local" id="eventStart" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2" required></div>
          <div><label class="block text-sm">Fin</label><input type="datetime-local" id="eventEnd" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2"></div>
        </div>
        <div><label class="block text-sm">Description</label><textarea id="eventDesc" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2"></textarea></div>
        <div class="flex justify-between">
          <button type="button" id="btnDeleteEvent" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500 hidden">Supprimer</button>
          <div class="ml-auto flex gap-2">
            <button type="button" id="btnCancel" class="rounded-lg border border-slate-600 px-3 py-2 text-sm">Annuler</button>
            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Enregistrer</button>
          </div>
        </div>
      </form>
    </dialog>

</main>

<script>
(() => {
  const storageKey = 'kc_admin_calendar_events';
  const loadEvents = () => { try { return JSON.parse(localStorage.getItem(storageKey) || '[]'); } catch(e){ return []; } };
  const saveEvents = (events) => localStorage.setItem(storageKey, JSON.stringify(events));
  const toLocalInput = (date) => {
    if (!date) return '';
    const d = new Date(date);
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0,16);
  };

  const dialog = document.getElementById('eventDialog');
  const form = document.getElementById('eventForm');
  const fields = {
    id: document.getElementById('eventId'), title: document.getElementById('eventTitle'),
    start: document.getElementById('eventStart'), end: document.getElementById('eventEnd'),
    desc: document.getElementById('eventDesc')
  };
  const btnDelete = document.getElementById('btnDeleteEvent');
  const btnNew = document.getElementById('btnNewEvent');
  const btnCancel = document.getElementById('btnCancel');

  const calendar = new FullCalendar.Calendar(document.getElementById('adminCalendar'), {
    initialView: 'dayGridMonth',
    locale: 'fr',
    editable: true,
    selectable: true,
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth' },
    events: loadEvents(),
    select(info) { openDialog({ start: info.startStr, end: info.endStr }); },
    eventClick(info) {
      const e = info.event;
      openDialog({ id: e.id, title: e.title, start: e.start, end: e.end, description: e.extendedProps.description || '' });
    },
    eventDrop: persistFromCalendar,
    eventResize: persistFromCalendar
  });

  function persistFromCalendar() {
    const events = calendar.getEvents().map(e => ({ id: e.id, title: e.title, start: e.start?.toISOString(), end: e.end?.toISOString() || null, description: e.extendedProps.description || '' }));
    saveEvents(events);
  }

  function openDialog(data = {}) {
    document.getElementById('dialogTitle').textContent = data.id ? 'Modifier événement' : 'Nouvel événement';
    fields.id.value = data.id || '';
    fields.title.value = data.title || '';
    fields.start.value = toLocalInput(data.start);
    fields.end.value = toLocalInput(data.end);
    fields.desc.value = data.description || '';
    btnDelete.classList.toggle('hidden', !data.id);
    dialog.showModal();
  }

  btnNew.addEventListener('click', () => openDialog());
  btnCancel.addEventListener('click', () => dialog.close());

  form.addEventListener('submit', (ev) => {
    ev.preventDefault();
    const id = fields.id.value || String(Date.now());
    const payload = { id, title: fields.title.value.trim(), start: new Date(fields.start.value).toISOString(), end: fields.end.value ? new Date(fields.end.value).toISOString() : null, description: fields.desc.value.trim() };
    if (!payload.title || !payload.start) return;

    const existing = calendar.getEventById(id);
    if (existing) { existing.setProp('title', payload.title); existing.setStart(payload.start); existing.setEnd(payload.end); existing.setExtendedProp('description', payload.description); }
    else { calendar.addEvent(payload); }
    persistFromCalendar();
    dialog.close();
  });

  btnDelete.addEventListener('click', () => {
    const id = fields.id.value;
    if (!id) return;
    const existing = calendar.getEventById(id);
    if (existing) existing.remove();
    persistFromCalendar();
    dialog.close();
  });

  calendar.render();
})();
</script>

</body>
</html>