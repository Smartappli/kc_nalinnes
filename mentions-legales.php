<?php
declare(strict_types=1);

require __DIR__ . '/includes/i18n.php';

$locale = kc_current_locale();

function e(string $text): string {
  return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?><!doctype html>
<html<?= kc_translate_guard_attr($locale) ?> lang="<?= e($locale) ?>">
<head>
  <meta charset="utf-8" />
  <?= kc_google_notranslate_meta($locale) ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Karaté Shotokan à Nalinnes — KC Nalinnes</title>

  <meta name="description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="https://kc-nalinnes.be/mentions-legales.php" />

  <!-- Open Graph -->
  <meta property="og:title" content="Karaté Shotokan à Nalinnes — KC Nalinnes" />
  <meta property="og:description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://kc-nalinnes.be/mentions-legales.php" />
  <meta property="og:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="Karaté Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Karaté Shotokan à Nalinnes — KC Nalinnes" />
  <meta name="twitter:description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta name="twitter:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />

  <meta name="theme-color" content="#0f172a" />

  <!-- PWA -->
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Karaté Club Nalinnes">
  <meta name="mobile-web-app-capable" content="yes">

  <link rel="icon" href="/favicon.ico" />

  <!-- Fonts / CSS / JS existants -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Styles globaux + thème light -->
  <style>
    :root {
      --accent: #0ea5e9;
    }

    body {
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu,
        Cantarell, Noto Sans, Arial, "Apple Color Emoji", "Segoe UI Emoji";
    }

    .glass {
      backdrop-filter: blur(6px);
      background: rgba(15, 23, 42, 0.6);
    }

    .section {
      scroll-margin-top: 6rem;
    }

    /* ------- Thème LIGHT : overrides quand <html class="light"> ------- */

    /* Fond général + texte */
    html.light body {
      background-color: #f8fafc; /* slate-50 */
      color: #020617;           /* slate-950 */
    }

    html.light main {
      color: #0f172a; /* slate-900 */
    }

    html.light h1,
    html.light h2,
    html.light h3 {
      color: #020617;
    }

    /* Texte qui utilisait des text-slate-100 en dark */
    html.light .text-slate-100,
    html.light .text-slate-100\/90,
    html.light .text-slate-100\/80 {
      color: #0f172a; /* texte foncé sur fond clair */
    }

    html.light .text-slate-400 {
      color: #64748b; /* slate-500 approx */
    }

    html.light .text-slate-500 {
      color: #64748b;
    }

    /* Header / nav en light */
    html.light .glass {
      background: rgba(255, 255, 255, 0.85);
      border-bottom: 1px solid rgba(148, 163, 184, 0.6); /* slate-400 */
    }

    html.light header {
      background-color: transparent;
    }

    html.light #mobileNav {
      background-color: #f8fafc;
      border-color: #e2e8f0; /* slate-200 */
    }

    /* Boutons de switch de thème */
    html.light #themeToggle,
    html.light #themeToggleMobile {
      border-color: #cbd5f5; /* slate-200/300 */
      background-color: #e5e7eb; /* slate-200 */
      color: #020617;
    }

    html.light #themeToggle:hover,
    html.light #themeToggleMobile:hover {
      border-color: var(--accent);
    }

    /* Cartes / blocs foncés en dark -> plus clairs en light */
    html.light .bg-slate-900\/60 {
      background-color: rgba(241, 245, 249, 0.95); /* slate-100 */
    }

    html.light .border-slate-800 {
      border-color: #e2e8f0; /* slate-200 */
    }

    /* Fond body / header utilisaient bg-slate-950 / bg-slate-950/70 */
    html.light .bg-slate-950,
    html.light .bg-slate-950\/70 {
      background-color: #f8fafc;
    }

    /* Liens du footer en light : lisibles mais visibles au survol */
    html.light a:hover {
      color: #0ea5e9;
    }

    /* Mobile : lien "Essai gratuit" */
    html.light a.text-red-400 {
      color: #b91c1c; /* red-700 approx */
    }
  </style>

  <script>
    // Theme boot : applique le thème enregistré avant le paint
    (function(){
      try{
        var saved = localStorage.getItem('themeMode');
        if(saved === 'light'){ document.documentElement.classList.add('light'); }
      }catch(e){}
    })();
  </script>

  <!-- Matomo -->
  <script>
    var _paq = window._paq = window._paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u="//stats.smartappli.eu/";
      _paq.push(['setTrackerUrl', u+'matomo.php']);
      _paq.push(['setSiteId', '2']);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
      g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
    })();
  </script>
  <!-- End Matomo Code -->
</head>
<body class="bg-slate-950 text-slate-100">

  <!-- Header -->
  <header class="fixed inset-x-0 top-0 z-50 bg-slate-950/70 glass">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <a href="/" class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
            <img
              src="./assets/logo-kc-nalinnes1.png"
              alt="KC Nalinnes - Frank Duchesne"
              class="h-full w-full object-contain"
            />
          </div>

          <div class="h-10 w-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
            <img
              src="./assets/logo-kc-nalinnes2.png"
              alt="KC Nalinnes - Olivier Lowie"
              class="h-full w-full object-contain"
            />
          </div>
          <span class="font-semibold">KC Nalinnes</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="index.php#horaires" class="hover:text-sky-400 transition-colors">Horaires</a>
          <a href="index.php#calendrier" class="hover:text-sky-400 transition-colors">Calendrier</a>
          <a href="index.php#tarifs" class="hover:text-sky-400 transition-colors">Tarifs</a>
          <a href="index.php#coach" class="hover:text-sky-400 transition-colors">Instructeurs</a>
          <a href="index.php#actus" class="hover:text-sky-400 transition-colors">Actus</a>
          <a href="index.php#documents" class="hover:text-sky-400 transition-colors">Documents</a>
          <a href="index.php#contact" class="hover:text-sky-400 transition-colors">Contact</a>
          <a href="membres.php"
            class="ml-2 rounded-full bg-red-600 px-4 py-2 font-semibold text-white shadow-md shadow-red-900/40 hover:bg-red-500 hover:translate-y-[1px] transition">
            Membres
          </a>
          <?= kc_language_switcher('ml-2 inline-flex') ?>

          <!-- Bouton Light/Dark -->
          <button id="themeToggle" class="ml-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                  aria-pressed="false" aria-label="Basculer le thème">
            <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79L3.17 4.83l1.79 1.8 1.8-1.79zm10.48 0l1.8-1.79 1.79 1.78-1.79 1.8-1.8-1.79zM12 4V1h-0v3h0zm0 19v-3h0v3h0zM4 12H1v0h3v0zm19 0h-3v0h3v0zM6.76 19.16l-1.8 1.79-1.79-1.78 1.79-1.8 1.8 1.79zM17.24 19.16l1.8 1.79 1.79-1.78-1.79-1.8-1.8 1.79zM12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/></svg>
            <span id="themeLabel">Dark</span>
          </button>
        </nav>

        <button id="menuBtn" class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-md bg-slate-800">☰</button>
      </div>
    </div>

    <nav id="mobileNav" class="md:hidden hidden border-t border-slate-800">
      <div class="mx-auto max-w-7xl px-4 py-3 space-y-2">
        <a href="index.php#horaires" class="block">Horaires</a>
        <a href="index.php#calendrier" class="block">Calendrier</a>
        <a href="index.php#tarifs" class="block">Tarifs</a>
        <a href="index.php#coach" class="block">Instructeurs</a>
        <a href="index.php#actus" class="block">Actus</a>
        <a href="index.php#documents" class="block">Documents</a>
        <a href="index.php#contact" class="block">Contact</a>
        <a href="membres.php" class="block font-semibold text-red-400">Membres</a>
        <?= kc_language_switcher('mt-2 block') ?>
        <!-- Bouton Light/Dark mobile -->
        <button id="themeToggleMobile" class="mt-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                aria-pressed="false" aria-label="Basculer le thème">
          🌗 <span id="themeLabelMobile">Dark</span>
        </button>
      </div>
    </nav>
  </header>

  <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 pt-20 md:pt-24">
    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
      Mentions légales
    </h1>

    <p class="text-sm text-slate-500 mb-10">
      Dernière mise à jour : 07 décembre 2025
    </p>

    <!-- 1. Éditeur du site -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">1. Éditeur du site</h2>
      <p class="mb-2">
        Le présent site internet est édité par :
      </p>
      <p class="mb-1">
        <strong>KC Nalinnes</strong><br>
        Association sportive (club de karaté Shotokan)<br>
        Adresse : <em>18 rue des Monts</em><br>
        Code postal & ville : <em>6120 Nalinnes</em><br>
        Belgique
      </p>
      <p class="mb-1">
        KCN : <em>5230</em>
      </p>
      <p class="mb-1">
        Téléphone : <em>Olivier Lowie: +32 497 25 12 14 / Frank Duchesne: +32 488 09 50 27</em><br>
        E-mail de contact : <a href="mailto:info@kcnalinnes.be" class="text-orange-600 hover:underline">info@kc-nalinnes.be</a>
      </p>
      <p class="mb-1">
        Responsable de la publication : <em>Matyas SIMON, Président du club</em>
      </p>
    </section>

    <!-- 2. Hébergement -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">2. Hébergement</h2>
      <p class="mb-2">
        Le site est hébergé par :
      </p>
      <p class="mb-1">
        <strong>OVH Cloud</strong><br>
        Adresse : <em>2 rue Kellermann à 59100 Roubaix (France)</em><br>
        Site web : <em>https://www.ovhcloud.com</em>
      </p>
    </section>

    <!-- 3. Propriété intellectuelle -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">3. Propriété intellectuelle</h2>
      <p class="mb-2">
        L’ensemble du contenu présent sur ce site (textes, images, logos, éléments graphiques, structure, etc.) est, sauf mention
        contraire, la propriété exclusive de <strong>KC Nalinnes</strong> ou utilisé avec l’autorisation de leurs titulaires
        de droits.
      </p>
      <p class="mb-2">
        Toute reproduction, représentation, modification, diffusion ou exploitation, totale ou partielle, du site ou de l’un de ses
        éléments, par quelque procédé que ce soit, sans autorisation écrite préalable, est strictement interdite et pourrait
        constituer une contrefaçon.
      </p>
    </section>

    <!-- 4. Responsabilité -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">4. Responsabilité</h2>
      <p class="mb-2">
        Les informations fournies sur ce site le sont à titre indicatif et général. Malgré tout le soin apporté à leur exactitude,
        <strong>KC Nalinnes</strong> ne peut garantir l’absence totale d’erreurs ou d’omissions.
      </p>
      <p class="mb-2">
        Le club ne saurait être tenu responsable des dommages directs ou indirects résultant de l’utilisation du site ou de
        l’impossibilité d’y accéder, y compris en cas de suspension temporaire pour maintenance ou mise à jour.
      </p>
    </section>

    <!-- 5. Liens externes -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">5. Liens hypertextes</h2>
      <p class="mb-2">
        Le site peut contenir des liens vers d’autres sites internet. <strong>KC Nalinnes</strong> n’exerce aucun contrôle sur
        le contenu de ces sites et ne saurait être tenu responsable de leur disponibilité, de leur contenu ou de tout dommage
        pouvant résulter de leur utilisation.
      </p>
    </section>

    <!-- 6. Données personnelles & cookies (renvoi vers politique) -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">6. Données personnelles et cookies</h2>
      <p class="mb-2">
        Pour toute information concernant la collecte et le traitement de vos données personnelles, ainsi que l’utilisation des
        cookies sur ce site, veuillez consulter notre
        <a href="/politique-confidentialite.php" class="text-orange-600 hover:underline">
          Politique de confidentialité
        </a>.
      </p>
    </section>

    <!-- 7. Droit applicable -->
    <section class="mb-10">
      <h2 class="text-2xl font-bold mb-3">7. Droit applicable et juridiction compétente</h2>
      <p class="mb-2">
        Les présentes mentions légales sont soumises au droit belge. En cas de litige et à défaut de résolution amiable,
        les tribunaux de l’arrondissement judiciaire compétent pour le siège de <strong>KC Nalinnes</strong> seront seuls compétents.
      </p>
    </section>

    <div class="mt-12 text-sm text-slate-500 border-t border-slate-800 pt-6 flex flex-wrap gap-4 justify-between">
      <p>© <span id="year"></span> KC Nalinnes. Tous droits réservés.</p>
      <div class="flex gap-4">
        <a href="/mentions-legales.php" class="hover:text-orange-400">Mentions légales</a>
        <a href="/politique-confidentialite.php" class="hover:text-orange-400">Politique de confidentialité</a>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // --- Thème Light/Dark -----------------------------------------
      function setTheme(mode){
        const root = document.documentElement;
        const isLight = mode === 'light';
        root.classList.toggle('light', isLight);
        try { localStorage.setItem('themeMode', mode); } catch(e){}
        const label = document.getElementById('themeLabel');
        const labelM = document.getElementById('themeLabelMobile');
        const sun = document.getElementById('iconSun'), moon = document.getElementById('iconMoon');
        if(label) label.textContent = isLight ? 'Light' : 'Dark';
        if(labelM) labelM.textContent = isLight ? 'Light' : 'Dark';
        if(sun && moon){ sun.classList.toggle('hidden', !isLight); moon.classList.toggle('hidden', isLight); }
      }

      (function(){
        let saved = 'dark';
        try { saved = localStorage.getItem('themeMode') || 'dark'; } catch(e){}
        setTheme(saved);
      })();

      const themeBtn = document.getElementById('themeToggle');
      const themeBtnM = document.getElementById('themeToggleMobile');
      function toggleTheme(){ setTheme(document.documentElement.classList.contains('light') ? 'dark' : 'light'); }
      if(themeBtn) themeBtn.addEventListener('click', toggleTheme);
      if(themeBtnM) themeBtnM.addEventListener('click', toggleTheme);

      // --- UI divers ------------------------------------------------
      try {
        const yearEl = document.getElementById('year');
        if (yearEl) yearEl.textContent = new Date().getFullYear();
        const menuBtn = document.getElementById('menuBtn');
        const mobileNav = document.getElementById('mobileNav');
        if (menuBtn && mobileNav) {
          menuBtn.addEventListener('click', function () { mobileNav.classList.toggle('hidden'); });
        }
      } catch (e) { console.error('Erreur UI:', e); }

    });     

    // Enregistrement du Service Worker (PWA)
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function () {
        navigator.serviceWorker.register('/service-worker.js')
          .then(function (registration) {
            console.log('ServiceWorker enregistré avec succès :', registration.scope);
          })
          .catch(function (error) {
            console.error('Échec de l\'enregistrement du ServiceWorker :', error);
          });
      });
    }
  </script>
</body>
</html>
