<?php
// contact.php

// Démarrer la session pour le petit rate limiting
session_start();

// On n'accepte que les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// Honeypot anti-bot : champ "website" que tu as ajouté en hidden dans le formulaire
$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    // Probablement un bot → on fait semblant que tout est OK, mais on ne fait rien
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
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// Validation de base
if ($name === '' || $email === '' || $message === '') {
    $errors[] = 'Veuillez remplir tous les champs.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Adresse e-mail invalide.';
}

// Si rate limit déclenché, on ajoute un message d’erreur
if ($errorRateLimit) {
    $errors[] = 'Vous avez déjà envoyé un message il y a quelques instants. Merci de patienter un peu avant de réessayer.';
}

// Fonction d’échappement pour l’HTML
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Si erreurs → petite page d’erreur
if (!empty($errors)) {
    ?>
    <!doctype html>
    <html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Erreur dans le formulaire</title>
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
            <h1 style="font-size:1.5rem; margin-bottom:1rem;">Oups, il y a un problème 👀</h1>
            <p>Votre message n’a pas pu être envoyé :</p>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top:1rem;">
                <a href="/">← Retour au site</a>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// -------- Envoi de l’e-mail --------

// Adresse de destination (à adapter si besoin)
$to = 'info@kc-nalinnes.be';

// Sujet du mail
$subject = 'Nouveau message depuis le site KC Nalinnes';

// Corps du message
$body  = "Vous avez reçu un nouveau message depuis le formulaire de contact du site KC Nalinnes.\n\n";
$body .= "Nom : " . $name . "\n";
$body .= "Email : " . $email . "\n\n";
$body .= "Message :\n" . $message . "\n";

// IMPORTANT sur OVH : utiliser une adresse de ton domaine en From
$fromEmail = 'no-reply@kc-nalinnes.be'; // remplace par une adresse existante si besoin

$headers   = [];
$headers[] = 'From: "KC Nalinnes" <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

// Envoi du mail
$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?php echo $sent ? 'Merci pour votre message' : 'Erreur lors de l’envoi'; ?></title>
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
            <h1 style="font-size:1.5rem; margin-bottom:1rem;">Merci 🙏</h1>
            <p>Votre message a bien été envoyé. Nous vous répondrons dès que possible.</p>
        <?php else: ?>
            <h1 style="font-size:1.5rem; margin-bottom:1rem;">Oups, un problème est survenu</h1>
            <p>
                Votre message n’a pas pu être envoyé.  
                Vous pouvez réessayer plus tard ou nous contacter directement :
            </p>
            <ul>
                <li>Email : <a href="mailto:info@kc-nalinnes.be">info@kc-nalinnes.be</a></li>
                <li>Téléphone : <a href="tel:+32497251214">+32 497 25 12 14</a></li>
            </ul>
        <?php endif; ?>

        <p style="margin-top:1rem;">
            <a href="/">← Retour au site</a>
        </p>
    </div>
</body>
</html>
