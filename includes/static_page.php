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
<html translate="no" lang="<?= kc_e($locale) ?>">
<head>
  <meta charset="utf-8">
  <meta name="google" content="notranslate">
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
