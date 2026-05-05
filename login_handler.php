<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

session_start();

$db = new \PDO('mysql:dbname=my-database;host=localhost;charset=utf8mb4', 'my-username', 'my-password');

$auth = new \Delight\Auth\Auth($db);

$redirectFail = 'login.php';
$redirectSuccess = 'manager/dashboard.php';

function flash(string $message, string $type = 'info'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirectFail);
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
    $auth->login($email, $password, $rememberDuration);

    unset($_SESSION['old_email'], $_SESSION['old_remember']);
    flash('Connexion réussie.', 'success');
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