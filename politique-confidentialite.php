<?php
declare(strict_types=1);

require __DIR__ . '/includes/i18n.php';

$locale = kc_current_locale();

function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$sections = [
    ['legal.privacy.section.controller.title', ['legal.privacy.section.controller.p1', 'legal.privacy.section.controller.p2']],
    ['legal.privacy.section.data.title', ['legal.privacy.section.data.p1', 'legal.privacy.section.data.p2']],
    ['legal.privacy.section.purposes.title', ['legal.privacy.section.purposes.p1', 'legal.privacy.section.purposes.p2']],
    ['legal.privacy.section.bases.title', ['legal.privacy.section.bases.p1', 'legal.privacy.section.bases.p2']],
    ['legal.privacy.section.retention.title', ['legal.privacy.section.retention.p1', 'legal.privacy.section.retention.p2']],
    ['legal.privacy.section.recipients.title', ['legal.privacy.section.recipients.p1', 'legal.privacy.section.recipients.p2']],
    ['legal.privacy.section.transfers.title', ['legal.privacy.section.transfers.p1']],
    ['legal.privacy.section.rights.title', ['legal.privacy.section.rights.p1', 'legal.privacy.section.rights.p2']],
    ['legal.privacy.section.cookies.title', ['legal.privacy.section.cookies.p1', 'legal.privacy.section.cookies.p2']],
    ['legal.privacy.section.update.title', ['legal.privacy.section.update.p1']],
];
?>
<!doctype html>
<html lang="<?= e($locale) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(kc_t('legal.privacy.meta.title')) ?></title>
  <meta name="description" content="<?= e(kc_t('legal.privacy.meta.description')) ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="https://kc-nalinnes.be/politique-confidentialite.php">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-950/90">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4">
      <a href="<?= e(kc_localized_url($locale, '/')) ?>" class="font-semibold">KC Nalinnes</a>
      <nav class="flex flex-wrap items-center gap-4 text-sm">
        <a href="/index.php#horaires" class="hover:text-sky-400"><?= e(kc_t('common.nav.schedule')) ?></a>
        <a href="/index.php#calendrier" class="hover:text-sky-400"><?= e(kc_t('common.nav.calendar')) ?></a>
        <a href="/index.php#tarifs" class="hover:text-sky-400"><?= e(kc_t('common.nav.prices')) ?></a>
        <a href="/index.php#contact" class="hover:text-sky-400"><?= e(kc_t('common.nav.contact')) ?></a>
        <a href="<?= e(kc_localized_url($locale, '/membres.php')) ?>" class="rounded-full bg-red-600 px-4 py-2 font-semibold text-white"><?= e(kc_t('common.nav.members')) ?></a>
      </nav>
      <?= kc_language_switcher('flex gap-2') ?>
    </div>
  </header>

  <main class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-extrabold md:text-4xl"><?= e(kc_t('legal.privacy.heading')) ?></h1>
    <p class="mt-3 text-sm text-slate-400"><?= e(kc_t('legal.privacy.updated')) ?></p>

    <div class="mt-10 space-y-10">
      <?php foreach ($sections as [$titleKey, $paragraphKeys]): ?>
        <section>
          <h2 class="text-2xl font-bold"><?= e(kc_t($titleKey)) ?></h2>
          <div class="mt-3 space-y-3 text-slate-300">
            <?php foreach ($paragraphKeys as $paragraphKey): ?>
              <p><?= e(kc_t($paragraphKey)) ?></p>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
  </main>

  <footer class="border-t border-slate-800">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
      <p>© <span id="year"></span> KC Nalinnes. <?= e(kc_t('common.footer.rights')) ?></p>
      <nav class="flex gap-4">
        <a href="<?= e(kc_localized_url($locale, '/mentions-legales.php')) ?>" class="hover:text-orange-400"><?= e(kc_t('common.footer.legal')) ?></a>
        <a href="<?= e(kc_localized_url($locale, '/politique-confidentialite.php')) ?>" class="hover:text-orange-400"><?= e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>
  <script>document.getElementById('year').textContent = String(new Date().getFullYear());</script>
</body>
</html>
