<?php
declare(strict_types=1);

// contact.php

require __DIR__ . '/includes/i18n.php';

// Démarrer la session pour le petit rate limiting
session_start();

if (isset($_POST['lang']) && !isset($_GET['lang'])) {
    $_GET['lang'] = (string)$_POST['lang'];
}

$locale = kc_current_locale();

// On n'accepte que les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . kc_redirect_url_with_locale('/'));
    exit;
}

// Honeypot anti-bot : champ "website" que tu as ajouté en hidden dans le formulaire
$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    // Probablement un bot -> on fait semblant que tout est OK, mais on ne fait rien
    http_response_code(200);
    exit;
}

// Protection simple contre les envois trop rapprochés (30 secondes)
$now = time();
if (isset($_SESSION['last_contact']) && ($now - $_SESSION['last_contact']) < 30) {
    $errorRateLimit = true;
} else {
    $errorRateLimit = false;
    $_SESSION['last_contact'] = $now;
}

// Récupération des champs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// Validation de base
if ($name === '' || $email === '' || $message === '') {
    $errors[] = kc_t('contact.error.required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = kc_t('contact.error.email');
}

// Si rate limit déclenché, on ajoute un message d'erreur
if ($errorRateLimit) {
    $errors[] = kc_t('contact.error.rate_limit');
}

// Fonction d'échappement pour l'HTML
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Si erreurs -> petite page d'erreur
if ($errors !== []) {
    ?>
    <!doctype html>
    <html translate="no" lang="<?= e($locale) ?>">
    <head>
        <meta charset="utf-8">
  <meta name="google" content="notranslate">
        <title><?= e(kc_t('contact.error.title')) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
          body {
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            padding: 2rem;
            background: #020617;
            color: #e5e7eb;
          }
          a { color: #38bdf8; }
          .card {
            max-width: 600px;
            margin: 0 auto;
            background: #020617;
            border-radius: 1rem;
            border: 1px solid #1f2937;
            padding: 1.5rem;
          }
        </style>
    </head>
    <body>
        <div class="card">
            <h1 style="font-size:1.5rem; margin-bottom:1rem;"><?= e(kc_t('contact.error.heading')) ?></h1>
            <p><?= e(kc_t('contact.error.intro')) ?></p>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top:1rem;">
                <a href="<?= e(kc_localized_url($locale, '/')) ?>"><?= e(kc_t('contact.back')) ?></a>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// -------- Envoi de l'e-mail --------

// Adresse de destination (à adapter si besoin)
$to = 'info@kc-nalinnes.be';

// Sujet du mail
$subject = kc_t('contact.mail.subject');

// Corps du message
$body  = kc_t('contact.mail.intro') . "\n\n";
$body .= kc_t('contact.mail.name') . ' : ' . $name . "\n";
$body .= kc_t('contact.mail.email') . ' : ' . $email . "\n\n";
$body .= kc_t('contact.mail.message') . " :\n" . $message . "\n";

// IMPORTANT sur OVH : utiliser une adresse de ton domaine en From
$fromEmail = 'no-reply@kc-nalinnes.be'; // remplace par une adresse existante si besoin

$headers = [];
$headers[] = 'From: "KC Nalinnes" <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

// Envoi du mail
$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

?>
<!doctype html>
<html translate="no" lang="<?= e($locale) ?>">
<head>
    <meta charset="utf-8">
  <meta name="google" content="notranslate">
    <title><?= e($sent ? kc_t('contact.success.title') : kc_t('contact.failure.title')) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      body {
        font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        padding: 2rem;
        background: #020617;
        color: #e5e7eb;
      }
      a { color: #38bdf8; }
      .card {
        max-width: 600px;
        margin: 0 auto;
        background: #020617;
        border-radius: 1rem;
        border: 1px solid #1f2937;
        padding: 1.5rem;
      }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($sent): ?>
            <h1 style="font-size:1.5rem; margin-bottom:1rem;"><?= e(kc_t('contact.success.heading')) ?></h1>
            <p><?= e(kc_t('contact.success.body')) ?></p>
        <?php else: ?>
            <h1 style="font-size:1.5rem; margin-bottom:1rem;"><?= e(kc_t('contact.failure.heading')) ?></h1>
            <p><?= e(kc_t('contact.failure.body')) ?></p>
            <ul>
                <li><?= e(kc_t('contact.mail.email')) ?> : <a href="mailto:info@kc-nalinnes.be">info@kc-nalinnes.be</a></li>
                <li><?= e(kc_t('contact.phone')) ?> : <a href="tel:+32497251214">+32 497 25 12 14</a></li>
            </ul>
        <?php endif; ?>

        <p style="margin-top:1rem;">
            <a href="<?= e(kc_localized_url($locale, '/')) ?>"><?= e(kc_t('contact.back')) ?></a>
        </p>
    </div>
</body>
</html>
