<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/i18n.php';
require __DIR__ . '/pdf_access.php';
require __DIR__ . '/meal_reservation.php';
require __DIR__ . '/../manager/admin_access.php';
require __DIR__ . '/../config/database.php';

session_start();

$locale = kc_current_locale();

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

function member_dashboard_url(): string {
    return '/member/dashboard.php?lang=' . rawurlencode(kc_current_locale());
}

function members_login_url(): string {
    return '/membres.php?lang=' . rawurlencode(kc_current_locale());
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
    ensure_meal_reservations_table($db);

    $loginBypassEnabled = is_temp_bypass_login_enabled();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        if ($auth->isLoggedIn()) {
            $auth->logOut();
        }

        flash(kc_t('member.flash.logged_out'), 'success');
        header('Location: ' . members_login_url(), true, 303);
        exit;
    }

    if (!$auth->isLoggedIn() && !$loginBypassEnabled) {
        flash(kc_t('member.flash.login_required'), 'error');
        header('Location: ' . members_login_url(), true, 303);
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
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
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

        flash(kc_t('member.flash.active_profile_updated'), 'success');
        header('Location: ' . member_dashboard_url(), true, 303);
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'meal_reservation') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        $postedSubmissionToken = (string)($_POST['meal_submission_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        if (!consume_meal_reservation_submission_token('member', $postedSubmissionToken)) {
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $adultQty = max(0, (int)($_POST['repas_adulte'] ?? 0));
        $childQty = max(0, (int)($_POST['repas_enfant'] ?? 0));
        $sendCopy = !empty($_POST['send_copy']) && $_POST['send_copy'] === '1';
        if ($adultQty === 0 && $childQty === 0) {
            flash(kc_t('meal.flash.no_meal'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
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
                flash(kc_t('member.flash.invalid_child_reservation'), 'error');
                header('Location: ' . member_dashboard_url(), true, 303);
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

        $reservationDate = date('Y-m-d H:i:s');
        append_meal_reservation_to_excel([
            'date' => $reservationDate,
            'member_user_id' => (string)(int)$auth->getUserId(),
            'profile_name' => $profileName,
            'profile_type' => $profileType,
            'contact_email' => $email,
            'contact_phone' => '',
            'adult_qty' => (string)$adultQty,
            'child_qty' => (string)$childQty,
            'total_amount' => (string)$total,
            'notes' => '',
        ]);

        flash(kc_t('member.flash.meal_saved'), 'success');

        $to = (string)(getenv('RESERVATION_EMAIL_TO') ?: 'contact@kc-nalinnes.be');
        $subject = kc_t('member.meal.mail_subject');
        $message = kc_t('member.meal.mail_member_id') . ": " . (int)$auth->getUserId() . "\n"
            . kc_t('member.meal.mail_profile') . ": " . $profileName . " (" . $profileType . ")\n"
            . kc_t('meal.mail.adults') . ": " . $adultQty . "\n"
            . kc_t('meal.mail.children') . ": " . $childQty . "\n"
            . kc_t('meal.mail.total') . ": " . $total . " EUR\n"
            . "Date: " . $reservationDate . "\n";
        @mail($to, $subject, $message, 'From: no-reply@kc-nalinnes.be');

        if ($sendCopy) {
            @mail($email, kc_t('member.meal.mail_copy_subject'), $message, 'From: no-reply@kc-nalinnes.be');
        }

        header('Location: ' . member_dashboard_url(), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'dependent_add') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $fullName = trim((string)($_POST['dependent_name'] ?? ''));
        $birthdate = trim((string)($_POST['dependent_birthdate'] ?? ''));
        $isMinor = !empty($_POST['dependent_is_minor']) ? 1 : 0;

        if ($fullName === '') {
            flash(kc_t('member.flash.child_name_required'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $stmt = $db->prepare('INSERT INTO member_dependents (guardian_user_id, full_name, birthdate, is_minor) VALUES (:uid, :name, :birthdate, :is_minor)');
        $stmt->execute([':uid' => (int)$auth->getUserId(), ':name' => $fullName, ':birthdate' => ($birthdate !== '' ? $birthdate : null), ':is_minor' => $isMinor]);

        flash(kc_t('member.flash.child_added'), 'success');
        header('Location: ' . member_dashboard_url(), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'dependent_delete') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash(kc_t('member.flash.csrf'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $dependentId = (int)($_POST['dependent_id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM member_dependents WHERE id = :id AND guardian_user_id = :uid');
        $stmt->execute([':id' => $dependentId, ':uid' => (int)$auth->getUserId()]);

        flash(kc_t('member.flash.child_deleted'), 'success');
        header('Location: ' . member_dashboard_url(), true, 303);
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
            flash(kc_t('member.flash.invalid_download_token'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $beneficiaryName = $user !== '' ? $user : $email;
        if ($profileType === 'child') {
            $childStmt = $db->prepare('SELECT full_name, is_minor FROM member_dependents WHERE id = :id AND guardian_user_id = :uid LIMIT 1');
            $childStmt->execute([':id' => $dependentId, ':uid' => (int)$userId]);
            $child = $childStmt->fetch();

            if (!$child) {
                flash(kc_t('member.flash.child_not_found'), 'error');
                header('Location: ' . member_dashboard_url(), true, 303);
                exit;
            }

            if ((int)$child['is_minor'] !== 1) {
                flash(kc_t('member.flash.minor_only'), 'error');
                header('Location: ' . member_dashboard_url(), true, 303);
                exit;
            }

            $beneficiaryName = (string)$child['full_name'];
        }

        $requestedTemplate = basename((string)($_GET['template'] ?? 'mutualia-ac-sport-fr.pdf'));
        if (!is_allowed_template($requestedTemplate, $templateFiles)) {
            flash(kc_t('member.flash.template_not_allowed'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
            exit;
        }

        $templatePath = $templatesDir . '/' . $requestedTemplate;
        if (!is_file($templatePath)) {
            flash(kc_t('member.flash.template_missing'), 'error');
            header('Location: ' . member_dashboard_url(), true, 303);
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

    $mealSubmissionToken = meal_reservation_submission_token('member');

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<pre style='white-space:pre-wrap'>500 ERROR\n" . e($e->getMessage()) . "</pre>";
    exit;
}
?>
<!doctype html>
<html<?= kc_translate_guard_attr($locale) ?> lang="<?= e($locale) ?>"><head><meta charset="utf-8"/>
  <?= kc_google_notranslate_meta($locale) ?><meta name="viewport" content="width=device-width, initial-scale=1"/><title><?= e(kc_t('member.meta.title')) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><main class="mx-auto max-w-5xl px-4 py-10">
<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
  <div>
    <h1 class="text-3xl font-extrabold"><?= e(kc_t('member.heading')) ?></h1>
    <p class="mt-1 text-slate-400"><?= e(kc_t('member.subtitle', ['count' => $manageableProfilesCount])) ?></p>
  </div>
  <?= kc_language_switcher('flex items-center gap-2') ?>
</div>
<form method="post" action="<?= e(member_dashboard_url()) ?>" class="mt-4"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="logout"><button class="rounded-xl bg-red-600 px-4 py-2 font-semibold"><?= e(kc_t('member.logout')) ?></button></form>
<?php if (is_array($flashMsg) && !empty($flashMsg['message'])): ?><div class="mt-6 rounded-xl border px-4 py-3 <?= e(flash_classes($flashMsg)) ?>"><?= e((string)$flashMsg['message']) ?></div><?php endif; ?>
<div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6"><dl class="space-y-2 text-sm"><div class="flex justify-between"><dt>User ID</dt><dd><?= e($userId) ?></dd></div><div class="flex justify-between"><dt>Email</dt><dd><?= e($email) ?></dd></div><div class="flex justify-between"><dt>Username</dt><dd><?= e($user !== '' ? $user : kc_t('member.user.username_empty')) ?></dd></div></dl></div>



<section class="mt-6 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold"><?= e(kc_t('member.active.title')) ?></h2>
  <p class="mt-2 text-sm text-slate-400"><?= e(kc_t('member.active.description')) ?></p>
  <form method="post" action="<?= e(member_dashboard_url()) ?>" class="mt-3 flex flex-wrap items-center gap-2">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="set_active_profile">
    <select name="active_type" id="activeType" class="rounded-lg bg-slate-800 border border-slate-700 px-2 py-1">
      <option value="self" <?= (($activeProfile['type'] ?? 'self') === 'self' ? 'selected' : '') ?>><?= e(kc_t('member.active.self')) ?></option>
      <option value="child" <?= (($activeProfile['type'] ?? 'self') === 'child' ? 'selected' : '') ?>><?= e(kc_t('member.active.child')) ?></option>
    </select>
    <select name="active_dependent_id" class="rounded-lg bg-slate-800 border border-slate-700 px-2 py-1">
      <option value="0"><?= e(kc_t('member.active.choose_child')) ?></option>
      <?php foreach ($minorDependents as $child): ?>
      <option value="<?= e((string)$child['id']) ?>" <?= ((int)($activeProfile['dependent_id'] ?? 0) === (int)$child['id'] ? 'selected' : '') ?>><?= e((string)$child['full_name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500"><?= e(kc_t('member.active.set')) ?></button>
  </form>
</section>


<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold"><?= e(kc_t('member.meal.title')) ?></h2>
  <p class="mt-2 text-sm text-slate-400"><?= e(kc_t('member.meal.description')) ?></p>
  <form method="post" action="<?= e(member_dashboard_url()) ?>" class="mt-4 grid gap-3 md:grid-cols-2" data-disable-on-submit>
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="meal_submission_token" value="<?= e($mealSubmissionToken) ?>">
    <input type="hidden" name="action" value="meal_reservation">
    <div>
      <label class="block text-sm"><?= e(kc_t('member.meal.profile')) ?></label>
      <select name="profile_type" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
        <option value="self"><?= e(kc_t('member.active.self')) ?></option>
        <option value="child"><?= e(kc_t('member.active.child')) ?></option>
      </select>
    </div>
    <div>
      <label class="block text-sm"><?= e(kc_t('member.meal.child_if')) ?></label>
      <select name="dependent_id" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
        <option value="0"><?= e(kc_t('member.meal.none')) ?></option>
        <?php foreach ($minorDependents as $child): ?>
          <option value="<?= e((string)$child['id']) ?>"><?= e((string)$child['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm"><?= e(kc_t('member.meal.adult_label')) ?></label>
      <input type="number" min="0" name="repas_adulte" value="0" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
    </div>
    <div>
      <label class="block text-sm"><?= e(kc_t('member.meal.child_label')) ?></label>
      <input type="number" min="0" name="repas_enfant" value="0" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
    </div>
    <div class="md:col-span-2">
      <label class="inline-flex items-center gap-2 text-sm mb-2"><input type="checkbox" name="send_copy" value="1"> <?= e(kc_t('member.meal.copy')) ?></label><br>
      <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-70"><?= e(kc_t('member.meal.reserve')) ?></button>
    </div>
  </form>

  <div class="mt-5">
    <h3 class="font-semibold"><?= e(kc_t('member.meal.mine')) ?></h3>
    <div class="mt-2 space-y-2 text-sm">
      <?php foreach ($reservations as $r): ?>
        <div class="rounded-lg border border-slate-800 p-2">
          <?= e((string)$r['created_at']) ?> - <?= e((string)$r['profile_name']) ?> - <?= e(kc_t('member.meal.list_adults')) ?>: <?= e((string)$r['adult_qty']) ?>, <?= e(kc_t('member.meal.list_children')) ?>: <?= e((string)$r['child_qty']) ?>, <?= e(kc_t('member.meal.list_total')) ?>: <?= e((string)$r['total_amount']) ?> EUR
        </div>
      <?php endforeach; ?>
      <?php if ($reservations === []): ?><p class="text-slate-400"><?= e(kc_t('member.meal.none_yet')) ?></p><?php endif; ?>
    </div>
  </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold"><?= e(kc_t('member.pdf.title')) ?></h2>
  <p class="mt-2 text-sm text-slate-400"><?= e(kc_t('member.pdf.description')) ?></p>
  <div class="mt-3 rounded-lg border border-slate-800 p-3 text-sm"><?= e(kc_t('member.pdf.active_profile')) ?>: <strong><?= e($activeProfileName) ?></strong></div>
  <div class="mt-4 space-y-2">
    <?php foreach ($templateFiles as $tpl): ?>
      <div class="rounded-lg border border-slate-800 p-3">
        <p class="text-sm font-semibold mb-2"><?= e(kc_t('member.pdf.template')) ?>: <?= e($tpl) ?></p>
        <?php if (($activeProfile['type'] ?? 'self') === 'self'): ?>
          <a class="inline-block rounded-lg bg-violet-600 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-500 mr-2" href="<?= e(member_dashboard_url()) ?>&download_mutuelle=1&profile=self&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>"><?= e(kc_t('member.pdf.download_active')) ?></a>
        <?php else: ?>
          <a class="inline-block rounded-lg bg-violet-600 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-500 mr-2" href="<?= e(member_dashboard_url()) ?>&download_mutuelle=1&profile=child&dependent_id=<?= e((string)($activeProfile['dependent_id'] ?? 0)) ?>&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>"><?= e(kc_t('member.pdf.download_active')) ?></a>
        <?php endif; ?>
        <a class="inline-block rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500" href="<?= e(member_dashboard_url()) ?>&download_mutuelle=1&profile=self&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>"><?= e(kc_t('member.pdf.download_self')) ?></a>
        <?php foreach ($dependents as $child): ?>
          <?php if ((int)$child['is_minor'] === 1): ?>
          <div class="mt-2">
            <a class="inline-block rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500" href="<?= e(member_dashboard_url()) ?>&download_mutuelle=1&profile=child&dependent_id=<?= e((string)$child['id']) ?>&template=<?= urlencode($tpl) ?>&token=<?= urlencode($downloadToken) ?>"><?= e(kc_t('member.pdf.download_child', ['name' => (string)$child['full_name']])) ?></a>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <?php if ($templateFiles === []): ?>
      <p class="text-sm text-red-300"><?= e(kc_t('member.pdf.none', ['dir' => 'docs'])) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
  <h2 class="text-xl font-bold"><?= e(kc_t('member.dependents.title')) ?></h2>
  <p class="mt-2 text-sm text-slate-400"><?= e(kc_t('member.dependents.description')) ?></p>

  <div class="mt-4 rounded-xl border border-slate-800 p-4">
    <h3 class="font-semibold"><?= e(kc_t('member.dependents.concerned')) ?></h3>
    <ul class="mt-2 space-y-2 text-sm">
      <li>• <?= e(kc_t('member.dependents.you')) ?> : <strong><?= e($email) ?></strong></li>
      <?php foreach ($dependents as $child): ?>
        <li>• <?= e(kc_t('member.dependents.child')) ?> : <strong><?= e((string)$child['full_name']) ?></strong> <?= ((int)$child['is_minor'] === 1 ? '(' . e(kc_t('member.dependents.minor')) . ')' : '(' . e(kc_t('member.dependents.adult')) . ')') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="mt-6 grid gap-6 md:grid-cols-2">
    <form method="post" action="<?= e(member_dashboard_url()) ?>" class="rounded-xl border border-slate-800 p-4 space-y-3">
      <h3 class="font-semibold"><?= e(kc_t('member.dependents.add_title')) ?></h3>
      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="action" value="dependent_add">
      <div><label class="block text-sm"><?= e(kc_t('member.dependents.full_name')) ?></label><input name="dependent_name" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2" required></div>
      <div><label class="block text-sm"><?= e(kc_t('member.dependents.birthdate')) ?></label><input type="date" name="dependent_birthdate" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2"></div>
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="dependent_is_minor" value="1" checked> <?= e(kc_t('member.dependents.is_minor')) ?></label>
      <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500"><?= e(kc_t('member.dependents.add')) ?></button>
    </form>

    <div class="rounded-xl border border-slate-800 p-4">
      <h3 class="font-semibold"><?= e(kc_t('member.dependents.delete_title')) ?></h3>
      <div class="mt-3 space-y-2">
        <?php foreach ($dependents as $child): ?>
          <form method="post" action="<?= e(member_dashboard_url()) ?>" class="flex items-center justify-between gap-2">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="dependent_delete">
            <input type="hidden" name="dependent_id" value="<?= e((string)$child['id']) ?>">
            <span class="text-sm"><?= e((string)$child['full_name']) ?></span>
            <button class="rounded-lg bg-red-600 px-2 py-1 text-xs font-semibold text-white hover:bg-red-500"><?= e(kc_t('member.dependents.delete')) ?></button>
          </form>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
  document.querySelectorAll('form[data-disable-on-submit]').forEach(function (form) {
    form.addEventListener('submit', function () {
      if (form.dataset.submitting === '1') {
        return;
      }

      form.dataset.submitting = '1';
      form.querySelectorAll('button[type="submit"], button:not([type])').forEach(function (button) {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
      });
    });
  });
</script>
</main></body></html>
