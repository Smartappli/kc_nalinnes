<?php
declare(strict_types=1);

require_once __DIR__ . '/i18n.php';

function kc_e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function kc_render_static_page(string $slug, string $canonicalPath, array $sectionKeys): void {
    $locale = kc_current_locale();
    $title = kc_t($slug . '.meta.title');
    $description = kc_t($slug . '.meta.description');
    ?>
<!doctype html>
<html lang="<?= kc_e($locale) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= kc_e($title) ?></title>
  <meta name="description" content="<?= kc_e($description) ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="https://kc-nalinnes.be/<?= kc_e(ltrim($canonicalPath, '/')) ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-950/90">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4">
      <a href="<?= kc_e(kc_localized_url($locale, '/')) ?>" class="font-semibold">KC Nalinnes</a>
      <nav class="flex flex-wrap items-center gap-4 text-sm">
        <a href="/index.php#horaires" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.schedule')) ?></a>
        <a href="/index.php#tarifs" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.prices')) ?></a>
        <a href="/index.php#calendrier" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.calendar')) ?></a>
        <a href="/index.php#contact" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.contact')) ?></a>
        <a href="<?= kc_e(kc_localized_url($locale, '/membres.php')) ?>" class="rounded-full bg-red-600 px-4 py-2 font-semibold text-white"><?= kc_e(kc_t('common.nav.members')) ?></a>
      </nav>
      <?= kc_language_switcher('flex gap-2') ?>
    </div>
  </header>

  <main class="mx-auto max-w-5xl px-4 py-12">
    <p class="text-sm font-semibold uppercase tracking-wide text-sky-300"><?= kc_e(kc_t($slug . '.kicker')) ?></p>
    <h1 class="mt-3 text-3xl font-extrabold md:text-5xl"><?= kc_e(kc_t($slug . '.heading')) ?></h1>
    <p class="mt-5 max-w-3xl text-lg text-slate-300"><?= kc_e(kc_t($slug . '.intro')) ?></p>

    <div class="mt-12 grid gap-6 md:grid-cols-2">
      <?php foreach ($sectionKeys as $sectionKey): ?>
        <section class="rounded-lg border border-slate-800 bg-slate-900/60 p-6">
          <h2 class="text-xl font-bold"><?= kc_e(kc_t($slug . '.' . $sectionKey . '.title')) ?></h2>
          <p class="mt-3 text-slate-300"><?= kc_e(kc_t($slug . '.' . $sectionKey . '.body')) ?></p>
        </section>
      <?php endforeach; ?>
    </div>
  </main>

  <footer class="border-t border-slate-800">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
      <p>© <span id="year"></span> KC Nalinnes. <?= kc_e(kc_t('common.footer.rights')) ?></p>
      <nav class="flex gap-4">
        <a href="<?= kc_e(kc_localized_url($locale, '/mentions-legales.php')) ?>" class="hover:text-orange-400"><?= kc_e(kc_t('common.footer.legal')) ?></a>
        <a href="<?= kc_e(kc_localized_url($locale, '/politique-confidentialite.php')) ?>" class="hover:text-orange-400"><?= kc_e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>
  <script>document.getElementById('year').textContent = String(new Date().getFullYear());</script>
</body>
</html>
    <?php
}

function kc_render_home_page(): void {
    $locale = kc_current_locale();
    $sections = [
        'schedule',
        'prices',
        'calendar',
        'instructors',
        'documents',
        'news',
        'contact',
    ];
    ?>
<!doctype html>
<html lang="<?= kc_e($locale) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= kc_e(kc_t('home.meta.title')) ?></title>
  <meta name="description" content="<?= kc_e(kc_t('home.meta.description')) ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="https://kc-nalinnes.be/">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-950/90">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4">
      <a href="<?= kc_e(kc_localized_url($locale, '/')) ?>" class="font-semibold"><?= kc_e(kc_t('common.brand')) ?></a>
      <nav class="flex flex-wrap items-center gap-4 text-sm">
        <a href="#schedule" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.schedule')) ?></a>
        <a href="#prices" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.prices')) ?></a>
        <a href="#calendar" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.calendar')) ?></a>
        <a href="#contact" class="hover:text-sky-400"><?= kc_e(kc_t('common.nav.contact')) ?></a>
        <a href="<?= kc_e(kc_localized_url($locale, '/membres.php')) ?>" class="rounded-full bg-red-600 px-4 py-2 font-semibold text-white"><?= kc_e(kc_t('common.nav.members')) ?></a>
      </nav>
      <?= kc_language_switcher('flex gap-2') ?>
    </div>
  </header>

  <main>
    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-orange-300"><?= kc_e(kc_t('home.hero.kicker')) ?></p>
        <h1 class="mt-4 text-4xl font-extrabold md:text-6xl"><?= kc_e(kc_t('home.hero.title')) ?></h1>
        <p class="mt-6 max-w-3xl text-lg text-slate-300"><?= kc_e(kc_t('home.hero.body')) ?></p>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="#contact" class="rounded-xl bg-red-600 px-5 py-3 font-semibold text-white hover:bg-red-500"><?= kc_e(kc_t('home.hero.cta_contact')) ?></a>
          <a href="<?= kc_e(kc_localized_url($locale, '/reservation-repas.php')) ?>" class="rounded-xl border border-slate-600 px-5 py-3 font-semibold text-slate-100 hover:border-sky-400 hover:text-sky-300"><?= kc_e(kc_t('home.hero.cta_meal')) ?></a>
        </div>
      </div>

      <aside class="rounded-lg border border-slate-800 bg-slate-900/60 p-6">
        <h2 class="text-2xl font-bold"><?= kc_e(kc_t('home.quick.title')) ?></h2>
        <dl class="mt-5 space-y-4 text-sm">
          <div>
            <dt class="font-semibold text-slate-100"><?= kc_e(kc_t('home.quick.place_label')) ?></dt>
            <dd class="mt-1 text-slate-300"><?= kc_e(kc_t('home.quick.place')) ?></dd>
          </div>
          <div>
            <dt class="font-semibold text-slate-100"><?= kc_e(kc_t('home.quick.audience_label')) ?></dt>
            <dd class="mt-1 text-slate-300"><?= kc_e(kc_t('home.quick.audience')) ?></dd>
          </div>
          <div>
            <dt class="font-semibold text-slate-100"><?= kc_e(kc_t('home.quick.trial_label')) ?></dt>
            <dd class="mt-1 text-slate-300"><?= kc_e(kc_t('home.quick.trial')) ?></dd>
          </div>
        </dl>
      </aside>
    </section>

    <section class="border-y border-slate-800 bg-slate-900/40">
      <div class="mx-auto grid max-w-7xl gap-6 px-4 py-12 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($sections as $section): ?>
          <article id="<?= kc_e($section) ?>" class="rounded-lg border border-slate-800 bg-slate-950/60 p-6">
            <h2 class="text-xl font-bold"><?= kc_e(kc_t('home.' . $section . '.title')) ?></h2>
            <p class="mt-3 text-slate-300"><?= kc_e(kc_t('home.' . $section . '.body')) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="border-t border-slate-800">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
      <p>© <span id="year"></span> <?= kc_e(kc_t('common.brand')) ?>. <?= kc_e(kc_t('common.footer.rights')) ?> - <?= kc_e(kc_t('common.footer.developed_by')) ?> <a href="https://smartappli.eu">SmartAppli&reg;</a></p>
      <nav class="flex gap-4">
        <a href="<?= kc_e(kc_localized_url($locale, '/mentions-legales.php')) ?>" class="hover:text-orange-400"><?= kc_e(kc_t('common.footer.legal')) ?></a>
        <a href="<?= kc_e(kc_localized_url($locale, '/politique-confidentialite.php')) ?>" class="hover:text-orange-400"><?= kc_e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>
  <script>document.getElementById('year').textContent = String(new Date().getFullYear());</script>
</body>
</html>
    <?php
}
