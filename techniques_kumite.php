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

  <title><?= e(kc_t('page.techniques_kumite.meta.title')) ?></title>

  <meta name="description" content="<?= e(kc_t('page.techniques_kumite.meta.description')) ?>" />
  <meta name="robots" content="index,follow" />
  <!-- Aligné avec og:url + lien du menu -->
  <link rel="canonical" href="https://kc-nalinnes.be/techniques_kumite.php" />

  <!-- Open Graph -->
  <meta property="og:title" content="Techniques de kumité Shotokan — KC Nalinnes" />
  <meta property="og:description" content="Techniques de kumité du karaté Shotokan avec le KC Nalinnes : conseils de garde, distance, déplacements, timing, attaques et contres pour s'entraîner à la maison." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://kc-nalinnes.be/techniques_kumite.php" />
  <meta property="og:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="Karaté Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Techniques de kumité Shotokan — KC Nalinnes" />
  <meta name="twitter:description" content="Techniques de kumité du karaté Shotokan avec le KC Nalinnes : vconseils de garde, distance, déplacements, timing, attaques et contres pour s'entraîner à la maison." />
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

    html.light body {
      background-color: #f8fafc;
      color: #020617;
    }

    html.light main {
      color: #0f172a;
    }

    html.light h1,
    html.light h2,
    html.light h3 {
      color: #020617;
    }

    html.light .text-slate-100,
    html.light .text-slate-100\/90,
    html.light .text-slate-100\/80 {
      color: #0f172a;
    }

    html.light .text-slate-400 {
      color: #64748b;
    }

    html.light .text-slate-500 {
      color: #64748b;
    }

    html.light .glass {
      background: rgba(255, 255, 255, 0.85);
      border-bottom: 1px solid rgba(148, 163, 184, 0.6);
    }

    html.light header {
      background-color: transparent;
    }

    html.light #mobileNav {
      background-color: #f8fafc;
      border-color: #e2e8f0;
    }

    /* Lisibilité du bouton menu en mode Light */
    .light #menuBtn{
      background:#e2e8f0 !important;   /* gris clair */
      color:#0f172a !important;         /* texte bien contrasté */
      border-color:#e2e8f0 !important;
    }
    .light #menuBtn:hover{
      background:#cbd5e1 !important;    /* hover un peu plus foncé */
    }
    .light header.glass #menuBtn{
      /* si le header est translucide, on renforce encore le contraste */
      box-shadow: 0 0 0 1px rgba(148,163,184,.35) inset;
    }

    html.light #themeToggle,
    html.light #themeToggleMobile {
      border-color: #cbd5f5;
      background-color: #e5e7eb;
      color: #020617;
    }

    html.light #themeToggle:hover,
    html.light #themeToggleMobile:hover {
      border-color: var(--accent);
    }

    html.light .bg-slate-900\/60 {
      background-color: rgba(241, 245, 249, 0.95);
    }

    html.light .border-slate-800 {
      border-color: #e2e8f0;
    }

    html.light .bg-slate-950,
    html.light .bg-slate-950\/70 {
      background-color: #f8fafc;
    }

    html.light a:hover {
      color: #0ea5e9;
    }

    html.light a.text-red-400 {
      color: #b91c1c;
    }

    .no-print {
      /* hook pour @media print */
    }

    /* ===== Styles d'impression ===== */
    @media print {
      :root {
        color-scheme: light;
      }

      html, body {
        margin: 0;
        padding: 0;
        background: #ffffff !important;
        color: #000000 !important;
      }

      header,
      #mobileNav,
      #themeToggle,
      #themeToggleMobile,
      #menuBtn {
        display: none !important;
      }

      .no-print {
        display: none !important;
      }

      main {
        max-width: 100% !important;
        padding: 10mm 15mm !important;
      }

      .bg-slate-950,
      .bg-slate-900,
      .bg-slate-900\/60,
      .bg-slate-900\/70,
      .bg-slate-900\/80 {
        background: transparent !important;
      }

      .border-slate-800 {
        border-color: #00000020 !important;
      }

      h1, h2, h3, p, li, a {
        color: #000000 !important;
      }

      * {
        box-shadow: none !important;
        text-shadow: none !important;
        background-image: none !important;
      }

      a[href]::after {
        content: " (" attr(href) ")";
        font-size: 0.8em;
      }

      h1, h2, h3 {
        page-break-after: avoid;
      }
    }

    html.light #printBtn {
      background-color: #e5e7eb !important;
      color: #0f172a !important;
      border-color: #cbd5e1 !important;
    }

    html.light #printBtn:hover {
      background-color: #cbd5e1 !important;
      color: #0f172a !important;
    }

    /* ========= Flocons de neige ========= */

    #snowContainer {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;         /* ne bloque jamais les clics */
      overflow: hidden;
      z-index: 60;                  /* au-dessus du contenu, sous tes modales éventuelles */
    }

    .snowflake {
      position: absolute;
      top: -10%;
      color: #e5f0ff;
      text-shadow: 0 0 4px rgba(15, 23, 42, 0.8);
      opacity: 0.4;
      user-select: none;
      animation-name: snowFall;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
    }

    @keyframes snowFall {
      0% {
        transform: translate3d(0, -100%, 0);
      }
      100% {
        transform: translate3d(20px, 110vh, 0);
      }
    }

    /* Un peu d’adaptation en mode light pour bien voir les flocons */
    html.light .snowflake {
      color: #e5f0ff;
      text-shadow: 0 0 4px rgba(15, 23, 42, 0.5);
    }

    /* Ne pas imprimer la neige */
    @media print {
      #snowContainer {
        display: none !important;
      }
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
          <a href="index.php#tarifs" class="hover:text-sky-400 transition-colors">Tarifs</a>
          <a href="index.php#calendrier" class="hover:text-sky-400 transition-colors">Calendrier</a>
          <a href="index.php#coach" class="hover:text-sky-400 transition-colors">Instructeurs</a>
          <a href="index.php#documents" class="hover:text-sky-400 transition-colors">Documents</a>
          <a href="index.php#actus" class="hover:text-sky-400 transition-colors">Actus</a>
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
        <a href="index.php#tarifs" class="block">Tarifs</a>
        <a href="index.php#calendrier" class="block">Calendrier</a>
        <a href="index.php#coach" class="block">Instructeurs</a>
        <a href="index.php#documents" class="block">Documents</a>
        <a href="index.php#actus" class="block">Actus</a>
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
    <!-- Bouton imprimer (non imprimé grâce à .no-print) -->
    <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-4">
      <div class="flex flex-wrap gap-2 text-sm text-slate-400">
        <span>Accès rapide :</span>
        <a href="#conseils" class="underline underline-offset-4 hover:text-sky-400">Conseils</a>
        <span>•</span>
        <a href="#playlist" class="underline underline-offset-4 hover:text-sky-400">Vidéos</a>
        <span>•</span>
        <a href="#checklist" class="underline underline-offset-4 hover:text-sky-400">Checklist</a>
      </div>

      <button
        id="printBtn"
        type="button"
        onclick="window.print()"
        class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 border border-slate-700 hover:bg-slate-700 transition"
      >
        🖨️ Imprimer la checklist kumité
      </button>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
      <?= e(kc_t('page.techniques_kumite.heading')) ?>
    </h1>

    <p class="text-sm text-slate-400 mb-6">
      Une sélection de vidéos (playlist) pour réviser les bases du <strong>kumité</strong> : garde, distance, déplacements, timing, attaques et contres.
      Utilise-les comme support : rien ne remplace les corrections du dojo.
    </p>

    <!-- Bloc conseils -->
    <section id="conseils" class="mb-10 section">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-6">
        <h2 class="text-xl font-bold mb-3">Conseils pour t'entraîner en sécurité</h2>
        <ul class="list-disc pl-6 space-y-2 text-slate-100/90">
          <li>Échauffe-toi 5–10 minutes (articulations, hanches, genoux, chevilles) avant de commencer.</li>
          <li>Travaille d’abord <strong>lentement</strong> : précision, posture, garde et placement des appuis.</li>
          <li>Le kumité demande de la distance : commence <strong>sans partenaire</strong> (shadow) ou avec une cible (pao, sac) si tu en as une.</li>
          <li>Priorité au contrôle : trajectoires propres, retour en garde, respiration, et pas de “coups à fond” à la maison.</li>
          <li>Note tes questions et demande une correction au cours suivant (timing, maai, hanches, relâchement).</li>
        </ul>
      </div>
    </section>

    <!-- Playlist / Vidéos -->
    <section id="playlist" class="mb-12 section">
      <h2 class="text-2xl font-bold mb-3">Vidéos – techniques de kumité</h2>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Vidéo 1 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/aJS2p5VMk_g"
              title="10 ULTIMATE KUMITE TECHNIQUES 🥋⛩ - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">10 ULTIMATE KUMITE TECHNIQUES</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 2 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/62CDPRtdsEI"
              title="10 KARATE KUMITE TECHNIQUES 🥋⛩️ - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">10 KARATE KUMITE TECHNIQUES</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 3 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/k7j_4N5gIMY"
              title="10 BEST KUMITE COMBOS 🥋⛩ - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">10 BEST KUMITE COMBOS</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 4 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/Jg8654XCELA"
              title="KUMITE TIPS & STRATEGIES 🥋🔥 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">KUMITE TIPS & STRATEGIES</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 5 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/xomfqmZ_Ueo"
              title="KARATE FOOTWORK TUTORIAL 🥋🔥 (Bounce, Shift, & Lateral) - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">KARATE FOOTWORK TUTORIAL</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 6 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/HlXmEHukZJA"
              title="6 BEST KUMITE TECHNIQUES ⛩🥋 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">6 BEST KUMITE TECHNIQUES</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 7 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/l5ZoWU8NwCo"
              title="7 AMAZING KARATE KICK COMBOS 🥋🔥 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">7 AMAZING KARATE KICK COMBOS</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 8 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/nQkmnQ9PRtQ"
              title="URA MAWASHI GERI TRAINING 🥋🔥 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">URA MAWASHI GERI TRAINING</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 9 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/Nh2sNRv2rSw"
              title="KARATE PRACTICE 002 Kumite Combos & Strategies - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">KARATE PRACTICE 002 Kumite Combos & Strategies</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 10 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/ydgZR9V1IO0"
              title="8 KUMITE TIPS & TECHNIQUES FOR KARATE BEGINNERS - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">8 KUMITE TIPS & TECHNIQUES FOR KARATE BEGINNERS</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 11 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/SXH4doeoY4A"
              title="10 KUMITE COMBOS & STRATEGIES 🥋⛩ — ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">10 KUMITE COMBOS & STRATEGIES</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 12 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/Xbloc8FoUjM"
              title="USHIRO GERI TUTORIAL - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">USHIRO GERI TUTORIAL</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 13 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/rUbpB1vXoSA"
              title="GYAKU ZUKI | KUMITE TECHNIQUES🥋⛩ - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">GYAKU ZUKI</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 14 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/o9AkRRSZczc"
              title="🥋 Spinning Hook Kick Tutorial 🥋 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Spinning Hook Kick</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 15 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/qLORONvp2Xw"
              title="Karate Blitz Tutorial 🥋⛩ - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Karate Blitz</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 16 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/sKOAzfANge8"
              title="Mawashi Geri Training for Kumite - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Mawashi Geri</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 17 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/CcqP9krIGcg"
              title="TOP 7 KARATE KUMITE COMBOS! 🥋🔥 - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">TOP 7 KARATE KUMITE COMBOS!</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
        <!-- Vidéo 18 -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/bpM95iX0JD4"
              title="10 KARATE KUMITE COMBINATIONS 🥋⛩️ — - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">10 KARATE KUMITE COMBINATIONS</h3>
          <p class="text-sm text-slate-400"></p>
        </article>
      </div>
    </section>

    <!-- Checklist -->
    <section id="checklist" class="mb-12 section">
      <h2 class="text-2xl font-bold mb-3">Checklist kumité</h2>
      <p class="mb-4 text-slate-100/90">
        Une petite liste simple pour t’auto-corriger pendant tes répétitions.
      </p>

      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-6">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
            <h3 class="font-semibold mb-2">Posture & garde</h3>
            <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
              <li>Pieds stables, genoux souples, centre bas.</li>
              <li>Mains en garde : protège menton et plexus.</li>
              <li>Épaules relâchées, respiration régulière.</li>
            </ul>
          </div>

          <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
            <h3 class="font-semibold mb-2">Distance (maai) & déplacements</h3>
            <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
              <li>Entre/Sort (irimi/taiji) sans croiser les pieds.</li>
              <li>Pas “glissés” : garde le buste stable.</li>
              <li>Reste à une distance qui permet d’attaquer et de sortir.</li>
            </ul>
          </div>

          <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
            <h3 class="font-semibold mb-2">Timing & vitesse</h3>
            <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
              <li>Commence lent → accélère quand c’est propre.</li>
              <li>Garde le contrôle : pas de gestes “jetés”.</li>
              <li>Observe : déclenche sur l’ouverture, pas au hasard.</li>
            </ul>
          </div>

          <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
            <h3 class="font-semibold mb-2">Attaques & retours</h3>
            <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
              <li>Trajectoires directes (kizami, gyaku, mae-geri).</li>
              <li>Hanches actives, mais buste contrôlé.</li>
              <li>Retour en garde immédiat + replacement (zanshin).</li>
            </ul>
          </div>
        </div>

        <div class="mt-6 text-sm text-slate-400">
          <p>
            Vocabulaire utile : <strong>kamae</strong> (garde), <strong>maai</strong> (distance),
            <strong>ashi-sabaki</strong> (déplacements), <strong>sen / go-no-sen</strong> (initiative / contre).
          </p>
        </div>
      </div>
    </section>

    <!-- CTA Essai gratuit -->
    <section class="mt-12 mb-10">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-8">
        <h2 class="text-xl md:text-2xl font-bold mb-3">
          Tu veux pratiquer le kumité au dojo&nbsp;?
        </h2>
        <p class="mb-4 text-slate-100/90">
          Les vidéos sont un bon complément, mais rien ne remplace un entraînement encadré sur le tatami.
          Passe nous voir pour un cours d’essai gratuit.
        </p>
        <a href="/#inscription"
           class="inline-flex items-center justify-center rounded-full px-6 py-3 text-base font-semibold
                  bg-red-600 text-white shadow-lg shadow-red-900/40 hover:bg-red-500 hover:translate-y-[1px]
                  transition">
          Réserver un essai gratuit
        </a>
      </div>
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

      // --- Flocons de neige (actifs du 1/12 au 6/1) ----------------
      try {
        const now = new Date();
        const month = now.getMonth();  // 0 = janvier, 11 = décembre
        const day   = now.getDate();   // 1..31

        const isSnowSeason =
          (month === 11 && day >= 1) ||   // du 1 au 31 décembre
          (month === 0  && day <= 6);     // du 1 au 6 janvier

        if (isSnowSeason) {
          const SNOWFLAKE_COUNT = 60;
          const container = document.createElement('div');
          container.id = 'snowContainer';
          document.body.appendChild(container);

          for (let i = 0; i < SNOWFLAKE_COUNT; i++) {
            const flake = document.createElement('span');
            flake.className = 'snowflake';
            flake.textContent = '❄';

            const size = 0.6 + Math.random() * 1.1;   // 0.6rem à 1.7rem
            const startLeft = Math.random() * 100;    // 0 à 100 vw
            const duration = 8 + Math.random() * 10;  // 8s à 18s
            const delay = -Math.random() * 20;        // démarrage échelonné

            flake.style.left = startLeft + 'vw';
            flake.style.fontSize = size + 'rem';
            flake.style.animationDuration = duration + 's';
            flake.style.animationDelay = delay + 's';
            flake.style.opacity = (0.3 + Math.random() * 0.5).toFixed(2);

            container.appendChild(flake);
          }
        }
      } catch (e) {
        console.error('Erreur neige :', e);
      }

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
