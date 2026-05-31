<?php
declare(strict_types=1);

require __DIR__ . '/includes/i18n.php';

$locale = kc_current_locale();

function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$sections = [
    ['horaires', 'home.schedule.title', 'home.schedule.body'],
    ['tarifs', 'home.prices.title', 'home.prices.body'],
    ['calendrier', 'home.calendar.title', 'home.calendar.body'],
    ['coach', 'home.instructors.title', 'home.instructors.body'],
    ['documents', 'home.documents.title', 'home.documents.body'],
    ['actus', 'home.news.title', 'home.news.body'],
];
?>
<!doctype html>
<html lang="<?= e($locale) ?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(kc_t('home.meta.title')) ?></title>
  <meta name="description" content="<?= e(kc_t('home.meta.description')) ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="https://kc-nalinnes.be/">
  <meta property="og:title" content="<?= e(kc_t('home.meta.title')) ?>">
  <meta property="og:description" content="<?= e(kc_t('home.meta.description')) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://kc-nalinnes.be/">
  <meta property="og:image" content="https://kc-nalinnes.be/assets/og-karate.jpg">
  <meta property="og:locale" content="<?= e($locale) ?>">
  <meta property="og:site_name" content="Karaté Club Nalinnes">
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="icon" href="/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="sticky top-0 z-50 border-b border-slate-800 bg-slate-950/90">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4">
      <a href="<?= e(kc_localized_url($locale, '/')) ?>" class="flex items-center gap-3 font-semibold">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-950">KC</span>
        KC Nalinnes
      </a>
      <nav class="flex flex-wrap items-center gap-4 text-sm">
        <a href="#horaires" class="hover:text-sky-400"><?= e(kc_t('common.nav.schedule')) ?></a>
        <a href="#tarifs" class="hover:text-sky-400"><?= e(kc_t('common.nav.prices')) ?></a>
        <a href="#calendrier" class="hover:text-sky-400"><?= e(kc_t('common.nav.calendar')) ?></a>
        <a href="#coach" class="hover:text-sky-400"><?= e(kc_t('common.nav.instructors')) ?></a>
        <a href="#contact" class="hover:text-sky-400"><?= e(kc_t('common.nav.contact')) ?></a>
        <a href="<?= e(kc_localized_url($locale, '/membres.php')) ?>" class="rounded-full bg-red-600 px-4 py-2 font-semibold text-white"><?= e(kc_t('common.nav.members')) ?></a>
      </nav>
      <?= kc_language_switcher('flex gap-2') ?>
    </div>
  </header>

  <main>
    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 md:grid-cols-[1.2fr_0.8fr] md:items-center">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-sky-300"><?= e(kc_t('home.hero.kicker')) ?></p>
        <h1 class="mt-4 text-4xl font-extrabold md:text-6xl"><?= e(kc_t('home.hero.title')) ?></h1>
        <p class="mt-6 max-w-2xl text-lg text-slate-300"><?= e(kc_t('home.hero.body')) ?></p>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="#contact" class="rounded-xl bg-red-600 px-5 py-3 font-semibold text-white"><?= e(kc_t('home.hero.cta_contact')) ?></a>
          <a href="<?= e(kc_localized_url($locale, '/reservation-repas.php')) ?>" class="rounded-xl border border-slate-700 px-5 py-3 font-semibold text-slate-100"><?= e(kc_t('home.hero.cta_meal')) ?></a>
        </div>
      </div>
      <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="text-xl font-bold"><?= e(kc_t('home.quick.title')) ?></h2>
        <dl class="mt-5 space-y-4 text-sm">
          <div><dt class="font-semibold text-sky-300"><?= e(kc_t('home.quick.place_label')) ?></dt><dd class="mt-1 text-slate-300"><?= e(kc_t('home.quick.place')) ?></dd></div>
          <div><dt class="font-semibold text-sky-300"><?= e(kc_t('home.quick.audience_label')) ?></dt><dd class="mt-1 text-slate-300"><?= e(kc_t('home.quick.audience')) ?></dd></div>
          <div><dt class="font-semibold text-sky-300"><?= e(kc_t('home.quick.trial_label')) ?></dt><dd class="mt-1 text-slate-300"><?= e(kc_t('home.quick.trial')) ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-6 px-4 pb-12 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($sections as [$id, $titleKey, $bodyKey]): ?>
        <article id="<?= e($id) ?>" class="scroll-mt-24 rounded-lg border border-slate-800 bg-slate-900/60 p-6">
          <h2 class="text-2xl font-bold"><?= e(kc_t($titleKey)) ?></h2>
          <p class="mt-3 text-slate-300"><?= e(kc_t($bodyKey)) ?></p>
        </article>
      <?php endforeach; ?>
    </section>

    <section id="contact" class="mx-auto max-w-3xl px-4 pb-16">
      <div class="rounded-lg border border-slate-800 bg-slate-900/60 p-6">
        <h2 class="text-2xl font-bold"><?= e(kc_t('home.contact.title')) ?></h2>
        <p class="mt-3 text-slate-300"><?= e(kc_t('home.contact.body')) ?></p>
        <form name="contact" method="POST" action="/contact.php" class="mt-6 space-y-4">
          <input type="hidden" name="lang" value="<?= e($locale) ?>">
          <p class="hidden"><label>Website <input name="website"></label></p>
          <div>
            <label class="block text-sm font-semibold" for="name"><?= e(kc_t('home.contact.name')) ?></label>
            <input id="name" name="name" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-semibold" for="email"><?= e(kc_t('home.contact.email')) ?></label>
            <input id="email" type="email" name="email" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-semibold" for="message"><?= e(kc_t('home.contact.message')) ?></label>
            <textarea id="message" name="message" rows="5" required class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2"></textarea>
          </div>
          <button class="rounded-xl bg-red-600 px-5 py-3 font-semibold text-white"><?= e(kc_t('home.contact.submit')) ?></button>
        </form>
      </div>
    </section>
  </main>

  <footer class="border-t border-slate-800">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
      <p>© <span id="year"></span> KC Nalinnes. <?= e(kc_t('common.footer.rights')) ?> - <?= e(kc_t('common.footer.developed_by')) ?> <a href="https://smartappli.eu">SmartAppli&reg;</a></p>
      <nav class="flex gap-4">
        <a href="<?= e(kc_localized_url($locale, '/mentions-legales.php')) ?>" class="hover:text-orange-400"><?= e(kc_t('common.footer.legal')) ?></a>
        <a href="<?= e(kc_localized_url($locale, '/politique-confidentialite.php')) ?>" class="hover:text-orange-400"><?= e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>
  <script>document.getElementById('year').textContent = String(new Date().getFullYear());</script>
</body>
</html>
