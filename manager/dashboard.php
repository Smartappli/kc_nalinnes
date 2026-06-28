<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';

configure_error_reporting_from_env();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/fpdf_alias.php';
require __DIR__ . '/../includes/i18n.php';
require __DIR__ . '/../includes/calendar_events.php';
require __DIR__ . '/admin_access.php';
require __DIR__ . '/member_admin.php';
require __DIR__ . '/../member/pdf_access.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../member/meal_reservation.php';

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

function manager_dashboard_url(): string {
    return '/manager/dashboard.php?lang=' . rawurlencode(kc_current_locale());
}

function manager_dashboard_url_with_params(array $params): string {
    $query = array_merge(['lang' => kc_current_locale()], $params);

    return '/manager/dashboard.php?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
}

function manager_login_url(): string {
    return '/membres.php?lang=' . rawurlencode(kc_current_locale());
}

function manager_member_dashboard_url(): string {
    return '/member/dashboard.php?lang=' . rawurlencode(kc_current_locale());
}

function manager_dashboard_anchor_url(string $anchor): string {
    return manager_dashboard_url() . '#' . ltrim($anchor, '#');
}

function manager_dashboard_anchor_url_with_params(string $anchor, array $params): string {
    return manager_dashboard_url_with_params($params) . '#' . ltrim($anchor, '#');
}

function manager_member_payment_year(mixed $value): int {
    $year = (int)$value;
    if ($year < 2000 || $year > 2100) {
        return (int)date('Y');
    }

    return $year;
}

function require_manager_csrf(): void {
    $postedToken = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $postedToken)) {
        flash(kc_t('manager.flash.csrf'), 'error');
        header('Location: ' . manager_dashboard_url(), true, 303);
        exit;
    }
}

function manager_admin_nav_items(): array {
    return [
        'admin-overview' => 'Tableau de bord',
        'admin-meal' => 'Reservation repas',
        'admin-users' => 'Membres',
        'admin-meal-summary' => 'Suivi repas',
        'admin-calendar' => 'Calendrier',
    ];
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Flash
$flashMsg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

try {
    $db = create_database_connection();

    $auth = new \Delight\Auth\Auth($db);
    ensure_meal_reservations_table($db);
    ensure_meal_reservations_columns($db);
    ensure_meal_settings_table($db);
    $mealSettings = meal_settings($db);

    $db->exec('CREATE TABLE IF NOT EXISTS member_grades (user_id INT PRIMARY KEY, grade VARCHAR(100) NOT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)');
    $db->exec('CREATE TABLE IF NOT EXISTS member_dependents (id INT AUTO_INCREMENT PRIMARY KEY, guardian_user_id INT NOT NULL, full_name VARCHAR(255) NOT NULL, birthdate DATE NULL, is_minor TINYINT(1) NOT NULL DEFAULT 1)');
    ensure_member_records_tables($db);
    ensure_calendar_events_table($db);

    $loginBypassEnabled = is_temp_bypass_login_enabled();

    // Logout (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
            flash(kc_t('manager.flash.csrf'), 'error');
            header('Location: ' . manager_dashboard_anchor_url('admin-users'), true, 303);
            exit;
        }

        if ($auth->isLoggedIn()) {
            $auth->logOut();
        }

        flash(kc_t('manager.flash.logged_out'), 'success');
        header('Location: ' . manager_login_url(), true, 303);
        exit;
    }

    if (!$auth->isLoggedIn() && !$loginBypassEnabled) {
        flash(kc_t('manager.flash.login_required'), 'error');
        header('Location: ' . manager_login_url(), true, 303);
        exit;
    }

    if ($loginBypassEnabled && !$auth->isLoggedIn()) {
        $userId = '1';
        $email  = 'admin@kc-nalinnes.be';
        $user   = 'Bypass Temporaire';
        $adminEmails = [];
        $isAdmin = true;
    }
    else {
        $userId = (string)($auth->getUserId() ?? '');
        $email  = (string)($auth->getEmail() ?? '');
        $user   = (string)($auth->getUsername() ?? '');
        $adminEmails = get_configured_admin_emails($db, (string) env_value('ADMIN_EMAILS', ''));
        $isAdmin = is_admin_email($email, $adminEmails);
    }

    if (!$isAdmin) {
        flash(kc_t('manager.flash.member_redirect'), 'info');
        header('Location: ' . manager_member_dashboard_url(), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['action'] ?? ''), ['calendar_event_save', 'calendar_event_delete', 'calendar_event_toggle', 'calendar_event_duplicate', 'calendar_import_default_drafts', 'calendar_bulk'], true)) {
        require_manager_csrf();

        try {
            if (($_POST['action'] ?? '') === 'calendar_bulk') {
                $bulkAction = (string)($_POST['calendar_bulk_action'] ?? '');
                $eventIds = kc_calendar_normalize_event_ids($_POST['event_ids'] ?? []);

                if ($bulkAction === 'publish') {
                    $updated = kc_calendar_set_events_active($db, $eventIds, true);
                    flash($updated . ' evenement(s) calendrier publie(s).', 'success');
                }
                elseif ($bulkAction === 'unpublish') {
                    $updated = kc_calendar_set_events_active($db, $eventIds, false);
                    flash($updated . ' evenement(s) calendrier passe(s) en brouillon.', 'success');
                }
                elseif ($bulkAction === 'delete') {
                    $deleted = kc_calendar_delete_events($db, $eventIds);
                    flash($deleted . ' evenement(s) calendrier supprime(s).', 'success');
                }
                else {
                    throw new InvalidArgumentException('Action calendrier en masse invalide.');
                }
            }
            elseif (($_POST['action'] ?? '') === 'calendar_event_delete') {
                kc_calendar_delete_event($db, (int)($_POST['event_id'] ?? 0));
                flash('Evenement calendrier supprime.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'calendar_import_default_drafts') {
                $imported = kc_calendar_import_default_drafts($db);
                flash($imported . ' modeles calendrier importes en brouillons.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'calendar_event_toggle') {
                kc_calendar_set_event_active($db, (int)($_POST['event_id'] ?? 0), (string)($_POST['is_active'] ?? '0') === '1');
                flash('Statut calendrier mis a jour.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'calendar_event_duplicate') {
                kc_calendar_duplicate_event($db, (int)($_POST['event_id'] ?? 0));
                flash('Evenement calendrier duplique en brouillon inactif.', 'success');
            }
            else {
                kc_calendar_save_event($db, $_POST);
                flash('Evenement calendrier enregistre.', 'success');
            }
        }
        catch (Throwable $e) {
            flash($e->getMessage(), 'error');
        }

        header('Location: ' . manager_dashboard_anchor_url('admin-calendar'), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['action'] ?? ''), ['meal_reservation_status_update', 'meal_reservation_delete'], true)) {
        require_manager_csrf();

        try {
            if (($_POST['action'] ?? '') === 'meal_reservation_delete') {
                delete_meal_reservation($db, (int)($_POST['reservation_id'] ?? 0));
                flash('Reservation repas supprimee.', 'success');
            }
            else {
                update_meal_reservation_status($db, (int)($_POST['reservation_id'] ?? 0), (string)($_POST['status'] ?? ''));
                flash('Statut de reservation repas mis a jour.', 'success');
            }
        }
        catch (Throwable $e) {
            flash($e->getMessage(), 'error');
        }

        header('Location: ' . manager_dashboard_anchor_url('admin-meal-summary'), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'meal_settings_update') {
        require_manager_csrf();

        try {
            $mealSettings = save_meal_settings($db, $_POST);
            flash('Parametres du repas enregistres.', 'success');
        }
        catch (Throwable $e) {
            flash($e->getMessage(), 'error');
        }

        header('Location: ' . manager_dashboard_anchor_url('admin-meal'), true, 303);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'admin_meal_reservation') {
        require_manager_csrf();

        $postedSubmissionToken = (string)($_POST['meal_submission_token'] ?? '');
        $profileName = trim((string)($_POST['profile_name'] ?? ''));
        $contactEmail = trim((string)($_POST['contact_email'] ?? ''));
        $contactPhone = trim((string)($_POST['contact_phone'] ?? ''));
        $adultQty = max(0, (int)($_POST['repas_adulte'] ?? 0));
        $childQty = max(0, (int)($_POST['repas_enfant'] ?? 0));
        $notes = trim((string)($_POST['notes'] ?? ''));
        $sendCopy = !empty($_POST['send_copy']) && $_POST['send_copy'] === '1';

        $_SESSION['meal_admin_old'] = [
            'profile_name' => $profileName,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'adult_qty' => (string)$adultQty,
            'child_qty' => (string)$childQty,
            'notes' => $notes,
            'send_copy' => $sendCopy ? '1' : '0',
        ];

        try {
            if (!consume_meal_reservation_submission_token('admin_public', $postedSubmissionToken)) {
                throw new RuntimeException(kc_t('meal.flash.invalid_request'));
            }

            if ($profileName === '' || strlen($profileName) > 255) {
                throw new RuntimeException(kc_t('meal.flash.invalid_name'));
            }

            if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException(kc_t('meal.flash.invalid_email'));
            }

            if ($adultQty === 0 && $childQty === 0) {
                throw new RuntimeException(kc_t('meal.flash.no_meal'));
            }

            $total = compute_meal_total($adultQty, $childQty, (float)$mealSettings['adult_price'], (float)$mealSettings['child_price']);
            $reservationDate = date('Y-m-d H:i:s');
            $reservationId = save_public_meal_reservation($db, [
                'profile_type' => 'admin_public',
                'profile_name' => $profileName,
                'status' => 'confirmed',
                'contact_email' => $contactEmail,
                'contact_phone' => $contactPhone,
                'adult_qty' => $adultQty,
                'child_qty' => $childQty,
                'total_amount' => $total,
                'notes' => $notes,
            ]);

            append_meal_reservation_to_excel([
                'date' => $reservationDate,
                'member_user_id' => '0',
                'profile_name' => $profileName,
                'profile_type' => 'admin_public',
                'status' => 'confirmed',
                'contact_email' => $contactEmail,
                'contact_phone' => $contactPhone,
                'adult_qty' => (string)$adultQty,
                'child_qty' => (string)$childQty,
                'total_amount' => (string)$total,
                'notes' => $notes,
            ]);

            $to = (string)env_value('RESERVATION_EMAIL_TO', 'duchesnesakura@gmail.com');
            $message = kc_t('meal.mail.heading') . "\n"
                . kc_t('meal.mail.reservation_id') . ': ' . $reservationId . "\n"
                . kc_t('meal.mail.name') . ': ' . $profileName . "\n"
                . 'Email: ' . $contactEmail . "\n"
                . kc_t('meal.mail.phone') . ': ' . ($contactPhone !== '' ? $contactPhone : '-') . "\n"
                . kc_t('meal.mail.adults') . ': ' . $adultQty . "\n"
                . kc_t('meal.mail.children') . ': ' . $childQty . "\n"
                . kc_t('meal.mail.total') . ': ' . $total . " EUR\n"
                . kc_t('meal.mail.notes') . ': ' . ($notes !== '' ? $notes : '-') . "\n"
                . "Date: " . $reservationDate . "\n";
            send_meal_reservation_mail($to, kc_t('meal.mail.admin_subject'), $message, 'From: no-reply@kc-nalinnes.be');

            if ($sendCopy) {
                send_meal_reservation_mail($contactEmail, kc_t('meal.mail.copy_subject'), $message, 'From: no-reply@kc-nalinnes.be');
            }

            unset($_SESSION['meal_admin_old']);
            flash(kc_t('meal.flash.success'), 'success');
        }
        catch (Throwable $e) {
            flash($e->getMessage(), 'error');
        }

        header('Location: ' . manager_dashboard_anchor_url('admin-meal'), true, 303);
        exit;
    }

    // Gestion membres (admin)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['action'] ?? ''), ['member_create', 'member_profile_update', 'member_password_reset', 'member_grade_add', 'member_grade_delete', 'member_payment_update', 'member_dependent_add', 'member_dependent_update', 'member_dependent_delete'], true)) {
        require_manager_csrf();
        $memberPaymentYearForRedirect = manager_member_payment_year($_POST['payment_year'] ?? $_POST['period_year'] ?? date('Y'));
        $memberAdminRedirect = manager_dashboard_anchor_url_with_params('admin-users', ['payment_year' => $memberPaymentYearForRedirect]);

        try {
            $targetId = (int)($_POST['target_user_id'] ?? 0);

            if (($_POST['action'] ?? '') === 'member_create') {
                $createdMember = manager_admin_create_member($db, $auth, $_POST);
                flash('Membre cree: ' . (string)$createdMember['email'], 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_profile_update') {
                $existingMember = manager_admin_fetch_user($db, $targetId);
                if ($existingMember === null) {
                    throw new RuntimeException('Membre introuvable.');
                }

                $newProfile = manager_admin_normalize_member_profile_input($_POST);
                $oldEmail = normalize_email((string)($existingMember['email'] ?? ''));
                if ($targetId === (int)$userId && $oldEmail !== (string)$newProfile['email']) {
                    throw new RuntimeException('Vous ne pouvez pas modifier votre propre email depuis cette page.');
                }

                $updatedProfile = manager_admin_update_member_profile($db, $targetId, $_POST);
                if ((string)$updatedProfile['old_email'] !== (string)$updatedProfile['email'] && is_admin_email((string)$updatedProfile['old_email'], $adminEmails)) {
                    set_admin_role($db, (string)$updatedProfile['old_email'], false);
                    set_admin_role($db, (string)$updatedProfile['email'], true);
                }

                flash('Informations membre mises a jour.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_password_reset') {
                if ($targetId === (int)$userId) {
                    throw new RuntimeException('Vous ne pouvez pas reinitialiser votre propre mot de passe depuis cette page.');
                }

                manager_admin_reset_member_password($db, $auth, $targetId, $_POST['new_member_password'] ?? '');
                flash('Mot de passe membre mis a jour.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_grade_add') {
                manager_admin_assert_user_exists($db, $targetId);
                member_record_add_grade($db, $targetId, $_POST);
                manager_admin_save_grade($db, $targetId, manager_admin_normalize_member_grade($_POST['grade'] ?? ''));
                flash('Grade date ajoute.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_grade_delete') {
                manager_admin_assert_user_exists($db, $targetId);
                member_record_delete_grade($db, $targetId, (int)($_POST['grade_id'] ?? 0));
                flash('Grade date supprime.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_payment_update') {
                manager_admin_assert_user_exists($db, $targetId);
                member_record_save_payment($db, $targetId, $_POST);
                flash('Paiement membre mis a jour.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_dependent_add') {
                manager_admin_add_dependent($db, $targetId, $_POST);
                flash('Profil lie ajoute.', 'success');
            }
            elseif (($_POST['action'] ?? '') === 'member_dependent_update') {
                manager_admin_update_dependent($db, $targetId, (int)($_POST['dependent_id'] ?? 0), $_POST);
                flash('Profil lie mis a jour.', 'success');
            }
            else {
                manager_admin_delete_dependent($db, $targetId, (int)($_POST['dependent_id'] ?? 0));
                flash('Profil lie supprime.', 'success');
            }
        }
        catch (Throwable $e) {
            flash(manager_admin_auth_exception_message($e), 'error');
        }

        header('Location: ' . $memberAdminRedirect, true, 303);
        exit;
    }

    // Gestion roles utilisateurs (admin)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'user_update') {
        require_manager_csrf();
        $memberPaymentYearForRedirect = manager_member_payment_year($_POST['payment_year'] ?? date('Y'));
        $memberAdminRedirect = manager_dashboard_anchor_url_with_params('admin-users', ['payment_year' => $memberPaymentYearForRedirect]);

        $targetId = (int)($_POST['target_user_id'] ?? 0);
        $targetRole = (string)($_POST['target_role'] ?? 'member');

        if ($targetId <= 0 || !in_array($targetRole, ['admin', 'member'], true)) {
            flash(kc_t('manager.flash.invalid_user_params'), 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        $stmt = $db->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $targetId]);
        $targetEmail = (string)($stmt->fetchColumn() ?: '');

        if ($targetEmail === '') {
            flash(kc_t('manager.flash.user_not_found'), 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        set_admin_role($db, $targetEmail, $targetRole === 'admin');

        flash(kc_t('manager.flash.user_role_updated'), 'success');
        header('Location: ' . $memberAdminRedirect, true, 303);
        exit;
    }

    if (isset($_GET['download']) && $_GET['download'] === 'meal_reservations_xlsx') {
        if (!$auth->isLoggedIn()) {
            header('Location: ' . manager_login_url(), true, 303);
            exit;
        }

        $adminEmails = get_effective_admin_emails($db, (string) env_value('ADMIN_EMAILS', ''));
        if (!is_admin_email((string)($auth->getEmail() ?? ''), $adminEmails)) {
            header('Location: ' . manager_member_dashboard_url(), true, 303);
            exit;
        }

        $rowsStmt = $db->query('SELECT member_user_id, profile_name, profile_type, status, contact_email, contact_phone, adult_qty, child_qty, total_amount, notes, created_at FROM meal_reservations ORDER BY created_at DESC');
        $rows = $rowsStmt->fetchAll();

        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [(string)$r['created_at'], (string)$r['member_user_id'], (string)$r['profile_name'], (string)$r['profile_type'], (string)($r['status'] ?? 'confirmed'), (string)($r['contact_email'] ?? ''), (string)($r['contact_phone'] ?? ''), (string)$r['adult_qty'], (string)$r['child_qty'], (string)$r['total_amount'], (string)($r['notes'] ?? '')];
        }

        $useXlsx = class_exists('ZipArchive');
        $tmpBase = tempnam(sys_get_temp_dir(), 'reservations_excel_');
        if ($tmpBase === false) {
            throw new RuntimeException(kc_t('manager.error.temp_excel'));
        }
        $tmp = $tmpBase . ($useXlsx ? '.xlsx' : '.xls');
        rename($tmpBase, $tmp);

        write_meal_reservations_excel($tmp, $dataRows);

        header('Content-Type: ' . ($useXlsx ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/vnd.ms-excel; charset=UTF-8'));
        header('Content-Disposition: attachment; filename="reservations-repas.' . ($useXlsx ? 'xlsx' : 'xls') . '"');
        readfile($tmp);
        @unlink($tmp);
        exit;
    }

    if (isset($_GET['download']) && $_GET['download'] === 'member_mutuelle') {
        $targetId = (int)($_GET['target_user_id'] ?? 0);
        $paymentYear = manager_member_payment_year($_GET['year'] ?? date('Y'));
        $memberAdminRedirect = manager_dashboard_anchor_url_with_params('admin-users', ['payment_year' => $paymentYear]);
        $targetUser = manager_admin_fetch_user($db, $targetId);
        if ($targetUser === null) {
            flash('Membre introuvable.', 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        if (!member_record_annual_payment_is_paid($db, $targetId, $paymentYear)) {
            flash('La cotisation annuelle doit etre payee pour generer la mutuelle.', 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        $templatesDir = __DIR__ . '/../docs';
        $templateFiles = list_pdf_templates($templatesDir);
        $allowedTemplatesRaw = (string)env_value('ALLOWED_PRECOMPLETED_PDFS', 'mutualia-ac-sport-fr.pdf');
        $allowedTemplates = array_values(array_filter(array_map('trim', explode(',', $allowedTemplatesRaw))));
        if ($allowedTemplates !== []) {
            $templateFiles = array_values(array_filter($templateFiles, static fn(string $name): bool => in_array($name, $allowedTemplates, true)));
        }

        $requestedTemplate = basename((string)($_GET['template'] ?? 'mutualia-ac-sport-fr.pdf'));
        if (!is_allowed_template($requestedTemplate, $templateFiles)) {
            flash('Modele mutuelle non autorise.', 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        $templatePath = $templatesDir . '/' . $requestedTemplate;
        if (!is_file($templatePath)) {
            flash('Modele mutuelle introuvable.', 'error');
            header('Location: ' . $memberAdminRedirect, true, 303);
            exit;
        }

        $responsibleName = member_record_display_name($targetUser, member_record_profile($db, $targetId));
        $beneficiaryName = $responsibleName;
        $dependentId = (int)($_GET['dependent_id'] ?? 0);
        if ($dependentId > 0) {
            $dependentStmt = $db->prepare('SELECT full_name FROM member_dependents WHERE id = :id AND guardian_user_id = :guardian_user_id LIMIT 1');
            $dependentStmt->execute([':id' => $dependentId, ':guardian_user_id' => $targetId]);
            $dependentRow = $dependentStmt->fetch(PDO::FETCH_ASSOC);
            if (!is_array($dependentRow)) {
                flash('Profil lie introuvable.', 'error');
                header('Location: ' . $memberAdminRedirect, true, 303);
                exit;
            }

            $beneficiaryName = (string)$dependentRow['full_name'];
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . precompleted_mutuelle_filename($beneficiaryName) . '"');
        echo generate_precompleted_mutuelle_pdf($templatePath, $beneficiaryName, $responsibleName);
        exit;
    }

    $usersStmt = $db->query('SELECT id, email, username FROM users ORDER BY id ASC');
    $users = $usersStmt->fetchAll();
    $memberPaymentYear = manager_member_payment_year($_GET['payment_year'] ?? date('Y'));
    $memberProfilesByUserId = member_record_profiles_by_user_id($db);

    $dependentsStmt = $db->query('SELECT id, guardian_user_id, full_name, birthdate, is_minor FROM member_dependents ORDER BY guardian_user_id ASC, is_minor DESC, full_name ASC, id ASC');
    $dependentsRows = $dependentsStmt->fetchAll();
    $dependentsByUserId = [];
    foreach ($dependentsRows as $dependentRow) {
        $guardianId = (int)($dependentRow['guardian_user_id'] ?? 0);
        if ($guardianId <= 0) {
            continue;
        }

        if (!isset($dependentsByUserId[$guardianId])) {
            $dependentsByUserId[$guardianId] = [];
        }

        $dependentsByUserId[$guardianId][] = $dependentRow;
    }

    $mealSummaryStmt = $db->query('SELECT COALESCE(SUM(CASE WHEN status <> \'cancelled\' THEN adult_qty ELSE 0 END),0) AS total_adult, COALESCE(SUM(CASE WHEN status <> \'cancelled\' THEN child_qty ELSE 0 END),0) AS total_child, COALESCE(SUM(CASE WHEN status <> \'cancelled\' THEN total_amount ELSE 0 END),0) AS total_amount FROM meal_reservations');
    $mealSummary = $mealSummaryStmt->fetch() ?: ['total_adult' => 0, 'total_child' => 0, 'total_amount' => 0];

    $mealReservationsStmt = $db->query('SELECT id, member_user_id, profile_name, profile_type, status, contact_email, contact_phone, adult_qty, child_qty, total_amount, notes, created_at FROM meal_reservations ORDER BY created_at DESC');
    $mealReservations = $mealReservationsStmt->fetchAll();
    $mealStatuses = meal_reservation_statuses();
    $gradesStmt = $db->query('SELECT user_id, grade FROM member_grades');
    $gradesRows = $gradesStmt->fetchAll();
    $gradesByUserId = [];
    foreach ($gradesRows as $g) { $gradesByUserId[(int)$g['user_id']] = (string)$g['grade']; }
    $gradeHistoryByUserId = member_record_grade_history_by_user_id($db);
    $paymentsByUserId = member_record_payments_by_user_id($db, $memberPaymentYear);

    $mealStatsByUserId = [];
    foreach ($mealReservations as $reservationRow) {
        $reservationUserId = (int)($reservationRow['member_user_id'] ?? 0);
        if ($reservationUserId <= 0) {
            continue;
        }

        if (!isset($mealStatsByUserId[$reservationUserId])) {
            $mealStatsByUserId[$reservationUserId] = [
                'count' => 0,
                'active_count' => 0,
                'adult_qty' => 0,
                'child_qty' => 0,
                'total_amount' => 0.0,
                'last_at' => '',
                'last_profile' => '',
                'profiles' => [],
            ];
        }

        $profileName = trim((string)($reservationRow['profile_name'] ?? ''));
        if ($profileName !== '') {
            $mealStatsByUserId[$reservationUserId]['profiles'][$profileName] = true;
        }

        if ($mealStatsByUserId[$reservationUserId]['last_at'] === '') {
            $mealStatsByUserId[$reservationUserId]['last_at'] = (string)($reservationRow['created_at'] ?? '');
            $mealStatsByUserId[$reservationUserId]['last_profile'] = $profileName;
        }

        $mealStatsByUserId[$reservationUserId]['count']++;
        if ((string)($reservationRow['status'] ?? 'confirmed') !== 'cancelled') {
            $mealStatsByUserId[$reservationUserId]['active_count']++;
            $mealStatsByUserId[$reservationUserId]['adult_qty'] += (int)($reservationRow['adult_qty'] ?? 0);
            $mealStatsByUserId[$reservationUserId]['child_qty'] += (int)($reservationRow['child_qty'] ?? 0);
            $mealStatsByUserId[$reservationUserId]['total_amount'] += (float)($reservationRow['total_amount'] ?? 0);
        }
    }

    $memberAdminRows = [];
    $memberAdminSummary = [
        'total' => 0,
        'admins' => 0,
        'members' => 0,
        'minor_dependents' => 0,
        'adult_dependents' => 0,
        'missing_grades' => 0,
        'meal_profiles' => 0,
        'annual_paid' => 0,
    ];
    foreach ($users as $row) {
        $memberId = (int)($row['id'] ?? 0);
        $rowEmail = strtolower((string)($row['email'] ?? ''));
        $rowIsAdmin = in_array($rowEmail, $adminEmails, true);
        $rowProfile = $memberProfilesByUserId[$memberId] ?? ['first_name' => null, 'last_name' => null];
        $rowDisplayName = member_record_display_name($row, $rowProfile);
        $rowGradeHistory = $gradeHistoryByUserId[$memberId] ?? [];
        $rowGrade = trim((string)($gradesByUserId[$memberId] ?? ''));
        if ($rowGrade === '' && $rowGradeHistory !== []) {
            $rowGrade = (string)($rowGradeHistory[0]['grade'] ?? '');
        }
        $rowDependents = $dependentsByUserId[$memberId] ?? [];
        $rowPayments = $paymentsByUserId[$memberId] ?? ['annual' => null, 'monthly' => []];
        $minorDependentCount = 0;
        $adultDependentCount = 0;
        foreach ($rowDependents as $dependentRow) {
            if ((int)($dependentRow['is_minor'] ?? 1) === 1) {
                $minorDependentCount++;
            }
            else {
                $adultDependentCount++;
            }
        }

        $mealStats = $mealStatsByUserId[$memberId] ?? [
            'count' => 0,
            'active_count' => 0,
            'adult_qty' => 0,
            'child_qty' => 0,
            'total_amount' => 0.0,
            'last_at' => '',
            'last_profile' => '',
            'profiles' => [],
        ];
        $mealProfileCount = count($mealStats['profiles']);
        $memberSearchText = strtolower(trim(
            (string)($row['email'] ?? '') . ' '
            . (string)($row['username'] ?? '') . ' '
            . $rowDisplayName . ' '
            . $rowGrade . ' '
            . implode(' ', array_map(static fn(array $dependentRow): string => (string)($dependentRow['full_name'] ?? ''), $rowDependents)) . ' '
            . implode(' ', array_keys($mealStats['profiles']))
        ));

        $memberAdminRows[] = [
            'user' => $row,
            'profile' => $rowProfile,
            'display_name' => $rowDisplayName,
            'is_admin' => $rowIsAdmin,
            'grade' => $rowGrade,
            'grade_history' => $rowGradeHistory,
            'dependents' => $rowDependents,
            'minor_dependents' => $minorDependentCount,
            'adult_dependents' => $adultDependentCount,
            'payments' => $rowPayments,
            'meal_stats' => $mealStats,
            'meal_profiles' => $mealProfileCount,
            'search' => $memberSearchText,
        ];

        $memberAdminSummary['total']++;
        $memberAdminSummary[$rowIsAdmin ? 'admins' : 'members']++;
        $memberAdminSummary['minor_dependents'] += $minorDependentCount;
        $memberAdminSummary['adult_dependents'] += $adultDependentCount;
        $memberAdminSummary['meal_profiles'] += $mealProfileCount;
        if ((string)($rowPayments['annual']['status'] ?? '') === 'paid') {
            $memberAdminSummary['annual_paid']++;
        }
        if ($rowGrade === '') {
            $memberAdminSummary['missing_grades']++;
        }
    }

    $calendarRows = kc_calendar_admin_event_rows($db);
    $calendarPayload = kc_calendar_events_payload($calendarRows, true);
    $calendarAudiences = kc_calendar_audiences();
    $calendarEventTypes = kc_calendar_event_types();
    $calendarCounts = kc_calendar_admin_counts($calendarRows);
    $calendarConflicts = kc_calendar_admin_conflicts($calendarRows);
    $calendarCounts['conflicts'] = count($calendarConflicts);
    $calendarConflictIds = [];
    foreach ($calendarConflicts as $calendarConflict) {
        $calendarConflictIds[(string)$calendarConflict['first_id']] = true;
        $calendarConflictIds[(string)$calendarConflict['second_id']] = true;
    }
    $mealAdminOld = $_SESSION['meal_admin_old'] ?? [
        'profile_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'adult_qty' => '0',
        'child_qty' => '0',
        'notes' => '',
        'send_copy' => '1',
    ];
    unset($_SESSION['meal_admin_old']);
    $mealAdminSubmissionToken = meal_reservation_submission_token('admin_public');

} catch (\Throwable $e) {
    error_log('Manager dashboard error: ' . get_class($e) . ': ' . $e->getMessage());
    http_response_code(500);
    if (env_flag('APP_DEBUG', false)) {
        echo "<pre style='white-space:pre-wrap'>500 ERROR\n"
            . e($e->getMessage()) . "\n\n"
            . e($e->getFile()) . ":" . (int)$e->getLine()
            . "</pre>";
    }
    else {
        echo "<pre style='white-space:pre-wrap'>500 ERROR\nErreur interne.</pre>";
    }
    exit;
}
?>
<!doctype html>
<html<?= kc_translate_guard_attr($locale) ?> lang="<?= e($locale) ?>">
<head>
    <meta charset="utf-8" />
    <?= kc_google_notranslate_meta($locale) ?>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= e(kc_t('manager.meta.title')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/skeleton.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/theme.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/palette.css" rel="stylesheet">
    <script>
      window.kcFullCalendarReady = Promise.all([
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/+esm'),
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/daygrid/+esm'),
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/timegrid/+esm'),
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/list/+esm'),
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/interaction/+esm'),
        import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/+esm')
      ]).then(function (modules) {
        window.FullCalendar = {
          Calendar: modules[0].Calendar || modules[0].default,
          plugins: [
            modules[1].default,
            modules[2].default,
            modules[3].default,
            modules[4].default,
            modules[5].default
          ].filter(Boolean)
        };

        return window.FullCalendar;
      });
    </script>
    <style>
      body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;}
      section[id]{scroll-margin-top:5.5rem;}
      .kc-calendar-inactive{opacity:.45;filter:grayscale(.35);}
    </style>
</head>
<body class="bg-slate-950 text-slate-100">
<main class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight"><?= e(kc_t('manager.heading')) ?></h1>
            <p class="mt-1 text-slate-400"><?= e(kc_t('manager.subtitle')) ?></p>
        </div>

        <div class="flex items-center gap-3">
        <?= kc_language_switcher('flex items-center gap-2') ?>
        <form method="post" action="<?= e(manager_dashboard_url()) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="logout">
            <button class="rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 transition">
                <?= e(kc_t('manager.logout')) ?>
            </button>
        </form>
        </div>
    </div>

    <?php if (is_array($flashMsg) && !empty($flashMsg['message'])): ?>
        <div class="mt-6 rounded-xl border px-4 py-3 <?= e(flash_classes($flashMsg)) ?>">
            <?= e((string)$flashMsg['message']) ?>
        </div>
    <?php endif; ?>

    <nav aria-label="Navigation admin" class="sticky top-0 z-20 -mx-4 mt-6 border-y border-slate-800 bg-slate-950/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="flex gap-2 overflow-x-auto text-sm">
            <?php foreach (manager_admin_nav_items() as $anchor => $label): ?>
                <a href="#<?= e((string)$anchor) ?>" class="whitespace-nowrap rounded-lg border border-slate-700 px-3 py-2 font-semibold text-slate-200 hover:border-sky-500 hover:text-sky-200">
                    <?= e((string)$label) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <section id="admin-overview" class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Membres</p>
            <p class="mt-2 text-3xl font-extrabold"><?= e((string)count($users)) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Repas</p>
            <p class="mt-2 text-3xl font-extrabold"><?= e((string)((int)$mealSummary['total_adult'] + (int)$mealSummary['total_child'])) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Recette repas</p>
            <p class="mt-2 text-3xl font-extrabold"><?= e((string)$mealSummary['total_amount']) ?> EUR</p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Evenements publies</p>
            <p class="mt-2 text-3xl font-extrabold"><?= e((string)$calendarCounts['active']) ?></p>
        </div>
        <div class="rounded-2xl border <?= $calendarCounts['conflicts'] > 0 ? 'border-red-500/40 bg-red-500/10' : 'border-slate-800 bg-slate-900/60' ?> p-5">
            <p class="text-xs uppercase tracking-[0.18em] <?= $calendarCounts['conflicts'] > 0 ? 'text-red-300' : 'text-slate-500' ?>">Conflits</p>
            <p class="mt-2 text-3xl font-extrabold"><?= e((string)$calendarCounts['conflicts']) ?></p>
        </div>
    </section>

    <section class="mt-4 grid gap-6 lg:grid-cols-[1fr_2fr]">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-xl font-bold"><?= e(kc_t('manager.status.title')) ?></h2>
            <p class="mt-2 text-slate-300"><?= e(kc_t('manager.status.connected')) ?></p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-xl font-bold"><?= e(kc_t('manager.account.title')) ?></h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-400">User ID</dt><dd class="font-semibold"><?= e($userId) ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400">Email</dt><dd class="font-semibold"><?= e($email) ?></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400">Username</dt><dd class="font-semibold"><?= e($user !== '' ? $user : kc_t('manager.account.username_empty')) ?></dd></div>
            </dl>
        </div>
    </section>

    <section id="admin-meal" class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-300"><?= e(kc_t('meal.hero.kicker')) ?></p>
                <h2 class="mt-1 text-xl font-bold"><?= e(kc_t('meal.hero.title')) ?></h2>
                <p class="mt-2 text-sm text-slate-400"><?= e(kc_t('meal.hero.description')) ?></p>
            </div>
            <a href="<?= e(manager_dashboard_url()) ?>&download=meal_reservations_xlsx" class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                <?= e(kc_t('manager.meal.export')) ?>
            </a>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Repas</p>
                <p class="mt-1 font-semibold"><?= e(meal_datetime_label((string)$mealSettings['meal_at'])) ?></p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Limite</p>
                <p class="mt-1 font-semibold"><?= e(meal_datetime_label((string)$mealSettings['reservation_deadline_at'])) ?></p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Adulte</p>
                <p class="mt-1 font-semibold"><?= e(meal_price_label((float)$mealSettings['adult_price'])) ?> EUR</p>
                <p class="mt-1 text-xs text-slate-400"><?= e((string)$mealSettings['adult_menu']) ?></p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Enfant</p>
                <p class="mt-1 font-semibold"><?= e(meal_price_label((float)$mealSettings['child_price'])) ?> EUR</p>
                <p class="mt-1 text-xs text-slate-400"><?= e((string)$mealSettings['child_menu']) ?></p>
            </div>
        </div>

        <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-meal')) ?>" class="mt-5 grid gap-4 md:grid-cols-2" data-disable-on-submit>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="meal_submission_token" value="<?= e($mealAdminSubmissionToken) ?>">
            <input type="hidden" name="action" value="admin_meal_reservation">

            <div class="md:col-span-2">
                <label for="admin_profile_name" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.name')) ?></label>
                <input id="admin_profile_name" name="profile_name" required maxlength="255" value="<?= e((string)$mealAdminOld['profile_name']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="name">
            </div>

            <div>
                <label for="admin_contact_email" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.email')) ?></label>
                <input id="admin_contact_email" type="email" name="contact_email" required value="<?= e((string)$mealAdminOld['contact_email']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="email">
            </div>

            <div>
                <label for="admin_contact_phone" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.phone')) ?></label>
                <input id="admin_contact_phone" name="contact_phone" value="<?= e((string)$mealAdminOld['contact_phone']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="tel">
            </div>

            <div>
                <label for="admin_repas_adulte" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.adults')) ?> - <?= e(meal_price_label((float)$mealSettings['adult_price'])) ?> EUR</label>
                <input id="admin_repas_adulte" type="number" min="0" name="repas_adulte" value="<?= e((string)$mealAdminOld['adult_qty']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
            </div>

            <div>
                <label for="admin_repas_enfant" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.children')) ?> - <?= e(meal_price_label((float)$mealSettings['child_price'])) ?> EUR</label>
                <input id="admin_repas_enfant" type="number" min="0" name="repas_enfant" value="<?= e((string)$mealAdminOld['child_qty']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
            </div>

            <div class="md:col-span-2">
                <label for="admin_meal_notes" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.notes')) ?></label>
                <textarea id="admin_meal_notes" name="notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100"><?= e((string)$mealAdminOld['notes']) ?></textarea>
            </div>

            <div class="md:col-span-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="send_copy" value="1" <?= ((string)$mealAdminOld['send_copy'] === '1') ? 'checked' : '' ?>>
                    <?= e(kc_t('meal.form.copy')) ?>
                </label>
                <button class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/40 hover:bg-red-500 transition disabled:cursor-not-allowed disabled:opacity-70">
                    <?= e(kc_t('meal.form.submit')) ?>
                </button>
            </div>
        </form>

        <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-meal')) ?>" class="mt-6 rounded-xl border border-slate-800 bg-slate-950/40 p-4" data-disable-on-submit>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="meal_settings_update">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="font-semibold">Parametres du repas</h3>
                    <p class="mt-1 text-sm text-slate-400">Menus, prix, date du repas et limite de reservation utilises par les formulaires public, membre et admin.</p>
                </div>
                <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-70">Enregistrer</button>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="meal_adult_menu" class="block text-sm font-semibold text-slate-200">Menu adulte</label>
                    <textarea id="meal_adult_menu" name="adult_menu" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100"><?= e((string)$mealSettings['adult_menu']) ?></textarea>
                </div>
                <div>
                    <label for="meal_child_menu" class="block text-sm font-semibold text-slate-200">Menu enfant</label>
                    <textarea id="meal_child_menu" name="child_menu" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100"><?= e((string)$mealSettings['child_menu']) ?></textarea>
                </div>
                <div>
                    <label for="meal_adult_price" class="block text-sm font-semibold text-slate-200">Prix adulte (EUR)</label>
                    <input id="meal_adult_price" name="adult_price" type="number" min="0" step="0.01" value="<?= e((string)$mealSettings['adult_price']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
                </div>
                <div>
                    <label for="meal_child_price" class="block text-sm font-semibold text-slate-200">Prix enfant (EUR)</label>
                    <input id="meal_child_price" name="child_price" type="number" min="0" step="0.01" value="<?= e((string)$mealSettings['child_price']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
                </div>
                <div>
                    <label for="meal_at" class="block text-sm font-semibold text-slate-200">Date et heure du repas</label>
                    <input id="meal_at" name="meal_at" type="datetime-local" value="<?= e(meal_datetime_input_value((string)$mealSettings['meal_at'])) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
                </div>
                <div>
                    <label for="meal_reservation_deadline_at" class="block text-sm font-semibold text-slate-200">Date limite de reservation</label>
                    <input id="meal_reservation_deadline_at" name="reservation_deadline_at" type="datetime-local" value="<?= e(meal_datetime_input_value((string)$mealSettings['reservation_deadline_at'])) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
                </div>
            </div>
        </form>
    </section>

    <section id="admin-users" class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-bold"><?= e(kc_t('manager.users.title')) ?></h2>
                <p class="mt-2 text-sm text-slate-400">Suivi centralise des comptes, noms, grades dates, paiements, enfants rattaches et reservations repas membres.</p>
            </div>
            <div class="text-sm text-slate-400"><span id="memberVisibleCount"><?= e((string)$memberAdminSummary['total']) ?></span> / <?= e((string)$memberAdminSummary['total']) ?> comptes visibles</div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4"><p class="text-xs uppercase tracking-[0.18em] text-slate-500">Comptes</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['total']) ?></p></div>
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4"><p class="text-xs uppercase tracking-[0.18em] text-emerald-300">Membres</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['members']) ?></p></div>
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 p-4"><p class="text-xs uppercase tracking-[0.18em] text-sky-300">Admins</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['admins']) ?></p></div>
            <div class="rounded-xl border border-orange-500/30 bg-orange-500/10 p-4"><p class="text-xs uppercase tracking-[0.18em] text-orange-300">Enfants mineurs</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['minor_dependents']) ?></p></div>
            <div class="rounded-xl border <?= $memberAdminSummary['missing_grades'] > 0 ? 'border-red-500/40 bg-red-500/10' : 'border-slate-800 bg-slate-950/40' ?> p-4"><p class="text-xs uppercase tracking-[0.18em] <?= $memberAdminSummary['missing_grades'] > 0 ? 'text-red-300' : 'text-slate-500' ?>">Grades manquants</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['missing_grades']) ?></p></div>
            <div class="rounded-xl border border-violet-500/30 bg-violet-500/10 p-4"><p class="text-xs uppercase tracking-[0.18em] text-violet-300">Annees payees</p><p class="mt-1 text-2xl font-bold"><?= e((string)$memberAdminSummary['annual_paid']) ?></p></div>
        </div>

        <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="mt-5 grid gap-3 rounded-xl border border-slate-800 bg-slate-950/40 p-4 lg:grid-cols-[1.2fr_0.9fr_0.9fr_1fr_0.8fr_0.8fr_auto]" data-disable-on-submit>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="member_create">
            <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
            <div>
                <label for="new_member_email" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Email</label>
                <input id="new_member_email" name="new_member_email" type="email" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_first_name" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Prenom</label>
                <input id="new_member_first_name" name="new_member_first_name" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_last_name" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Nom</label>
                <input id="new_member_last_name" name="new_member_last_name" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_username" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Nom affiche</label>
                <input id="new_member_username" name="new_member_username" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_password" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Mot de passe</label>
                <input id="new_member_password" name="new_member_password" type="password" minlength="8" required autocomplete="new-password" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_grade" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Grade</label>
                <input id="new_member_grade" name="new_member_grade" maxlength="100" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
            </div>
            <div>
                <label for="new_member_role" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Role</label>
                <select id="new_member_role" name="new_member_role" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100">
                    <option value="member">Membre</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-70">Creer</button>
            </div>
        </form>

        <div class="mt-5 grid gap-3 lg:grid-cols-[1fr_auto_auto]">
            <div>
                <label for="memberSearch" class="sr-only">Rechercher un membre</label>
                <input id="memberSearch" type="search" placeholder="Rechercher par email, nom, prenom, grade, enfant ou profil repas" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">
            </div>
            <form method="get" action="/manager/dashboard.php#admin-users" class="flex flex-wrap items-end gap-2">
                <input type="hidden" name="lang" value="<?= e(kc_current_locale()) ?>">
                <div>
                    <label for="memberPaymentYear" class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Annee paiements</label>
                    <input id="memberPaymentYear" name="payment_year" type="number" min="2000" max="2100" value="<?= e((string)$memberPaymentYear) ?>" class="mt-1 w-28 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100">
                </div>
                <button class="rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-600">Voir</button>
            </form>
            <div class="flex flex-wrap gap-2">
                <label for="memberRoleFilter" class="sr-only">Filtrer par role</label>
                <select id="memberRoleFilter" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100">
                    <option value="all">Tous les roles</option>
                    <option value="member">Membres</option>
                    <option value="admin">Admins</option>
                </select>
                <label for="memberGradeFilter" class="sr-only">Filtrer par grade</label>
                <select id="memberGradeFilter" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100">
                    <option value="all">Tous les grades</option>
                    <option value="missing">Grade manquant</option>
                    <option value="filled">Grade renseigne</option>
                </select>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-800">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/70">
                <tr class="text-left text-slate-400">
                    <th class="px-3 py-2"><?= e(kc_t('manager.users.id')) ?></th>
                    <th class="px-3 py-2">Membre</th>
                    <th class="px-3 py-2">Grade</th>
                    <th class="px-3 py-2">Paiements</th>
                    <th class="px-3 py-2">Profils lies</th>
                    <th class="px-3 py-2">Repas membre</th>
                    <th class="px-3 py-2">Role</th>
                    <th class="px-3 py-2">Actions</th>
                </tr>
                </thead>
                <tbody id="memberRows">
                <?php foreach ($memberAdminRows as $memberRow): ?>
                    <?php
                    $row = $memberRow['user'];
                    $rowId = (int)($row['id'] ?? 0);
                    $rowIsAdmin = (bool)$memberRow['is_admin'];
                    $rowIsCurrentUser = $rowId === (int)$userId;
                    $rowProfile = $memberRow['profile'];
                    $rowGrade = (string)$memberRow['grade'];
                    $rowGradeHistory = $memberRow['grade_history'];
                    $rowPayments = $memberRow['payments'];
                    $annualPayment = is_array($rowPayments['annual'] ?? null) ? $rowPayments['annual'] : null;
                    $monthlyPayments = is_array($rowPayments['monthly'] ?? null) ? $rowPayments['monthly'] : [];
                    $rowDependents = $memberRow['dependents'];
                    $mealStats = $memberRow['meal_stats'];
                    ?>
                    <tr data-member-row data-role="<?= $rowIsAdmin ? 'admin' : 'member' ?>" data-grade="<?= $rowGrade === '' ? 'missing' : 'filled' ?>" data-search="<?= e((string)$memberRow['search']) ?>" class="border-t border-slate-800 align-top">
                        <td class="px-3 py-3 text-slate-400"><?= e((string)$rowId) ?></td>
                        <td class="px-3 py-3">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="grid min-w-[16rem] gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="member_profile_update">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <label class="sr-only" for="member_email_<?= e((string)$rowId) ?>">Email membre</label>
                                <input id="member_email_<?= e((string)$rowId) ?>" name="target_email" type="email" value="<?= e((string)($row['email'] ?? '')) ?>" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-sm font-semibold text-slate-100">
                                <label class="sr-only" for="member_username_<?= e((string)$rowId) ?>">Nom membre</label>
                                <input id="member_username_<?= e((string)$rowId) ?>" name="target_username" maxlength="100" value="<?= e((string)($row['username'] ?? '')) ?>" placeholder="Nom affiche" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100 placeholder:text-slate-500">
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <label class="sr-only" for="member_first_name_<?= e((string)$rowId) ?>">Prenom</label>
                                    <input id="member_first_name_<?= e((string)$rowId) ?>" name="target_first_name" maxlength="100" value="<?= e((string)($rowProfile['first_name'] ?? '')) ?>" placeholder="Prenom" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100 placeholder:text-slate-500">
                                    <label class="sr-only" for="member_last_name_<?= e((string)$rowId) ?>">Nom</label>
                                    <input id="member_last_name_<?= e((string)$rowId) ?>" name="target_last_name" maxlength="100" value="<?= e((string)($rowProfile['last_name'] ?? '')) ?>" placeholder="Nom" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100 placeholder:text-slate-500">
                                </div>
                                <?php if ((string)$memberRow['display_name'] !== ''): ?><p class="text-xs text-slate-400"><?= e((string)$memberRow['display_name']) ?></p><?php endif; ?>
                                <button class="justify-self-start rounded-lg bg-sky-600 px-2 py-1 text-xs font-semibold text-white hover:bg-sky-500">Sauver</button>
                            </form>
                        </td>
                        <td class="px-3 py-3">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="grid min-w-[13rem] gap-2 rounded-lg border border-slate-800 bg-slate-950/40 p-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="member_grade_add">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <p class="text-xs font-semibold text-slate-300">Actuel: <?= e($rowGrade !== '' ? $rowGrade : 'A definir') ?></p>
                                <input name="grade" value="<?= e($rowGrade) ?>" placeholder="Grade" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                <input name="obtained_at" type="date" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                <button class="justify-self-start rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-500">Ajouter</button>
                            </form>
                            <?php if ($rowGradeHistory !== []): ?>
                                <ul class="mt-2 space-y-1 text-xs text-slate-300">
                                    <?php foreach ($rowGradeHistory as $gradeRow): ?>
                                        <li class="flex items-center justify-between gap-2">
                                            <span><?= e((string)$gradeRow['grade']) ?> - <?= e((string)$gradeRow['obtained_at']) ?></span>
                                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" onsubmit="return confirm('Supprimer ce grade date ?');">
                                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="action" value="member_grade_delete">
                                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                                <input type="hidden" name="grade_id" value="<?= e((string)$gradeRow['id']) ?>">
                                                <button class="rounded bg-red-600 px-1.5 py-0.5 text-[11px] font-semibold text-white hover:bg-red-500">X</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="grid min-w-[15rem] gap-2 rounded-lg border border-slate-800 bg-slate-950/40 p-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="member_payment_update">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="period_type" value="annual">
                                <input type="hidden" name="period_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <p class="text-xs font-semibold text-slate-300">Annee <?= e((string)$memberPaymentYear) ?>: <?= e(member_record_payment_status_label(is_array($annualPayment) ? (string)$annualPayment['status'] : null)) ?></p>
                                <select name="payment_status" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                    <?php foreach (['unpaid' => 'Non paye', 'pending' => 'A verifier', 'paid' => 'Paye'] as $paymentStatus => $paymentLabel): ?>
                                        <option value="<?= e($paymentStatus) ?>" <?= (string)($annualPayment['status'] ?? 'unpaid') === $paymentStatus ? 'selected' : '' ?>><?= e($paymentLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input name="paid_at" type="date" value="<?= e((string)($annualPayment['paid_at'] ?? '')) ?>" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                <button class="justify-self-start rounded-lg bg-violet-600 px-2 py-1 text-xs font-semibold text-white hover:bg-violet-500">Sauver annee</button>
                            </form>
                            <details class="mt-2">
                                <summary class="cursor-pointer text-xs font-semibold text-sky-200">Paiements mensuels</summary>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <?php for ($month = 1; $month <= 12; $month++): ?>
                                        <?php $monthPayment = is_array($monthlyPayments[$month] ?? null) ? $monthlyPayments[$month] : null; ?>
                                        <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="grid gap-1 rounded-lg border border-slate-800 bg-slate-950/40 p-2">
                                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="action" value="member_payment_update">
                                            <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                            <input type="hidden" name="period_type" value="monthly">
                                            <input type="hidden" name="period_year" value="<?= e((string)$memberPaymentYear) ?>">
                                            <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                            <input type="hidden" name="period_month" value="<?= e((string)$month) ?>">
                                            <span class="text-[11px] font-semibold text-slate-400"><?= e(str_pad((string)$month, 2, '0', STR_PAD_LEFT)) ?></span>
                                            <select name="payment_status" class="rounded border border-slate-700 bg-slate-800 px-1 py-0.5 text-[11px] text-slate-100">
                                                <?php foreach (['unpaid' => 'Non', 'pending' => 'Verif', 'paid' => 'Paye'] as $paymentStatus => $paymentLabel): ?>
                                                    <option value="<?= e($paymentStatus) ?>" <?= (string)($monthPayment['status'] ?? 'unpaid') === $paymentStatus ? 'selected' : '' ?>><?= e($paymentLabel) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="rounded bg-slate-700 px-1 py-0.5 text-[11px] font-semibold text-white hover:bg-slate-600">OK</button>
                                        </form>
                                    <?php endfor; ?>
                                </div>
                            </details>
                            <?php if ((string)($annualPayment['status'] ?? '') === 'paid'): ?>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <a class="inline-flex rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-500" href="<?= e(manager_dashboard_url_with_params(['download' => 'member_mutuelle', 'target_user_id' => $rowId, 'year' => $memberPaymentYear])) ?>">Mutuelle membre</a>
                                    <?php foreach ($rowDependents as $dependentRow): ?>
                                        <?php $dependentIdForMutuelle = (int)($dependentRow['id'] ?? 0); ?>
                                        <?php if ($dependentIdForMutuelle > 0): ?>
                                            <a class="inline-flex rounded-lg bg-sky-600 px-2 py-1 text-xs font-semibold text-white hover:bg-sky-500" href="<?= e(manager_dashboard_url_with_params(['download' => 'member_mutuelle', 'target_user_id' => $rowId, 'dependent_id' => $dependentIdForMutuelle, 'year' => $memberPaymentYear])) ?>">Mutuelle <?= e((string)($dependentRow['full_name'] ?? 'profil')) ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <p class="font-semibold"><?= e((string)((int)$memberRow['minor_dependents'] + (int)$memberRow['adult_dependents'])) ?> profil(s)</p>
                            <p class="mt-1 text-xs text-slate-400"><?= e((string)$memberRow['minor_dependents']) ?> mineur(s), <?= e((string)$memberRow['adult_dependents']) ?> adulte(s)</p>
                            <?php if ($rowDependents !== []): ?>
                                <details class="mt-2" open>
                                    <summary class="cursor-pointer text-xs font-semibold text-sky-200">Gerer les profils</summary>
                                    <div class="mt-2 space-y-2">
                                        <?php foreach ($rowDependents as $dependentRow): ?>
                                            <?php $dependentId = (int)($dependentRow['id'] ?? 0); ?>
                                            <div class="rounded-lg border border-slate-800 bg-slate-950/50 p-2">
                                                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="grid gap-2">
                                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="action" value="member_dependent_update">
                                                    <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                                    <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                                    <input type="hidden" name="dependent_id" value="<?= e((string)$dependentId) ?>">
                                                    <label class="sr-only" for="dependent_name_<?= e((string)$dependentId) ?>">Nom du profil lie</label>
                                                    <input id="dependent_name_<?= e((string)$dependentId) ?>" name="dependent_name" value="<?= e((string)($dependentRow['full_name'] ?? '')) ?>" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                                    <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                                        <label class="sr-only" for="dependent_birthdate_<?= e((string)$dependentId) ?>">Date de naissance</label>
                                                        <input id="dependent_birthdate_<?= e((string)$dependentId) ?>" name="dependent_birthdate" type="date" value="<?= e((string)($dependentRow['birthdate'] ?? '')) ?>" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                                        <label class="sr-only" for="dependent_is_minor_<?= e((string)$dependentId) ?>">Type de profil</label>
                                                        <select id="dependent_is_minor_<?= e((string)$dependentId) ?>" name="dependent_is_minor" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                                            <option value="1" <?= (int)($dependentRow['is_minor'] ?? 1) === 1 ? 'selected' : '' ?>>Mineur</option>
                                                            <option value="0" <?= (int)($dependentRow['is_minor'] ?? 1) === 0 ? 'selected' : '' ?>>Adulte</option>
                                                        </select>
                                                    </div>
                                                    <div class="flex flex-wrap gap-2">
                                                        <button class="rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-500">Sauver</button>
                                                    </div>
                                                </form>
                                                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="mt-2" onsubmit="return confirm('Supprimer ce profil lie ?');">
                                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="action" value="member_dependent_delete">
                                                    <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                                    <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                                    <input type="hidden" name="dependent_id" value="<?= e((string)$dependentId) ?>">
                                                    <button class="rounded-lg bg-red-600 px-2 py-1 text-xs font-semibold text-white hover:bg-red-500">Supprimer</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="mt-3 grid min-w-[15rem] gap-2 rounded-lg border border-slate-800 bg-slate-950/40 p-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="member_dependent_add">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <p class="text-xs font-semibold text-slate-300">Ajouter un profil</p>
                                <label class="sr-only" for="dependent_add_name_<?= e((string)$rowId) ?>">Nom du profil lie</label>
                                <input id="dependent_add_name_<?= e((string)$rowId) ?>" name="dependent_name" placeholder="Nom enfant/adulte" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100 placeholder:text-slate-500">
                                <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                    <label class="sr-only" for="dependent_add_birthdate_<?= e((string)$rowId) ?>">Date de naissance</label>
                                    <input id="dependent_add_birthdate_<?= e((string)$rowId) ?>" name="dependent_birthdate" type="date" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                    <label class="sr-only" for="dependent_add_is_minor_<?= e((string)$rowId) ?>">Type de profil</label>
                                    <select id="dependent_add_is_minor_<?= e((string)$rowId) ?>" name="dependent_is_minor" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                        <option value="1">Mineur</option>
                                        <option value="0">Adulte</option>
                                    </select>
                                </div>
                                <button class="justify-self-start rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-500">Ajouter</button>
                            </form>
                        </td>
                        <td class="px-3 py-3">
                            <p class="font-semibold"><?= e((string)$mealStats['active_count']) ?> active(s) / <?= e((string)$mealStats['count']) ?> total</p>
                            <p class="mt-1 text-xs text-slate-400"><?= e((string)$mealStats['adult_qty']) ?> adulte(s), <?= e((string)$mealStats['child_qty']) ?> enfant(s), <?= e(number_format((float)$mealStats['total_amount'], 2, ',', ' ')) ?> EUR</p>
                            <?php if ((string)$mealStats['last_at'] !== ''): ?>
                                <p class="mt-1 text-xs text-slate-500">Derniere: <?= e((string)$mealStats['last_at']) ?><?= (string)$mealStats['last_profile'] !== '' ? ' - ' . e((string)$mealStats['last_profile']) : '' ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $rowIsAdmin ? 'bg-sky-500/15 text-sky-200' : 'bg-emerald-500/15 text-emerald-200' ?>"><?= e($rowIsAdmin ? kc_t('manager.users.admin') : kc_t('manager.users.member')) ?></span>
                        </td>
                        <td class="px-3 py-3">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="flex min-w-[12rem] items-center gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="user_update">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <select name="target_role" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-slate-100">
                                    <option value="member" <?= !$rowIsAdmin ? 'selected' : '' ?>><?= e(kc_t('manager.users.member')) ?></option>
                                    <option value="admin" <?= $rowIsAdmin ? 'selected' : '' ?>><?= e(kc_t('manager.users.admin')) ?></option>
                                </select>
                                <button class="rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-500"><?= e(kc_t('manager.users.save')) ?></button>
                            </form>
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-users')) ?>" class="mt-3 grid min-w-[12rem] gap-2" data-disable-on-submit>
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="member_password_reset">
                                <input type="hidden" name="target_user_id" value="<?= e((string)$rowId) ?>">
                                <input type="hidden" name="payment_year" value="<?= e((string)$memberPaymentYear) ?>">
                                <label class="sr-only" for="member_password_<?= e((string)$rowId) ?>">Nouveau mot de passe</label>
                                <input id="member_password_<?= e((string)$rowId) ?>" name="new_member_password" type="password" minlength="8" required autocomplete="new-password" placeholder="Nouveau mot de passe" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100 placeholder:text-slate-500" <?= $rowIsCurrentUser ? 'disabled' : '' ?>>
                                <button class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-60" <?= $rowIsCurrentUser ? 'disabled' : '' ?>>Reset acces</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($memberAdminRows === []): ?>
                    <tr><td colspan="8" class="px-3 py-4 text-slate-400">Aucun membre.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>


    <section id="admin-meal-summary" class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex items-center justify-between gap-3"><h2 class="text-xl font-bold"><?= e(kc_t('manager.meal.title')) ?></h2><a href="<?= e(manager_dashboard_url()) ?>&download=meal_reservations_xlsx" class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500"><?= e(kc_t('manager.meal.export')) ?></a></div>
        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-slate-800 p-4"><p class="text-slate-400 text-sm"><?= e(kc_t('manager.meal.adult_count')) ?></p><p class="text-2xl font-bold"><?= e((string)$mealSummary['total_adult']) ?></p></div>
            <div class="rounded-xl border border-slate-800 p-4"><p class="text-slate-400 text-sm"><?= e(kc_t('manager.meal.child_count')) ?></p><p class="text-2xl font-bold"><?= e((string)$mealSummary['total_child']) ?></p></div>
            <div class="rounded-xl border border-slate-800 p-4"><p class="text-slate-400 text-sm"><?= e(kc_t('manager.meal.total_amount')) ?></p><p class="text-2xl font-bold"><?= e((string)$mealSummary['total_amount']) ?> EUR</p></div>
        </div>

        <h3 class="mt-6 text-lg font-semibold"><?= e(kc_t('manager.meal.by_members')) ?></h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="text-left text-slate-400 border-b border-slate-800">
                    <th class="py-2 pr-4">Date</th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.member_id')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.profile')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.type')) ?></th><th class="py-2 pr-4">Statut</th><th class="py-2 pr-4">Email</th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.phone')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.adults')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.children')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.total')) ?></th><th class="py-2 pr-4"><?= e(kc_t('manager.meal.note')) ?></th><th class="py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mealReservations as $r): ?>
                    <?php $reservationStatus = array_key_exists((string)($r['status'] ?? ''), $mealStatuses) ? (string)$r['status'] : 'confirmed'; ?>
                    <tr class="border-b border-slate-800/60">
                        <td class="py-2 pr-4"><?= e((string)$r['created_at']) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['member_user_id']) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['profile_name']) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['profile_type']) ?></td>
                        <td class="py-2 pr-4">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-meal-summary')) ?>" class="flex min-w-[12rem] items-center gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="meal_reservation_status_update">
                                <input type="hidden" name="reservation_id" value="<?= e((string)$r['id']) ?>">
                                <select name="status" class="rounded-lg border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-100">
                                    <?php foreach ($mealStatuses as $statusKey => $statusLabel): ?>
                                        <option value="<?= e((string)$statusKey) ?>" <?= $reservationStatus === (string)$statusKey ? 'selected' : '' ?>><?= e((string)$statusLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="rounded-lg bg-sky-600 px-2 py-1 text-xs font-semibold text-white hover:bg-sky-500">OK</button>
                            </form>
                        </td>
                        <td class="py-2 pr-4"><?= e((string)($r['contact_email'] ?? '')) ?></td>
                        <td class="py-2 pr-4"><?= e((string)($r['contact_phone'] ?? '')) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['adult_qty']) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['child_qty']) ?></td>
                        <td class="py-2 pr-4"><?= e((string)$r['total_amount']) ?> EUR</td>
                        <td class="py-2 pr-4"><?= e((string)($r['notes'] ?? '')) ?></td>
                        <td class="py-2">
                            <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-meal-summary')) ?>" onsubmit="return confirm('Supprimer cette reservation repas ?');">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="meal_reservation_delete">
                                <input type="hidden" name="reservation_id" value="<?= e((string)$r['id']) ?>">
                                <button class="rounded-lg border border-red-600 px-2 py-1 text-xs font-semibold text-red-100 hover:bg-red-950">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($mealReservations === []): ?>
                    <tr><td colspan="12" class="py-3 text-slate-400"><?= e(kc_t('manager.meal.none')) ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="admin-calendar" class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/60 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-xl font-bold"><?= e(kc_t('manager.calendar.title')) ?></h2>
                <p class="mt-2 text-sm text-slate-400">Gerez les calendriers enfants, ados et adultes avec des evenements ponctuels ou repetes. Les evenements inactifs restent visibles ici mais ne sont pas publies.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>" onsubmit="return confirm('Importer les modeles par defaut en brouillons ?');">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="calendar_import_default_drafts">
                    <button class="rounded-lg border border-slate-600 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-800">Importer modeles</button>
                </form>
                <button id="btnNewEvent" class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500"><?= e(kc_t('manager.calendar.new')) ?></button>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Total</p>
                <p class="mt-1 text-2xl font-bold"><?= e((string)$calendarCounts['total']) ?></p>
            </div>
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-emerald-300">Publies</p>
                <p class="mt-1 text-2xl font-bold"><?= e((string)$calendarCounts['active']) ?></p>
            </div>
            <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Brouillons</p>
                <p class="mt-1 text-2xl font-bold"><?= e((string)$calendarCounts['inactive']) ?></p>
            </div>
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 p-4">
                <p class="text-xs uppercase tracking-[0.18em] text-sky-300">Repetes</p>
                <p class="mt-1 text-2xl font-bold"><?= e((string)$calendarCounts['recurring']) ?></p>
            </div>
            <div class="rounded-xl border <?= $calendarCounts['conflicts'] > 0 ? 'border-red-500/40 bg-red-500/10' : 'border-slate-800 bg-slate-950/40' ?> p-4">
                <p class="text-xs uppercase tracking-[0.18em] <?= $calendarCounts['conflicts'] > 0 ? 'text-red-300' : 'text-slate-500' ?>">Conflits</p>
                <p class="mt-1 text-2xl font-bold"><?= e((string)$calendarCounts['conflicts']) ?></p>
            </div>
        </div>

        <?php if ($calendarConflicts !== []): ?>
            <div class="mt-5 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-100">
                <p class="font-semibold">Conflits horaires detectes</p>
                <div class="mt-3 space-y-2">
                    <?php foreach (array_slice($calendarConflicts, 0, 8) as $conflict): ?>
                        <div class="rounded-lg border border-red-500/20 bg-red-950/30 px-3 py-2">
                            <span class="font-semibold"><?= e($calendarAudiences[(string)$conflict['audience']] ?? (string)$conflict['audience']) ?></span>
                            <span class="text-red-200"> - <?= e((string)$conflict['start']) ?></span>
                            <span class="text-red-100"> : <?= e((string)$conflict['first_title']) ?> / <?= e((string)$conflict['second_title']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($calendarConflicts) > 8): ?>
                        <p class="text-red-200">Seuls les 8 premiers conflits sont affiches.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div id="calendarAudienceFilters" class="mt-5 flex flex-wrap gap-2 text-sm">
            <button type="button" data-filter="club" class="calendar-filter rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 font-semibold text-slate-100">Tout</button>
            <button type="button" data-filter="children" class="calendar-filter rounded-lg border border-blue-400/60 px-3 py-2 font-semibold text-blue-100">Enfants</button>
            <button type="button" data-filter="teens" class="calendar-filter rounded-lg border border-orange-400/60 px-3 py-2 font-semibold text-orange-100">Ados</button>
            <button type="button" data-filter="adults" class="calendar-filter rounded-lg border border-emerald-400/60 px-3 py-2 font-semibold text-emerald-100">Adultes</button>
        </div>

        <div class="mt-4">
            <label for="calendarSearch" class="sr-only">Rechercher un evenement</label>
            <input id="calendarSearch" type="search" placeholder="Rechercher par titre, calendrier, type ou date" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">
        </div>

        <div class="mt-6 rounded-xl border border-slate-800 bg-slate-950/50 p-2">
            <div id="adminCalendar" class="min-h-[560px]"></div>
        </div>

        <form id="calendarBulkForm" method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>" class="mt-6 flex flex-wrap items-center gap-2" data-calendar-bulk-form>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="calendar_bulk">
            <label for="calendarBulkAction" class="sr-only">Action en masse</label>
            <select id="calendarBulkAction" name="calendar_bulk_action" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100">
                <option value="publish">Publier</option>
                <option value="unpublish">Passer en brouillon</option>
                <option value="delete">Supprimer</option>
            </select>
            <button type="submit" class="rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-500 disabled:cursor-not-allowed disabled:opacity-50" disabled>Appliquer</button>
            <span id="calendarBulkCount" class="text-sm text-slate-400">0 selection</span>
        </form>

        <div class="mt-3 overflow-x-auto rounded-xl border border-slate-800">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/70 text-left text-slate-400">
                <tr>
                    <th class="px-3 py-2">
                        <label class="sr-only" for="calendarSelectAll">Selectionner les evenements visibles</label>
                        <input id="calendarSelectAll" type="checkbox" class="h-4 w-4 rounded border-slate-600 bg-slate-900">
                    </th>
                    <th class="px-3 py-2">Statut</th>
                    <th class="px-3 py-2">Calendrier</th>
                    <th class="px-3 py-2">Type</th>
                    <th class="px-3 py-2">Titre</th>
                    <th class="px-3 py-2">Periode</th>
                    <th class="px-3 py-2">Horaire</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody id="calendarEventRows">
                <?php foreach ($calendarRows as $calendarRow): ?>
                    <?php
                    $calendarRowId = (int)($calendarRow['id'] ?? 0);
                    $calendarAudience = (string)($calendarRow['audience'] ?? 'children');
                    $calendarType = (string)($calendarRow['event_type'] ?? 'single');
                    $calendarActive = (int)($calendarRow['is_active'] ?? 1) === 1;
                    $calendarHasConflict = isset($calendarConflictIds[(string)$calendarRowId]);
                    $calendarSearchText = strtolower(trim(
                        (string)($calendarRow['title'] ?? '') . ' '
                        . ($calendarAudiences[$calendarAudience] ?? $calendarAudience) . ' '
                        . ($calendarEventTypes[$calendarType] ?? $calendarType) . ' '
                        . kc_calendar_admin_period_label($calendarRow) . ' '
                        . kc_calendar_admin_schedule_label($calendarRow)
                    ));
                    ?>
                    <tr data-calendar-row data-audience="<?= e($calendarAudience) ?>" data-search="<?= e($calendarSearchText) ?>" class="border-t <?= $calendarHasConflict ? 'border-red-500/30 bg-red-950/20' : 'border-slate-800' ?> <?= $calendarActive ? '' : 'bg-slate-950/50 text-slate-400' ?>">
                        <td class="px-3 py-3 align-top">
                            <label class="sr-only" for="calendarSelect<?= e((string)$calendarRowId) ?>">Selectionner <?= e((string)($calendarRow['title'] ?? '')) ?></label>
                            <input id="calendarSelect<?= e((string)$calendarRowId) ?>" form="calendarBulkForm" type="checkbox" name="event_ids[]" value="<?= e((string)$calendarRowId) ?>" class="h-4 w-4 rounded border-slate-600 bg-slate-900" data-calendar-select>
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $calendarActive ? 'bg-emerald-500/15 text-emerald-200' : 'bg-slate-700/60 text-slate-300' ?>">
                                <?= $calendarActive ? 'Publie' : 'Brouillon' ?>
                            </span>
                            <?php if ($calendarHasConflict): ?>
                                <span class="mt-1 inline-flex rounded-full bg-red-500/15 px-2 py-1 text-xs font-semibold text-red-200">Conflit</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3"><?= e($calendarAudiences[$calendarAudience] ?? $calendarAudience) ?></td>
                        <td class="px-3 py-3"><?= e($calendarEventTypes[$calendarType] ?? $calendarType) ?></td>
                        <td class="px-3 py-3 font-semibold text-slate-100"><?= e((string)($calendarRow['title'] ?? '')) ?></td>
                        <td class="px-3 py-3 whitespace-nowrap"><?= e(kc_calendar_admin_period_label($calendarRow)) ?></td>
                        <td class="px-3 py-3 whitespace-nowrap"><?= e(kc_calendar_admin_schedule_label($calendarRow)) ?></td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap justify-end gap-2">
                                <button type="button" data-edit-event-id="<?= e((string)$calendarRowId) ?>" class="rounded-lg border border-slate-600 px-2 py-1 text-xs font-semibold text-slate-100 hover:bg-slate-800">Modifier</button>
                                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="calendar_event_duplicate">
                                    <input type="hidden" name="event_id" value="<?= e((string)$calendarRowId) ?>">
                                    <button class="rounded-lg border border-sky-600 px-2 py-1 text-xs font-semibold text-sky-100 hover:bg-sky-950">Dupliquer</button>
                                </form>
                                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="calendar_event_toggle">
                                    <input type="hidden" name="event_id" value="<?= e((string)$calendarRowId) ?>">
                                    <input type="hidden" name="is_active" value="<?= $calendarActive ? '0' : '1' ?>">
                                    <button class="rounded-lg border border-emerald-600 px-2 py-1 text-xs font-semibold text-emerald-100 hover:bg-emerald-950"><?= $calendarActive ? 'Desactiver' : 'Activer' ?></button>
                                </form>
                                <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>" onsubmit="return confirm('Supprimer cet evenement calendrier ?');">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="calendar_event_delete">
                                    <input type="hidden" name="event_id" value="<?= e((string)$calendarRowId) ?>">
                                    <button class="rounded-lg border border-red-600 px-2 py-1 text-xs font-semibold text-red-100 hover:bg-red-950">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($calendarRows === []): ?>
                    <tr><td colspan="8" class="px-3 py-4 text-slate-400">Aucun evenement calendrier.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <dialog id="eventDialog" class="rounded-xl p-0 backdrop:bg-black/70">
      <form method="post" action="<?= e(manager_dashboard_anchor_url('admin-calendar')) ?>" id="eventForm" class="w-[94vw] max-w-2xl bg-slate-900 text-slate-100 p-5 space-y-4">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="action" id="eventFormAction" value="calendar_event_save">
        <input type="hidden" name="event_id" id="eventId">
        <h3 class="text-lg font-bold" id="dialogTitle"><?= e(kc_t('manager.calendar.new')) ?></h3>

        <div class="grid gap-3 md:grid-cols-2">
            <div>
                <label for="eventAudience" class="block text-sm">Calendrier</label>
                <select id="eventAudience" name="audience" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                    <?php foreach ($calendarAudiences as $audienceKey => $audienceLabel): ?>
                        <option value="<?= e((string)$audienceKey) ?>"><?= e((string)$audienceLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="eventType" class="block text-sm">Type</label>
                <select id="eventType" name="event_type" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                    <?php foreach ($calendarEventTypes as $typeKey => $typeLabel): ?>
                        <option value="<?= e((string)$typeKey) ?>"><?= e((string)$typeLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="eventTitle" class="block text-sm"><?= e(kc_t('manager.calendar.field_title')) ?></label>
            <input id="eventTitle" name="title" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2" required maxlength="255">
        </div>

        <div class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
            <div>
                <label for="eventColor" class="block text-sm">Couleur</label>
                <input id="eventColor" name="color" type="color" value="#3b82f6" class="mt-1 h-10 w-full rounded-lg border border-slate-700 bg-slate-800 px-2 py-1">
            </div>
            <div>
                <label for="eventSortOrder" class="block text-sm">Ordre</label>
                <input id="eventSortOrder" name="sort_order" type="number" min="0" value="100" class="mt-1 w-28 rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
            </div>
            <label class="mt-7 inline-flex items-center gap-2 text-sm text-slate-300">
                <input id="eventIsActive" type="checkbox" name="is_active" value="1" checked>
                Actif
            </label>
        </div>

        <div id="singleEventFields" class="grid gap-3 md:grid-cols-2">
            <div>
                <label for="eventStart" class="block text-sm"><?= e(kc_t('manager.calendar.start')) ?></label>
                <input type="datetime-local" id="eventStart" name="start_at" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
            </div>
            <div>
                <label for="eventEnd" class="block text-sm"><?= e(kc_t('manager.calendar.end')) ?></label>
                <input type="datetime-local" id="eventEnd" name="end_at" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
            </div>
        </div>

        <div id="recurringEventFields" class="hidden space-y-3 rounded-xl border border-slate-800 bg-slate-950/40 p-4">
            <div>
                <p class="text-sm font-semibold">Jours de repetition</p>
                <div class="mt-2 grid grid-cols-2 gap-2 text-sm sm:grid-cols-4">
                    <?php foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 0 => 'Dim'] as $dayValue => $dayLabel): ?>
                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-2 py-1">
                            <input type="checkbox" name="days_of_week[]" value="<?= e((string)$dayValue) ?>" data-day-checkbox>
                            <?= e($dayLabel) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label for="eventStartTime" class="block text-sm">Heure debut</label>
                    <input type="time" id="eventStartTime" name="start_time" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                </div>
                <div>
                    <label for="eventEndTime" class="block text-sm">Heure fin</label>
                    <input type="time" id="eventEndTime" name="end_time" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                </div>
                <div>
                    <label for="eventStartRecur" class="block text-sm">Debut recurrence</label>
                    <input type="date" id="eventStartRecur" name="start_recur" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                </div>
                <div>
                    <label for="eventEndRecur" class="block text-sm">Fin recurrence</label>
                    <input type="date" id="eventEndRecur" name="end_recur" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2">
                </div>
            </div>
        </div>

        <div>
            <label for="eventDesc" class="block text-sm"><?= e(kc_t('manager.calendar.field_description')) ?></label>
            <textarea id="eventDesc" name="description" rows="3" class="mt-1 w-full rounded-lg bg-slate-800 border border-slate-700 px-3 py-2"></textarea>
        </div>

        <div class="flex justify-between gap-3">
          <button type="button" id="btnDeleteEvent" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500 hidden"><?= e(kc_t('manager.calendar.delete')) ?></button>
          <div class="ml-auto flex gap-2">
            <button type="button" id="btnCancel" class="rounded-lg border border-slate-600 px-3 py-2 text-sm"><?= e(kc_t('manager.calendar.cancel')) ?></button>
            <button type="submit" id="btnSaveEvent" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500"><?= e(kc_t('manager.calendar.save')) ?></button>
          </div>
        </div>
      </form>
    </dialog>

</main>

<script>
(() => {
  document.querySelectorAll('form[data-disable-on-submit]').forEach((managedForm) => {
    managedForm.addEventListener('submit', () => {
      managedForm.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');
      });
    });
  });

  const memberSearch = document.getElementById('memberSearch');
  const memberRoleFilter = document.getElementById('memberRoleFilter');
  const memberGradeFilter = document.getElementById('memberGradeFilter');
  const memberVisibleCount = document.getElementById('memberVisibleCount');
  const memberRows = Array.from(document.querySelectorAll('[data-member-row]'));

  function syncMemberRows() {
    const query = (memberSearch?.value || '').trim().toLowerCase();
    const role = memberRoleFilter?.value || 'all';
    const grade = memberGradeFilter?.value || 'all';
    let visibleCount = 0;

    memberRows.forEach((row) => {
      const matchesQuery = query === '' || (row.dataset.search || '').includes(query);
      const matchesRole = role === 'all' || row.dataset.role === role;
      const matchesGrade = grade === 'all' || row.dataset.grade === grade;
      const visible = matchesQuery && matchesRole && matchesGrade;
      row.classList.toggle('hidden', !visible);
      if (visible) {
        visibleCount++;
      }
    });

    if (memberVisibleCount) {
      memberVisibleCount.textContent = String(visibleCount);
    }
  }

  memberSearch?.addEventListener('input', syncMemberRows);
  memberRoleFilter?.addEventListener('change', syncMemberRows);
  memberGradeFilter?.addEventListener('change', syncMemberRows);
  syncMemberRows();

  (window.kcFullCalendarReady || Promise.resolve(window.FullCalendar)).then((FullCalendar) => {
  if (!FullCalendar || typeof FullCalendar.Calendar !== 'function') {
    return;
  }

  const calendarTexts = <?= json_encode([
      'locale' => $locale,
      'new' => kc_t('manager.calendar.new'),
      'edit' => kc_t('manager.calendar.edit'),
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const serverEvents = <?= json_encode($calendarPayload['fullcalendar'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  let activeFilter = 'club';

  const toLocalInput = (date) => {
    if (!date) return '';
    const d = new Date(date);
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0,16);
  };
  const compactDate = (value) => value ? String(value).slice(0, 10) : '';
  const compactTime = (value) => value ? String(value).slice(0, 5) : '';

  const dialog = document.getElementById('eventDialog');
  const form = document.getElementById('eventForm');
  const actionInput = document.getElementById('eventFormAction');
  const singleFields = document.getElementById('singleEventFields');
  const recurringFields = document.getElementById('recurringEventFields');
  const dayCheckboxes = Array.from(document.querySelectorAll('[data-day-checkbox]'));
  const fields = {
    id: document.getElementById('eventId'),
    audience: document.getElementById('eventAudience'),
    type: document.getElementById('eventType'),
    title: document.getElementById('eventTitle'),
    color: document.getElementById('eventColor'),
    sortOrder: document.getElementById('eventSortOrder'),
    isActive: document.getElementById('eventIsActive'),
    start: document.getElementById('eventStart'),
    end: document.getElementById('eventEnd'),
    startTime: document.getElementById('eventStartTime'),
    endTime: document.getElementById('eventEndTime'),
    startRecur: document.getElementById('eventStartRecur'),
    endRecur: document.getElementById('eventEndRecur'),
    desc: document.getElementById('eventDesc')
  };
  const btnDelete = document.getElementById('btnDeleteEvent');
  const btnNew = document.getElementById('btnNewEvent');
  const btnCancel = document.getElementById('btnCancel');
  const filterButtons = Array.from(document.querySelectorAll('.calendar-filter'));
  const searchInput = document.getElementById('calendarSearch');
  const tableRows = Array.from(document.querySelectorAll('[data-calendar-row]'));
  const editButtons = Array.from(document.querySelectorAll('[data-edit-event-id]'));
  const bulkForm = document.getElementById('calendarBulkForm');
  const bulkAction = document.getElementById('calendarBulkAction');
  const bulkCount = document.getElementById('calendarBulkCount');
  const selectAll = document.getElementById('calendarSelectAll');
  const rowCheckboxes = Array.from(document.querySelectorAll('[data-calendar-select]'));
  let pendingCalendarChangeRevert = null;
  let eventFormSubmitting = false;

  function filteredEvents() {
    if (activeFilter === 'club') {
      return serverEvents;
    }

    return serverEvents.filter((event) => {
      const audience = event.extendedProps && event.extendedProps.audience;
      return audience === activeFilter || audience === 'all';
    });
  }

  function refreshCalendarEvents(calendar) {
    calendar.removeAllEvents();
    calendar.addEventSource(filteredEvents());
  }

  function updateFilterButtons() {
    filterButtons.forEach((button) => {
      const active = button.dataset.filter === activeFilter;
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
      button.classList.toggle('bg-slate-800', active);
      button.classList.toggle('text-slate-100', active);
      button.classList.toggle('bg-transparent', !active);
    });
  }

  function syncCalendarTable() {
    const query = (searchInput?.value || '').trim().toLowerCase();
    tableRows.forEach((row) => {
      const audience = row.dataset.audience || '';
      const matchesAudience = activeFilter === 'club' || audience === activeFilter || audience === 'all';
      const matchesSearch = query === '' || (row.dataset.search || '').includes(query);
      row.classList.toggle('hidden', !(matchesAudience && matchesSearch));
    });
    updateCalendarBulkState();
  }

  function visibleCalendarCheckboxes() {
    return rowCheckboxes.filter((checkbox) => {
      const row = checkbox.closest('[data-calendar-row]');
      return row && !row.classList.contains('hidden');
    });
  }

  function updateCalendarBulkState() {
    const selectedCount = rowCheckboxes.filter((checkbox) => checkbox.checked).length;
    const visibleCheckboxes = visibleCalendarCheckboxes();
    const visibleSelectedCount = visibleCheckboxes.filter((checkbox) => checkbox.checked).length;

    bulkCount.textContent = selectedCount + ' selection' + (selectedCount > 1 ? 's' : '');
    bulkForm.querySelectorAll('button[type="submit"]').forEach((button) => {
      button.disabled = selectedCount === 0;
    });

    selectAll.checked = visibleCheckboxes.length > 0 && visibleSelectedCount === visibleCheckboxes.length;
    selectAll.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visibleCheckboxes.length;
  }

  function resetDayCheckboxes(values = []) {
    const selectedDays = values.map((value) => Number(value));
    dayCheckboxes.forEach((checkbox) => {
      checkbox.checked = selectedDays.includes(Number(checkbox.value));
    });
  }

  function toggleEventTypeFields() {
    const recurring = fields.type.value === 'recurring';
    singleFields.classList.toggle('hidden', recurring);
    recurringFields.classList.toggle('hidden', !recurring);
    fields.start.required = !recurring;
    fields.end.required = !recurring;
    fields.startTime.required = recurring;
    fields.endTime.required = recurring;
    fields.startRecur.required = recurring;
    fields.endRecur.required = recurring;
  }

  function eventDataFromCalendar(event) {
    const extended = event.extendedProps || {};
    return {
      id: event.id,
      title: event.title,
      start: event.start,
      end: event.end,
      color: extended.color || event.backgroundColor || event.borderColor,
      audience: extended.audience || 'children',
      eventType: extended.eventType || 'single',
      description: extended.description || '',
      daysOfWeek: extended.daysOfWeek || [],
      startTime: extended.startTime || '',
      endTime: extended.endTime || '',
      startRecur: extended.startRecur || '',
      endRecur: extended.endRecur || '',
      sortOrder: extended.sortOrder ?? 100,
      isActive: extended.isActive !== false
    };
  }

  function eventDataFromPlainEvent(event) {
    const extended = event.extendedProps || {};
    return {
      id: event.id,
      title: event.title,
      start: event.start || '',
      end: event.end || '',
      color: extended.color || event.color || '#3b82f6',
      audience: extended.audience || 'children',
      eventType: extended.eventType || 'single',
      description: extended.description || '',
      daysOfWeek: extended.daysOfWeek || event.daysOfWeek || [],
      startTime: extended.startTime || event.startTime || '',
      endTime: extended.endTime || event.endTime || '',
      startRecur: extended.startRecur || event.startRecur || '',
      endRecur: extended.endRecur || '',
      sortOrder: extended.sortOrder ?? 100,
      isActive: extended.isActive !== false
    };
  }

  const calendar = new FullCalendar.Calendar(document.getElementById('adminCalendar'), {
    plugins: FullCalendar.plugins || [],
    initialView: 'dayGridMonth',
    locale: calendarTexts.locale,
    editable: true,
    selectable: true,
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth' },
    events: filteredEvents(),
    select(info) {
      openDialog({
        start: info.startStr,
        end: info.endStr,
        audience: activeFilter === 'club' ? 'children' : activeFilter,
        eventType: 'single'
      });
    },
    eventClick(info) {
      openDialog(eventDataFromCalendar(info.event));
    },
    eventDrop(info) {
      const data = eventDataFromCalendar(info.event);
      if (data.eventType === 'recurring') {
        info.revert();
        openDialog(data);
        return;
      }
      openDialog(data, info.revert);
    },
    eventResize(info) {
      const data = eventDataFromCalendar(info.event);
      if (data.eventType === 'recurring') {
        info.revert();
        openDialog(data);
        return;
      }
      openDialog(data, info.revert);
    }
  });

  function openDialog(data = {}, revertOnCancel = null) {
    pendingCalendarChangeRevert = typeof revertOnCancel === 'function' ? revertOnCancel : null;
    eventFormSubmitting = false;
    document.getElementById('dialogTitle').textContent = data.id ? calendarTexts.edit : calendarTexts.new;
    fields.id.value = data.id || '';
    fields.audience.value = data.audience || (activeFilter === 'club' ? 'children' : activeFilter);
    fields.type.value = data.eventType || 'single';
    fields.title.value = data.title || '';
    fields.color.value = data.color || '#3b82f6';
    fields.sortOrder.value = String(data.sortOrder ?? 100);
    fields.isActive.checked = data.isActive !== false;
    fields.start.value = toLocalInput(data.start);
    fields.end.value = toLocalInput(data.end);
    fields.startTime.value = compactTime(data.startTime);
    fields.endTime.value = compactTime(data.endTime);
    fields.startRecur.value = compactDate(data.startRecur);
    fields.endRecur.value = compactDate(data.endRecur);
    resetDayCheckboxes(data.daysOfWeek || []);
    fields.desc.value = data.description || '';
    actionInput.value = 'calendar_event_save';
    btnDelete.classList.toggle('hidden', !data.id);
    toggleEventTypeFields();
    dialog.showModal();
  }

  btnNew.addEventListener('click', () => openDialog({
    audience: activeFilter === 'club' ? 'children' : activeFilter,
    eventType: 'single',
    isActive: true
  }));
  btnCancel.addEventListener('click', () => dialog.close());
  dialog.addEventListener('close', () => {
    if (!eventFormSubmitting && pendingCalendarChangeRevert) {
      pendingCalendarChangeRevert();
    }
    pendingCalendarChangeRevert = null;
    eventFormSubmitting = false;
  });
  form.addEventListener('submit', () => {
    eventFormSubmitting = true;
  });
  fields.type.addEventListener('change', toggleEventTypeFields);

  filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'club';
      updateFilterButtons();
      refreshCalendarEvents(calendar);
      syncCalendarTable();
    });
  });

  searchInput?.addEventListener('input', syncCalendarTable);

  rowCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', updateCalendarBulkState);
  });

  selectAll?.addEventListener('change', () => {
    visibleCalendarCheckboxes().forEach((checkbox) => {
      checkbox.checked = selectAll.checked;
    });
    updateCalendarBulkState();
  });

  bulkForm?.addEventListener('submit', (event) => {
    const selectedCount = rowCheckboxes.filter((checkbox) => checkbox.checked).length;
    if (selectedCount === 0) {
      event.preventDefault();
      return;
    }

    if (bulkAction.value === 'delete' && !confirm('Supprimer les evenements calendrier selectionnes ?')) {
      event.preventDefault();
    }
  });

  editButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const eventId = button.dataset.editEventId || '';
      const event = serverEvents.find((candidate) => String(candidate.id) === String(eventId));
      if (event) {
        openDialog(eventDataFromPlainEvent(event));
      }
    });
  });

  btnDelete.addEventListener('click', () => {
    if (!fields.id.value) {
      return;
    }

    actionInput.value = 'calendar_event_delete';
    eventFormSubmitting = true;
    form.submit();
  });

  updateFilterButtons();
  syncCalendarTable();
  toggleEventTypeFields();
  calendar.render();
  }).catch((e) => console.error('Erreur FullCalendar:', e));
})();
</script>

</body>
</html>
