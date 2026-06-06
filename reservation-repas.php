<?php
declare(strict_types=1);

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/i18n.php';
require __DIR__ . '/member/meal_reservation.php';

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
    return match ($type) {
        'success' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
        'error' => 'border-red-500/40 bg-red-500/10 text-red-200',
        default => 'border-sky-500/40 bg-sky-500/10 text-sky-200',
    };
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$flashMsg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$old = $_SESSION['meal_public_old'] ?? [
    'profile_name' => '',
    'contact_email' => '',
    'contact_phone' => '',
    'adult_qty' => '0',
    'child_qty' => '0',
    'notes' => '',
    'send_copy' => '1',
];
unset($_SESSION['meal_public_old']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string)($_POST['csrf_token'] ?? '');
    $postedSubmissionToken = (string)($_POST['meal_submission_token'] ?? '');

    $profileName = trim((string)($_POST['profile_name'] ?? ''));
    $contactEmail = trim((string)($_POST['contact_email'] ?? ''));
    $contactPhone = trim((string)($_POST['contact_phone'] ?? ''));
    $adultQty = max(0, (int)($_POST['repas_adulte'] ?? 0));
    $childQty = max(0, (int)($_POST['repas_enfant'] ?? 0));
    $notes = trim((string)($_POST['notes'] ?? ''));
    $sendCopy = !empty($_POST['send_copy']) && $_POST['send_copy'] === '1';

    $_SESSION['meal_public_old'] = [
        'profile_name' => $profileName,
        'contact_email' => $contactEmail,
        'contact_phone' => $contactPhone,
        'adult_qty' => (string)$adultQty,
        'child_qty' => (string)$childQty,
        'notes' => $notes,
        'send_copy' => $sendCopy ? '1' : '0',
    ];

    if (!hash_equals((string)$_SESSION['csrf_token'], $postedToken)) {
        flash(kc_t('meal.flash.invalid_request'), 'error');
        header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
        exit;
    }

    if (!consume_meal_reservation_submission_token('public', $postedSubmissionToken)) {
        flash(kc_t('meal.flash.invalid_request'), 'error');
        header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
        exit;
    }

    if ($profileName === '' || strlen($profileName) > 255) {
        flash(kc_t('meal.flash.invalid_name'), 'error');
        header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
        exit;
    }

    if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        flash(kc_t('meal.flash.invalid_email'), 'error');
        header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
        exit;
    }

    if ($adultQty === 0 && $childQty === 0) {
        flash(kc_t('meal.flash.no_meal'), 'error');
        header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
        exit;
    }

    try {
        $total = compute_meal_total($adultQty, $childQty, 19, 10);
        $reservationDate = date('Y-m-d H:i:s');
        $reservationId = null;

        try {
            $db = create_database_connection();
            $reservationId = save_public_meal_reservation($db, [
                'profile_name' => $profileName,
                'contact_email' => $contactEmail,
                'contact_phone' => $contactPhone,
                'adult_qty' => $adultQty,
                'child_qty' => $childQty,
                'total_amount' => $total,
                'notes' => $notes,
            ]);
        }
        catch (Throwable $dbError) {
            error_log('Meal reservation database save failed: ' . $dbError->getMessage());
        }

        append_meal_reservation_to_excel([
            'date' => $reservationDate,
            'member_user_id' => '0',
            'profile_name' => $profileName,
            'profile_type' => 'public',
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
            'adult_qty' => (string)$adultQty,
            'child_qty' => (string)$childQty,
            'total_amount' => (string)$total,
            'notes' => $notes,
        ]);

        $to = (string)(getenv('RESERVATION_EMAIL_TO') ?: 'duchesnesakura@gmail.com');
        $subject = kc_t('meal.mail.admin_subject');
        $message = kc_t('meal.mail.heading') . "\n"
            . kc_t('meal.mail.reservation_id') . ": " . ($reservationId !== null ? (string)$reservationId : 'excel-only') . "\n"
            . kc_t('meal.mail.name') . ": " . $profileName . "\n"
            . "Email: " . $contactEmail . "\n"
            . kc_t('meal.mail.phone') . ": " . ($contactPhone !== '' ? $contactPhone : '-') . "\n"
            . kc_t('meal.mail.adults') . ": " . $adultQty . "\n"
            . kc_t('meal.mail.children') . ": " . $childQty . "\n"
            . kc_t('meal.mail.total') . ": " . $total . " EUR\n"
            . kc_t('meal.mail.notes') . ": " . ($notes !== '' ? $notes : '-') . "\n"
            . "Date: " . $reservationDate . "\n";
        $headers = "From: no-reply@kc-nalinnes.be\r\nReply-To: " . $contactEmail;
        $adminMailSent = send_meal_reservation_mail($to, $subject, $message, $headers);

        if ($sendCopy) {
            send_meal_reservation_mail($contactEmail, kc_t('meal.mail.copy_subject'), $message, 'From: no-reply@kc-nalinnes.be');
        }

        if (!$adminMailSent) {
            error_log('Meal reservation saved but admin notification failed. Reservation ID: ' . $reservationId);
        }

        unset($_SESSION['meal_public_old']);
        flash(kc_t('meal.flash.success'), 'success');
    }
    catch (Throwable $e) {
        error_log('Meal reservation save failed: ' . $e->getMessage());
        flash(kc_t('meal.flash.error'), 'error');
    }

    header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
    exit;
}

$mealSubmissionToken = meal_reservation_submission_token('public');
?>
<!doctype html>
<html<?= kc_translate_guard_attr($locale) ?> lang="<?= e($locale) ?>" class="">
<head>
  <meta charset="utf-8" />
  <?= kc_google_notranslate_meta($locale) ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e(kc_t('meal.meta.title')) ?></title>
  <meta name="description" content="<?= e(kc_t('meal.meta.description')) ?>" />
  <meta name="robots" content="noindex,nofollow,noarchive" />
  <link rel="canonical" href="https://kc-nalinnes.be/reservation-repas.php" />
  <meta name="theme-color" content="#0f172a" />
  <link rel="icon" href="/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/index.css">
  <script>
    (function(){
      try{
        var saved = localStorage.getItem('themeMode');
        if(saved === 'light'){ document.documentElement.classList.add('light'); }
      }catch(e){}
    })();
  </script>
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="fixed inset-x-0 top-0 z-50 bg-slate-950/70 glass">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <a href="/" class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
            <img
              src="/assets/logo-kc-nalinnes1.png"
              alt="KC Nalinnes - Frank Duchesne"
              class="h-full w-full object-contain"
            />
          </div>

          <div class="h-10 w-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
            <img
              src="/assets/logo-kc-nalinnes2.png"
              alt="KC Nalinnes - Olivier Lowie"
              class="h-full w-full object-contain"
            />
          </div>
          <span class="font-semibold"><?= e(kc_t('common.brand')) ?></span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="/#horaires" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.schedule')) ?></a>
          <a href="/#tarifs" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.prices')) ?></a>
          <a href="/#calendrier" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.calendar')) ?></a>
          <a href="/#coach" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.instructors')) ?></a>
          <a href="/#documents" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.documents')) ?></a>
          <a href="/#actus" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.news')) ?></a>
          <a href="/#contact" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.contact')) ?></a>
          <a href="/membres.php"
            class="ml-2 rounded-full bg-red-600 px-4 py-2 font-semibold text-white shadow-md shadow-red-900/40 hover:bg-red-500 hover:translate-y-[1px] transition">
            <?= e(kc_t('common.nav.members')) ?>
          </a>

          <?= kc_language_switcher('ml-2 inline-flex') ?>

          <button id="themeToggle" class="ml-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                  aria-pressed="false" aria-label="<?= e(kc_t('common.theme.toggle')) ?>">
            <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79L3.17 4.83l1.79 1.8 1.8-1.79zm10.48 0l1.8-1.79 1.79 1.78-1.79 1.8-1.8-1.79zM12 4V1h-0v3h0zm0 19v-3h0v3h0zM4 12H1v0h3v0zm19 0h-3v0h3v0zM6.76 19.16l-1.8 1.79-1.79-1.78 1.79-1.8 1.8 1.79zM17.24 19.16l1.8 1.79 1.79-1.78-1.79-1.8-1.8 1.79zM12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/></svg>
            <span id="themeLabel">Dark</span>
          </button>
        </nav>

        <button id="menuBtn"
          class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-md
                bg-slate-800 text-slate-100 border border-transparent
                hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500"
          aria-label="<?= e(kc_t('common.menu.open')) ?>" type="button">☰</button>
      </div>
    </div>

    <nav id="mobileNav" class="md:hidden hidden border-t border-slate-800">
      <div class="mx-auto max-w-7xl px-4 py-3 space-y-2">
        <a href="/#horaires" class="block"><?= e(kc_t('common.nav.schedule')) ?></a>
        <a href="/#tarifs" class="block"><?= e(kc_t('common.nav.prices')) ?></a>
        <a href="/#calendrier" class="block"><?= e(kc_t('common.nav.calendar')) ?></a>
        <a href="/#coach" class="block"><?= e(kc_t('common.nav.instructors')) ?></a>
        <a href="/#documents" class="block"><?= e(kc_t('common.nav.documents')) ?></a>
        <a href="/#actus" class="block"><?= e(kc_t('common.nav.news')) ?></a>
        <a href="/#contact" class="block"><?= e(kc_t('common.nav.contact')) ?></a>
        <a href="/membres.php" class="block font-semibold text-red-400"><?= e(kc_t('common.nav.members')) ?></a>

        <?= kc_language_switcher('mt-2 block') ?>

        <button id="themeToggleMobile" class="mt-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                aria-pressed="false" aria-label="<?= e(kc_t('common.theme.toggle')) ?>">
          🌗 <span id="themeLabelMobile">Dark</span>
        </button>
      </div>
    </nav>
  </header>

  <main id="reservation-repas-page" class="pt-24 pb-16">
    <section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
      <div class="reservation-form-panel rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-2xl">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300"><?= e(kc_t('meal.hero.kicker')) ?></p>
        <h1 class="mt-2 text-3xl font-extrabold text-slate-100"><?= e(kc_t('meal.hero.title')) ?></h1>
        <p class="mt-3 text-slate-300">
          <?= e(kc_t('meal.hero.description')) ?>
        </p>

        <div class="mt-5 grid gap-3 sm:grid-cols-2">
          <div class="reservation-meal-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
            <p class="text-sm font-semibold text-slate-100"><?= e(kc_t('meal.card.adult.title')) ?></p>
            <p class="mt-1 text-sm text-slate-400"><?= e(kc_t('meal.card.adult.description')) ?></p>
          </div>
          <div class="reservation-meal-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
            <p class="text-sm font-semibold text-slate-100"><?= e(kc_t('meal.card.child.title')) ?></p>
            <p class="mt-1 text-sm text-slate-400"><?= e(kc_t('meal.card.child.description')) ?></p>
          </div>
        </div>

        <?php if ($flashMsg): ?>
          <div class="mt-5 rounded-xl border px-4 py-3 text-sm <?= e(flash_classes($flashMsg)) ?>">
            <?= e((string)$flashMsg['message']) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= e(kc_localized_url($locale, '/reservation-repas.php')) ?>" class="mt-6 grid gap-4 md:grid-cols-2" data-disable-on-submit>
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="meal_submission_token" value="<?= e($mealSubmissionToken) ?>">

          <div class="md:col-span-2">
            <label for="profile_name" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.name')) ?></label>
            <input id="profile_name" name="profile_name" required maxlength="255" value="<?= e((string)$old['profile_name']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="name">
          </div>

          <div>
            <label for="contact_email" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.email')) ?></label>
            <input id="contact_email" type="email" name="contact_email" required value="<?= e((string)$old['contact_email']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="email">
          </div>

          <div>
            <label for="contact_phone" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.phone')) ?></label>
            <input id="contact_phone" name="contact_phone" value="<?= e((string)$old['contact_phone']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100" autocomplete="tel">
          </div>

          <div>
            <label for="repas_adulte" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.adults')) ?></label>
            <input id="repas_adulte" type="number" min="0" name="repas_adulte" value="<?= e((string)$old['adult_qty']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
          </div>

          <div>
            <label for="repas_enfant" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.children')) ?></label>
            <input id="repas_enfant" type="number" min="0" name="repas_enfant" value="<?= e((string)$old['child_qty']) ?>" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100">
          </div>

          <div class="md:col-span-2">
            <label for="notes" class="block text-sm font-semibold text-slate-200"><?= e(kc_t('meal.form.notes')) ?></label>
            <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-slate-100"><?= e((string)$old['notes']) ?></textarea>
          </div>

          <div class="md:col-span-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-slate-300">
              <input type="checkbox" name="send_copy" value="1" <?= ((string)$old['send_copy'] === '1') ? 'checked' : '' ?>>
              <?= e(kc_t('meal.form.copy')) ?>
            </label>
            <button class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/40 hover:bg-red-500 transition disabled:cursor-not-allowed disabled:opacity-70">
              <?= e(kc_t('meal.form.submit')) ?>
            </button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <footer class="border-t border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 text-sm text-slate-400 flex flex-col md:flex-row gap-3 items-center justify-between">
      <p>© <span id="year"></span> <?= e(kc_t('common.brand')) ?>. <?= e(kc_t('common.footer.rights')) ?> - <?= e(kc_t('common.footer.developed_by')) ?> <a href="https://smartappli.eu">SmartAppli&reg;</a></p>
      <nav class="flex gap-4">
        <a href="/mentions-legales.php?lang=<?= e($locale) ?>" class="hover:text-orange-600"><?= e(kc_t('common.footer.legal')) ?></a>
        <a href="/politique-confidentialite.php?lang=<?= e($locale) ?>" class="hover:text-orange-600"><?= e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>

  <script>
    function setTheme(mode){
      const root = document.documentElement;
      const isLight = mode === 'light';
      root.classList.toggle('light', isLight);
      try { localStorage.setItem('themeMode', mode); } catch(e){}
      const label = document.getElementById('themeLabel');
      const labelM = document.getElementById('themeLabelMobile');
      const sun = document.getElementById('iconSun');
      const moon = document.getElementById('iconMoon');
      if(label) label.textContent = isLight ? 'Light' : 'Dark';
      if(labelM) labelM.textContent = isLight ? 'Light' : 'Dark';
      if(sun && moon){
        sun.classList.toggle('hidden', !isLight);
        moon.classList.toggle('hidden', isLight);
      }
    }

    (function(){
      let saved = 'dark';
      try { saved = localStorage.getItem('themeMode') || 'dark'; } catch(e){}
      setTheme(saved);

      const themeBtn = document.getElementById('themeToggle');
      const themeBtnM = document.getElementById('themeToggleMobile');
      const toggleTheme = function(){
        setTheme(document.documentElement.classList.contains('light') ? 'dark' : 'light');
      };
      if(themeBtn) themeBtn.addEventListener('click', toggleTheme);
      if(themeBtnM) themeBtnM.addEventListener('click', toggleTheme);

      const yearEl = document.getElementById('year');
      if (yearEl) yearEl.textContent = new Date().getFullYear();

      const menuBtn = document.getElementById('menuBtn');
      const mobileNav = document.getElementById('mobileNav');
      if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', function () { mobileNav.classList.toggle('hidden'); });
      }

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
    })();
  </script>
</body>
</html>
