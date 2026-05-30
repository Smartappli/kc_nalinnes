<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/manager/admin_access.php';
require __DIR__ . '/config/database.php';

session_start();

$redirectFail = 'membres.php';
$redirectSuccess = 'member/dashboard.php';

function flash(string $message, string $type = 'info'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirectFail);
    exit;
}

$loginBypassEnabled = is_temp_bypass_login_enabled();
if ($loginBypassEnabled) {
    flash('Bypass login temporaire actif.', 'info');
    header('Location: /manager/dashboard.php', true, 303);
    exit;
}

// CSRF
$postedToken = (string)($_POST['csrf_token'] ?? '');
$sessionToken = (string)($_SESSION['csrf_token'] ?? '');

if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    flash('Requête invalide (CSRF).', 'error');
    header('Location: ' . $redirectFail);
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');

// Repop checkbox si erreur
$_SESSION['old_remember'] = (!empty($_POST['remember']) && $_POST['remember'] == '1') ? 1 : 0;

// Remember duration (comme ton snippet)
if (!empty($_POST['remember']) && $_POST['remember'] == '1') {
    // keep logged in for one year
    $rememberDuration = (int) (60 * 60 * 24 * 365.25);
}
else {
    // do not keep logged in after session ends
    $rememberDuration = null;
}

$_SESSION['old_email'] = $email;

try {
    $db = create_database_connection();
    $auth = new \Delight\Auth\Auth($db);

    $auth->login($email, $password, $rememberDuration);

    unset($_SESSION['old_email'], $_SESSION['old_remember']);
    flash('Connexion réussie.', 'success');
    $redirectSuccess = resolve_dashboard_path($email, $db, (string) getenv('ADMIN_EMAILS'));
    header('Location: ' . $redirectSuccess);
    exit;
}
catch (\Delight\Auth\InvalidEmailException $e) {
    flash('Adresse email incorrecte.', 'error');
}
catch (\Delight\Auth\InvalidPasswordException $e) {
    flash('Mot de passe incorrect.', 'error');
}
catch (\Delight\Auth\EmailNotVerifiedException $e) {
    flash('Email non vérifié.', 'error');
}
catch (\Delight\Auth\TooManyRequestsException $e) {
    flash('Trop de tentatives, réessaie plus tard.', 'error');
}
catch (\Throwable $e) {
    flash('Erreur interne. Merci de réessayer.', 'error');
}

header('Location: ' . $redirectFail);
exit;
