<?php
declare(strict_types=1);

require __DIR__ . '/includes/i18n.php';

$locale = kc_current_locale();

function e(string $text): string {
  return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html<?= kc_translate_guard_attr($locale) ?> lang="<?= e($locale) ?>" class="">
<head>
  <meta charset="utf-8" />
  <?= kc_google_notranslate_meta($locale) ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title><?= e(kc_t('home.meta.title')) ?></title>

  <meta name="description" content="<?= e(kc_t('home.meta.description')) ?>" />
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="https://kc-nalinnes.be/" />

  <!-- Open Graph -->
  <meta property="og:title" content="<?= e(kc_t('home.meta.title')) ?>" />
  <meta property="og:description" content="<?= e(kc_t('home.meta.description')) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://kc-nalinnes.be/" />
  <meta property="og:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="KaratÃ© Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= e(kc_t('home.meta.title')) ?>" />
  <meta name="twitter:description" content="<?= e(kc_t('home.meta.description')) ?>" />
  <meta name="twitter:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />

  <meta name="theme-color" content="#0f172a" />
  <link rel="alternate" type="application/json" href="/ai-summary.json" title="RÃ©sumÃ© factuel KC Nalinnes pour moteurs gÃ©nÃ©ratifs" />
  <link rel="alternate" type="application/ld+json" href="/entity.jsonld" title="EntitÃ© KC Nalinnes en JSON-LD" />
  <link rel="alternate" type="application/json" href="/answers.json" title="RÃ©ponses directes KC Nalinnes" />

  <!-- PWA -->
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="KaratÃ© Club Nalinnes">
  <meta name="mobile-web-app-capable" content="yes">

  <link rel="icon" href="/favicon.ico" />

  <!-- Fonts / CSS / JS existants -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/skeleton.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/theme.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/palette.css" />
  <link rel="stylesheet" href="css/index.css">

  <script>
    // Theme boot : applique le thÃ¨me enregistrÃ© avant le paint
    (function(){
      try{
        var saved = localStorage.getItem('themeMode');
        if(saved === 'light'){ document.documentElement.classList.add('light'); }
      }catch(e){}
    })();
  </script>

  <!-- DonnÃ©es structurÃ©es Local Business / SportsClub -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SportsClub",
    "name": "KC Nalinnes",
    "alternateName": ["KaratÃ© Club Nalinnes", "Karate Club Nalinnes"],
    "url": "https://kc-nalinnes.be/",
    "logo": "https://kc-nalinnes.be/assets/logo-kc-nalinnes1.png",
    "image": "https://kc-nalinnes.be/assets/og-karate.jpg",
    "description": "KaratÃ© Shotokan pour tous niveaux â€” enfants (5+), ados, adultes. Ambiance familiale, instructeurs diplÃ´mÃ©s, progression ceintures, stages & compÃ©titions.",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Rue des Monts 18",
      "postalCode": "6120",
      "addressLocality": "Nalinnes",
      "addressRegion": "Hainaut",
      "addressCountry": "BE"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 50.3239,
      "longitude": 4.4448
    },
    "hasMap": "https://maps.google.com/?q=18%20rue%20des%20Monts%2C%206120%20Nalinnes%2C%20Belgique",
    "email": "info@kc-nalinnes.be",
    "telephone": ["+32 497 25 12 14", "+32 488 09 50 27"],
    "contactPoint": [
      {
        "@type": "ContactPoint",
        "contactType": "information",
        "telephone": "+32 497 25 12 14",
        "email": "info@kc-nalinnes.be",
        "availableLanguage": ["fr-BE"]
      }
    ],
    "areaServed": [
      "Nalinnes",
      "Ham-sur-Heure-Nalinnes",
      "Charleroi",
      "Hainaut"
    ],
    "sport": "KaratÃ© Shotokan",
    "knowsAbout": [
      "KaratÃ© Shotokan",
      "Kata Shotokan",
      "KumitÃ©",
      "Kihon",
      "Dojo Kun",
      "PrÃ©paration aux passages de ceinture"
    ],
    "sameAs": [
      "https://www.facebook.com/KarateClubNalinnes"
    ],
    "priceRange": "EUR",
    "openingHoursSpecification": [
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": "Monday",
        "opens": "17:00",
        "closes": "20:30"
      },
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": "Friday",
        "opens": "18:00",
        "closes": "20:30"
      }
    ]
  }
  </script>
  

  <meta name="geo.region" content="BE-WHT" />
  <meta name="geo.placename" content="Nalinnes" />
  <meta name="geo.position" content="50.3239;4.4448" />
  <meta name="ICBM" content="50.3239, 4.4448" />

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "KaratÃ© Club Nalinnes",
    "url": "https://kc-nalinnes.be/",
    "inLanguage": "fr-BE",
    "about": {
      "@type": "SportsClub",
      "name": "KC Nalinnes",
      "sport": "KaratÃ© Shotokan",
      "address": "Rue des Monts 18, 6120 Nalinnes, Belgique"
    }
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Ã€ partir de quel Ã¢ge peut-on commencer le karatÃ© ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Les cours sont accessibles dÃ¨s 5 ans, ainsi qu'aux adolescents et aux adultes."
        }
      },
      {
        "@type": "Question",
        "name": "Le club propose-t-il des cours d'essai ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, le club propose 3 premiers cours d'essai gratuits."
        }
      },
      {
        "@type": "Question",
        "name": "Quelle discipline est enseignÃ©e ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Le club enseigne le KaratÃ© Shotokan, avec une progression adaptÃ©e Ã  tous les niveaux."
        }
      },
      {
        "@type": "Question",
        "name": "OÃ¹ se trouve le KC Nalinnes ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Le club se trouve au Centre sportif Jules Roulin-Dorvillez, Rue des Monts 18, 6120 Nalinnes, Belgique."
        }
      },
      {
        "@type": "Question",
        "name": "Quels sont les horaires principaux ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Les cours ont lieu principalement le lundi de 17h00 Ã  20h30 et le vendredi de 18h00 Ã  20h30, selon les groupes d'Ã¢ge et de niveau."
        }
      },
      {
        "@type": "Question",
        "name": "Comment contacter le club ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Le club peut Ãªtre contactÃ© par email Ã  info@kc-nalinnes.be, par tÃ©lÃ©phone au +32 497 25 12 14 ou au +32 488 09 50 27."
        }
      }
    ]
  }
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
        <a href="#" class="flex items-center gap-3">
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
          <span class="font-semibold"><?= e(kc_t('common.brand')) ?></span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="#horaires" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.schedule')) ?></a>
          <a href="#tarifs" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.prices')) ?></a>
          <a href="#calendrier" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.calendar')) ?></a>
          <a href="#coach" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.instructors')) ?></a>
          <a href="#documents" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.documents')) ?></a>
          <a href="#actus" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.news')) ?></a>
          <a href="#contact" class="hover:text-sky-400 transition-colors"><?= e(kc_t('common.nav.contact')) ?></a>
          <a href="membres.php"
            class="ml-2 rounded-full bg-red-600 px-4 py-2 font-semibold text-white shadow-md shadow-red-900/40 hover:bg-red-500 hover:translate-y-[1px] transition">
            <?= e(kc_t('common.nav.members')) ?>
          </a>

          <?= kc_language_switcher('ml-2 inline-flex') ?>

          <!-- Bouton Light/Dark -->
          <button id="themeToggle" class="ml-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                  aria-pressed="false" aria-label="<?= e(kc_t('common.theme.toggle')) ?>">
            <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="currentColor"><path d="M6.76 4.84l-1.8-1.79L3.17 4.83l1.79 1.8 1.8-1.79zm10.48 0l1.8-1.79 1.79 1.78-1.79 1.8-1.8-1.79zM12 4V1h-0v3h0zm0 19v-3h0v3h0zM4 12H1v0h3v0zm19 0h-3v0h3v0zM6.76 19.16l-1.8 1.79-1.79-1.78 1.79-1.8 1.8 1.79zM17.24 19.16l1.8 1.79 1.79-1.78-1.79-1.8-1.8 1.79zM12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"/></svg>
            <span id="themeLabel"><?= e(kc_t('home.theme.dark')) ?></span>
          </button>
        </nav>

        <button id="menuBtn"
          class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-md
                bg-slate-800 text-slate-100 border border-transparent
                hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500"
          aria-label="<?= e(kc_t('common.menu.open')) ?>" type="button">â˜°</button>
      </div>
    </div>

    <nav id="mobileNav" class="md:hidden hidden border-t border-slate-800">
      <div class="mx-auto max-w-7xl px-4 py-3 space-y-2">
        <a href="#horaires" class="block"><?= e(kc_t('common.nav.schedule')) ?></a>
        <a href="#tarifs" class="block"><?= e(kc_t('common.nav.prices')) ?></a>
        <a href="#calendrier" class="block"><?= e(kc_t('common.nav.calendar')) ?></a>
        <a href="#coach" class="block"><?= e(kc_t('common.nav.instructors')) ?></a>
        <a href="#documents" class="block"><?= e(kc_t('common.nav.documents')) ?></a>
        <a href="#actus" class="block"><?= e(kc_t('common.nav.news')) ?></a>
        <a href="#contact" class="block"><?= e(kc_t('common.nav.contact')) ?></a>
        <a href="membres.php" class="block font-semibold text-red-400"><?= e(kc_t('common.nav.members')) ?></a>
        <?= kc_language_switcher('mt-2 block') ?>

        <!-- Bouton Light/Dark mobile -->
        <button id="themeToggleMobile" class="mt-2 inline-flex items-center gap-2 rounded-md border border-slate-700 px-3 py-1.5 text-sm hover:border-sky-500"
                aria-pressed="false" aria-label="<?= e(kc_t('common.theme.toggle')) ?>">
          ðŸŒ— <span id="themeLabelMobile"><?= e(kc_t('home.theme.dark')) ?></span>
        </button>
      </div>
    </nav>
  </header>

  <!-- Annonce saison -->
  <section id="passage-grade-repas" class="section bg-slate-900/80 pt-20 pb-5">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="rounded-2xl border border-red-500/40 bg-slate-950/70 p-5 shadow-lg shadow-red-950/20">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300"><?= e(kc_t('meal.hero.kicker')) ?></p>
            <h2 class="mt-2 text-2xl font-extrabold text-slate-100"><?= e(kc_t('meal.hero.title')) ?></h2>
            <p class="mt-2 max-w-3xl text-sm text-slate-300">
              <?= kc_t('home.season.summary_html') ?>
            </p>
            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
              <div class="season-meal-card rounded-xl border border-slate-700/70 bg-slate-900/70 p-3">
                <p class="font-semibold text-slate-100"><?= e(kc_t('home.season.child_meal.title')) ?></p>
                <p class="mt-1 text-slate-300"><?= e(kc_t('home.season.child_meal.body')) ?></p>
              </div>
              <div class="season-meal-card rounded-xl border border-slate-700/70 bg-slate-900/70 p-3">
                <p class="font-semibold text-slate-100"><?= e(kc_t('home.season.adult_meal.title')) ?></p>
                <p class="mt-1 text-slate-300"><?= e(kc_t('home.season.adult_meal.body')) ?></p>
              </div>
            </div>
            <p class="mt-3 text-sm font-semibold text-orange-200">
              <?= kc_t('home.season.deadline_html') ?>
            </p>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row lg:shrink-0">
            <a href="<?= e(kc_localized_url($locale, '/reservation-repas.php')) ?>" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-900/30 hover:bg-red-500 hover:translate-y-[1px] transition">
              <?= e(kc_t('meal.form.submit')) ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Hero -->
  <section class="relative section" id="accueil">
    <!-- Image de fond + dÃ©gradÃ© -->
    <div class="absolute inset-0">
      <img
        src="/assets/hero-karate.jpg"
        alt="<?= e(kc_t('home.hero.image_alt')) ?>"
        class="h-full w-full object-cover opacity-40"
      >
      <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/90 to-slate-900/95"></div>
    </div>

    <div class="relative pt-12 pb-10 lg:pt-14 lg:pb-14">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
        <!-- Colonne texte -->
        <div>
          <p class="text-base uppercase tracking-[0.2em] text-orange-300 mb-3">
            <a href="karate-shotokan.php" class="hover:text-orange-200 transition"><?= e(kc_t('home.hero.kicker')) ?></a>
          </p>

          <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
            <?= e(kc_t('home.hero.title')) ?>
          </h1>

          <p class="mt-4 text-slate-200 max-w-prose">
            <a
              href="karate-shotokan.php"
              class="text-slate-100 hover:text-sky-200 underline underline-offset-4 decoration-slate-500/60 hover:decoration-sky-400/60 transition"
            >
              <?= e(kc_t('page.karate_shotokan.heading')) ?>
            </a>
            <?= e(kc_t('home.hero.body')) ?>
          </p>

          <div class="mt-6 flex flex-wrap gap-3">
            <a
              href="#inscription"
              class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm md:text-base font-semibold text-white shadow-lg shadow-red-900/40 hover:bg-red-500 hover:translate-y-[1px] transition"
            >
              <?= e(kc_t('home.quick.trial_label')) ?>
              <span aria-hidden="true"></span>
            </a>

            <a
              href="#horaires"
              class="inline-flex items-center rounded-xl border border-slate-500/70 px-5 py-3 text-sm md:text-base font-semibold text-slate-100 hover:border-sky-400 hover:text-sky-300 transition"
            >
              <?= e(kc_t('common.nav.schedule')) ?>
            </a>
          </div>

          <div class="mt-4 text-xs md:text-sm text-slate-300">
            <?= e(kc_t('home.quick.trial')) ?>
          </div>
        </div>

        <!-- Colonne vidÃ©o + carte (mÃªme largeur) -->
        <div class="relative lg:justify-self-end">
          <!-- Largeur commune (VIDÃ‰O + CARTE) -->
          <div class="mx-auto w-full max-w-[22rem] lg:mx-0 lg:ml-auto">
            <!-- Bloc vidÃ©o -->
            <div class="relative">
              <!-- Glow -->
              <div
                aria-hidden="true"
                class="pointer-events-none absolute -inset-6 rounded-[2rem] bg-gradient-to-tr from-sky-500/20 via-red-500/10 to-emerald-500/20 blur-2xl"
              ></div>

              <!-- Cadre -->
              <div class="relative overflow-hidden rounded-2xl border border-slate-700/70 bg-slate-950/40 shadow-2xl">
                <!-- Barre header style "player" -->
                <div class="flex items-center justify-between gap-3 border-b border-slate-800/60 px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    <span class="ml-2 text-xs text-slate-400"><?= e(kc_t('home.video.caption')) ?></span>
                  </div>
                  <span class="rounded-full border border-slate-700/80 bg-slate-900/60 px-2.5 py-1 text-[11px] text-slate-300">
                    <?= e(kc_t('home.video.autoplay')) ?>
                  </span>
                </div>

                <!-- VidÃ©o responsive -->
                <div class="aspect-video">
                  <iframe
                    id="random-youtube"
                    class="h-full w-full"
                    src=""
                    title="<?= e(kc_t('home.video.title')) ?>"
                    frameborder="0"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                  ></iframe>
                </div>
              </div>
            </div>

            <!-- Carte groupes (mÃªme largeur) -->
            <div class="mt-4">
              <div class="rounded-2xl bg-slate-900/80 border border-slate-700/70 p-5 shadow-xl backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-3">
                  <?= e(kc_t('home.groups.title')) ?>
                </p>

                <ul class="space-y-2 text-sm text-slate-100">
                  <li class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                    <span><?= e(kc_t('home.groups.children')) ?></span>
                  </li>
                  <li class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-orange-500"></span>
                    <span><?= e(kc_t('home.groups.teens')) ?></span>
                  </li>
                  <li class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    <span><?= e(kc_t('home.groups.adults')) ?></span>
                  </li>
                </ul>

                <p class="mt-4 text-xs text-slate-400">
                  <?= e(kc_t('home.groups.note_before')) ?>
                  <code class="text-slate-300">.ics</code>.
                </p>
              </div>
            </div>
          </div>
          <!-- /Largeur commune -->
        </div>
      </div>
    </div>
  </section>

  <!-- Informations essentielles -->
  <section id="informations-essentielles" class="section bg-slate-900/40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
      <h2 class="text-2xl md:text-3xl font-extrabold"><?= e(kc_t('home.essential.title')) ?></h2>
      <p class="mt-3 max-w-3xl text-slate-300">
        <?= e(kc_t('home.essential.body')) ?>
      </p>
      <dl class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4 text-sm">
        <div class="essential-info-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
          <dt class="font-semibold text-slate-100"><?= e(kc_t('home.essential.discipline.label')) ?></dt>
          <dd class="mt-1 text-slate-300"><?= e(kc_t('home.essential.discipline.value')) ?></dd>
        </div>
        <div class="essential-info-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
          <dt class="font-semibold text-slate-100"><?= e(kc_t('home.essential.address.label')) ?></dt>
          <dd class="mt-1 text-slate-300"><?= e(kc_t('home.essential.address.value')) ?></dd>
        </div>
        <div class="essential-info-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
          <dt class="font-semibold text-slate-100"><?= e(kc_t('home.essential.audience.label')) ?></dt>
          <dd class="mt-1 text-slate-300"><?= e(kc_t('home.essential.audience.value')) ?></dd>
        </div>
        <div class="essential-info-card rounded-xl border border-slate-800 bg-slate-950/50 p-4">
          <dt class="font-semibold text-slate-100"><?= e(kc_t('home.essential.contact.label')) ?></dt>
          <dd class="mt-1 text-slate-300">info@kc-nalinnes.be Â· +32 497 25 12 14 Â· +32 488 09 50 27</dd>
        </div>
      </dl>
    </div>
  </section>

  <!-- Pour qui ? -->
  <section class="section" id="publics">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-10 pt-10">
      <h2 class="text-2xl md:text-3xl font-extrabold mb-6">
        <?= e(kc_t('home.audience.title')) ?>
      </h2>
      <div class="grid gap-4 md:grid-cols-3 text-sm text-slate-200">
        <div class="rounded-2xl border border-blue-500/40 bg-blue-500/10 p-4">
          <h3 class="font-semibold mb-1 text-blue-100"><?= e(kc_t('home.audience.children.title')) ?></h3>
          <p class="text-xs sm:text-sm">
            <?= e(kc_t('home.audience.children.body')) ?>
          </p>
        </div>
        <div class="rounded-2xl border border-orange-500/40 bg-orange-500/10 p-4">
          <h3 class="font-semibold mb-1 text-orange-100"><?= e(kc_t('home.audience.teens.title')) ?></h3>
          <p class="text-xs sm:text-sm">
            <?= e(kc_t('home.audience.teens.body')) ?>
          </p>
        </div>
        <div class="rounded-2xl border border-green-500/40 bg-green-500/10 p-4">
          <h3 class="font-semibold mb-1 text-green-100"><?= e(kc_t('home.audience.adults.title')) ?></h3>
          <p class="text-xs sm:text-sm">
            <?= e(kc_t('home.audience.adults.body')) ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pourquoi KC Nalinnes -->
  <section class="section" id="pourquoi">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
      <h2 class="text-2xl md:text-3xl font-extrabold mb-6">
        <?= e(kc_t('home.why.title')) ?>
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div class="text-2xl mb-2">ðŸ¥‹</div>
          <h3 class="font-semibold"><a href="karate-shotokan.php"><?= e(kc_t('page.karate_shotokan.heading')) ?></a> <a href="https://www.ffkama.be/">FFKAMA/GFK</a></h3>
          <p class="mt-1 text-slate-300 text-xs sm:text-sm">
            <?= e(kc_t('home.why.affiliated.body')) ?>
          </p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div class="text-2xl mb-2">ðŸ‘¨â€ðŸ‘©â€ðŸ‘§</div>
          <h3 class="font-semibold"><?= e(kc_t('home.why.all_ages.title')) ?></h3>
          <p class="mt-1 text-slate-300 text-xs sm:text-sm">
            <?= e(kc_t('home.why.all_ages.body')) ?>
          </p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div class="text-2xl mb-2">ðŸŽ“</div>
          <h3 class="font-semibold"><?= e(kc_t('home.why.grades.title')) ?></h3>
          <p class="mt-1 text-slate-300 text-xs sm:text-sm">
            <?= e(kc_t('home.why.grades.body')) ?>
          </p>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <div class="text-2xl mb-2">ðŸ†</div>
          <h3 class="font-semibold"><?= e(kc_t('home.why.events.title')) ?></h3>
          <p class="mt-1 text-slate-300 text-xs sm:text-sm">
            <?= e(kc_t('home.why.events.body')) ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Horaires -->
  <section id="horaires" class="section">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.schedule.title')) ?></h2>
      <p class="mt-2 text-slate-300"><?= e(kc_t('home.schedule.body')) ?></p>
      <div class="mt-6 overflow-x-auto rounded-xl border border-slate-800">
        <table class="min-w-full divide-y divide-slate-800">
          <thead class="bg-slate-900/60">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold"><?= e(kc_t('home.schedule.table.day')) ?></th>
              <th class="px-4 py-3 text-left text-sm font-semibold"><?= e(kc_t('home.schedule.table.start')) ?></th>
              <th class="px-4 py-3 text-left text-sm font-semibold"><?= e(kc_t('home.schedule.table.end')) ?></th>
              <th class="px-4 py-3 text-left text-sm font-semibold"><?= e(kc_t('home.schedule.table.group')) ?></th>
              <th class="px-4 py-3 text-left text-sm font-semibold"><?= e(kc_t('home.schedule.table.level')) ?></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800">
            <tr>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.monday')) ?></td>
              <td class="px-4 py-3">17:00</td>
              <td class="px-4 py-3">18:00</td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.group.children_5_7')) ?></td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.level.beginner')) ?></td>
            </tr>
            <tr>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.monday')) ?></td>
              <td class="px-4 py-3">18:00</td>
              <td class="px-4 py-3">19:00</td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.group.children_7_12')) ?></td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.level.advanced')) ?></td>
            </tr>
            <tr>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.monday')) ?></td>
              <td class="px-4 py-3">19:00</td>
              <td class="px-4 py-3">20:30</td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.group.adults')) ?></td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.level.all')) ?></td>
            </tr>
            <tr>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.friday')) ?></td>
              <td class="px-4 py-3">18:00</td>
              <td class="px-4 py-3">19:00</td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.group.children_7_12')) ?></td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.level.advanced')) ?></td>
            </tr>
            <tr>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.friday')) ?></td>
              <td class="px-4 py-3">19:00</td>
              <td class="px-4 py-3">20:30</td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.group.adults')) ?></td>
              <td class="px-4 py-3"><?= e(kc_t('home.schedule.level.all')) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="mt-3 text-sm text-slate-400"><?= e(kc_t('home.schedule.warning')) ?></p>
    </div>
  </section>

  <!-- Tarifs septembre Ã  juin -->
  <section id="tarifs" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.prices.full_year.title')) ?></h2>
      <div class="mt-4 inline-flex items-center gap-1 rounded-xl border border-slate-800 p-1" role="tablist" aria-label="<?= e(kc_t('home.prices.toggle_aria')) ?>">
        <button id="btn-annual" role="tab" aria-selected="true" class="price-toggle active rounded-lg px-3 py-1 text-sm font-semibold bg-slate-800"><?= e(kc_t('home.prices.annual')) ?></button>
        <button id="btn-monthly" role="tab" aria-selected="false" class="price-toggle rounded-lg px-3 py-1 text-sm font-semibold hover:bg-slate-800"><?= e(kc_t('home.prices.monthly')) ?></button>
      </div>
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.children.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.children.subtitle')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount" data-annual="150" data-monthly="20">150</span><span class="text-base font-medium text-slate-400 price-period"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.license')) ?></li>
            <li><?= e(kc_t('home.prices.feature.one_class')) ?></li>
            <li><?= e(kc_t('home.prices.feature.belts')) ?></li>
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-sky-700 p-6 ring-1 ring-sky-700">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.all.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.all.subtitle')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount" data-annual="250" data-monthly="35">250</span><span class="text-base font-medium text-slate-400 price-period"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.license')) ?></li>
            <li><?= e(kc_t('home.prices.feature.two_classes')) ?></li>
            <li><?= e(kc_t('home.prices.feature.belts')) ?></li>
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.family.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.family.second_child')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount" data-annual="200" data-monthly="30">200</span><span class="text-base font-medium text-slate-400 price-period"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.discount')) ?></li>
            <li><?= e(kc_t('home.prices.feature.installments')) ?></li>
            <!-- <li>Stage inclus</li>-->
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.family.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.family.third_child')) ?></p>
          <p class="mt-4 text-4xl font-extrabold"><span class="price-amount2" data-annual="<?= e(kc_t('home.prices.free')) ?>" data-monthly="<?= e(kc_t('home.prices.free')) ?>"><?= e(kc_t('home.prices.free')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.discount')) ?></li>
            <li><?= e(kc_t('home.prices.feature.installments')) ?></li>
            <!-- <li>Stage inclus</li>-->
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- Tarifs janvier Ã  juin -->
  <section id="tarifs2" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.prices.half_year.title')) ?></h2>
      <div class="mt-4 inline-flex items-center gap-1 rounded-xl border border-slate-800 p-1" role="tablist" aria-label="<?= e(kc_t('home.prices.toggle_aria')) ?>">
        <button id="btn-annual2" role="tab" aria-selected="true" class="price-toggle active rounded-lg px-3 py-1 text-sm font-semibold bg-slate-800"><?= e(kc_t('home.prices.annual')) ?></button>
        <button id="btn-monthly2" role="tab" aria-selected="false" class="price-toggle rounded-lg px-3 py-1 text-sm font-semibold hover:bg-slate-800"><?= e(kc_t('home.prices.monthly')) ?></button>
      </div>
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.children.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.children.subtitle')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount2" data-annual="90" data-monthly="20">90</span><span class="text-base font-medium text-slate-400 price-period2"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.license')) ?></li>
            <li><?= e(kc_t('home.prices.feature.one_class')) ?></li>
            <li><?= e(kc_t('home.prices.feature.belts')) ?></li>
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-sky-700 p-6 ring-1 ring-sky-700">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.all.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.all.subtitle')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount2" data-annual="150" data-monthly="35">150</span><span class="text-base font-medium text-slate-400 price-period2"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.license')) ?></li>
            <li><?= e(kc_t('home.prices.feature.two_classes')) ?></li>
            <li><?= e(kc_t('home.prices.feature.belts')) ?></li>
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.family.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.family.second_child')) ?></p>
          <p class="mt-4 text-4xl font-extrabold">â‚¬<span class="price-amount2" data-annual="120" data-monthly="30">120</span><span class="text-base font-medium text-slate-400 price-period2"><?= e(kc_t('home.prices.period.year')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.discount')) ?></li>
            <li><?= e(kc_t('home.prices.feature.installments')) ?></li>
            <!-- <li>Stage inclus</li> -->
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
        <div class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.prices.family.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.prices.family.third_child')) ?></p>
          <p class="mt-4 text-4xl font-extrabold"><span class="price-amount2" data-annual="<?= e(kc_t('home.prices.free')) ?>" data-monthly="<?= e(kc_t('home.prices.free')) ?>"><?= e(kc_t('home.prices.free')) ?></span></p>
          <ul class="mt-4 space-y-2 text-sm text-slate-300">
            <li><?= e(kc_t('home.prices.feature.discount')) ?></li>
            <li><?= e(kc_t('home.prices.feature.installments')) ?></li>
            <!-- <li>Stage inclus</li> -->
          </ul>
          <a href="#inscription" class="mt-6 inline-block rounded-xl bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/30"><?= e(kc_t('home.prices.register')) ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- Inscription CTA -->
  <section id="inscription" class="section bg-gradient-to-br from-sky-900/30 to-slate-900/30">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
      <h2 class="text-3xl font-extrabold"><?= e(kc_t('home.registration.title')) ?></h2>
      <p class="mt-2 text-slate-300"><?= e(kc_t('home.registration.body')) ?></p>
      <a href="#contact" class="mt-6 inline-block rounded-xl bg-red-600 px-6 py-3 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-md shadow-red-900/40"><?= e(kc_t('home.hero.cta_contact')) ?></a>
    </div>
  </section>

  <!-- Coachs -->
  <section id="coach" class="section">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold text-center"><?= e(kc_t('home.instructors.title')) ?></h2>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/sensei1.jpg"
            alt="Sensei Frank Duchesne"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">Sensei Frank Duchesne</h3>
          <p class="text-slate-300">
            <?= e(kc_t('home.instructors.frank.body')) ?>
          </p>
        </article>

        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/sensei2.jpg"
            alt="Sensei Olivier Lowie"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">Sensei Olivier Lowie</h3>
          <p class="text-slate-300">
            <?= e(kc_t('home.instructors.olivier.body')) ?>
          </p>
        </article>

        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/sensei3.jpg"
            alt="Sensei Matyas"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">Sensei Matyas Simon</h3>
          <p class="text-slate-300"><?= e(kc_t('home.instructors.matyas.body')) ?></p>
        </article>

        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/senpai2.jpg"
            alt="Senpai HervÃ© Lowie"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">Senpai HervÃ© Lowie</h3>
          <p class="text-slate-300"><?= e(kc_t('home.instructors.herve.body')) ?></p>
        </article>
      </div>
      <!-- TrÃ©sorier / SecrÃ©taire -->
      <!--<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/"
            alt="SecrÃ©taire"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">SecrÃ©taire</h3>
          <p class="text-slate-300">Ceinture.</p>
        </article>

        <article class="rounded-2xl border border-slate-800 p-6 text-center">
          <img
            src="./assets/"
            alt="TrÃ©sorier"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />
          <h3 class="mt-4 text-xl font-semibold">TrÃ©sorier</h3>
          <p class="text-slate-300">Ceinture.</p>
        </article>
      </div>-->
    </div>
  </section>

  <!-- TÃ©moignages -->
  <section id="temoignages" class="section bg-slate-900/40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.testimonials.title')) ?></h2>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
        <figure class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 shadow-sm">
          <blockquote class="text-slate-200 italic">
            <?= e(kc_t('home.testimonials.1.quote')) ?>
          </blockquote>
          <figcaption class="mt-3 text-xs text-slate-400">
            <?= e(kc_t('home.testimonials.1.author')) ?>
          </figcaption>
        </figure>
        <figure class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 shadow-sm">
          <blockquote class="text-slate-200 italic">
            <?= e(kc_t('home.testimonials.2.quote')) ?>
          </blockquote>
          <figcaption class="mt-3 text-xs text-slate-400">
            <?= e(kc_t('home.testimonials.2.author')) ?>
          </figcaption>
        </figure>
        <figure class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5 shadow-sm">
          <blockquote class="text-slate-200 italic">
            <?= e(kc_t('home.testimonials.3.quote')) ?>
          </blockquote>
          <figcaption class="mt-3 text-xs text-slate-400">
            <?= e(kc_t('home.testimonials.3.author')) ?>
          </figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- Calendrier -->
  <section id="calendrier" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.calendar.title')) ?></h2>
      <p class="mt-2 text-slate-300"><?= e(kc_t('home.calendar.short_body')) ?></p>

      <!-- Boutons ICS : 4 fichiers sÃ©parÃ©s -->
      <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end sm:gap-3 text-xs sm:text-sm">

        <button id="btnExportICSenfants"
                class="group inline-flex items-center gap-2 rounded-xl border border-blue-400/70 bg-blue-500/10 px-4 py-2 font-semibold text-blue-100 hover:bg-blue-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-500/20 group-hover:bg-blue-600/80">
            ðŸ“…
          </span>
          <span class="text-left leading-tight">
            <span class="block"><?= e(kc_t('home.calendar.ics.children')) ?></span>
            <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.calendar.ics.add')) ?></span>
          </span>
        </button>

        <button id="btnExportICSados"
                class="group inline-flex items-center gap-2 rounded-xl border border-orange-400/70 bg-orange-500/10 px-4 py-2 font-semibold text-orange-100 hover:bg-orange-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-orange-500/20 group-hover:bg-orange-600/80">
            ðŸ“…
          </span>
          <span class="text-left leading-tight">
            <span class="block"><?= e(kc_t('home.calendar.ics.teens')) ?></span>
            <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.calendar.ics.add')) ?></span>
          </span>
        </button>

        <button id="btnExportICSadultes"
                class="group inline-flex items-center gap-2 rounded-xl border border-green-400/70 bg-green-500/10 px-4 py-2 font-semibold text-green-100 hover:bg-green-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-green-500/20 group-hover:bg-green-600/80">
            ðŸ“…
          </span>
          <span class="text-left leading-tight">
            <span class="block"><?= e(kc_t('home.calendar.ics.adults')) ?></span>
            <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.calendar.ics.add')) ?></span>
          </span>
        </button>

        <button id="btnExportICStout"
                class="group inline-flex items-center gap-2 rounded-xl border border-sky-400/80 bg-slate-900/60 px-4 py-2 font-semibold text-slate-100 hover:bg-gradient-to-r hover:from-blue-500 hover:via-orange-500 hover:to-green-500 hover:text-slate-900 hover:shadow-lg hover:-translate-y-[1px] transition">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-800 group-hover:bg-slate-900/10">
            â­
          </span>
          <span class="text-left leading-tight">
            <span class="block"><?= e(kc_t('home.calendar.ics.club')) ?></span>
            <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.calendar.ics.club_note')) ?></span>
          </span>
        </button>

      </div>


      <div class="mt-6 rounded-2xl border border-slate-800 p-2">
        <div id="calendar" class="bg-slate-950 rounded-xl p-2"></div>
      </div>
      <p class="mt-3 text-sm text-slate-400"><?= e(kc_t('home.calendar.tip')) ?></p>
    </div>
  </section>

  <!-- Documents -->
  <section id="documents" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.documents.title')) ?></h2>
      <p class="mt-2 text-slate-300">
        <?= e(kc_t('home.documents.intro')) ?>
      </p>

      <!-- Ligne 1 -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Licence FFKAMA -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.license.title')) ?> <a href="https://www.ffkama.be/">FFKAMA</a></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= e(kc_t('home.documents.license.body')) ?> <a href="https://www.ffkama.be/">FFKAMA</a>.
            </p>
          </div>
          <a href="/docs/fichier_modulable_licence_pratiquant_avec_carnet.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.license.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- Programme et conditions pour les Ã©preuves Shiken G.F.K.-->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.shiken.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= e(kc_t('home.documents.shiken.body')) ?>
            </p>
          </div>
          <a href="/docs/programme-shiken-092025-3.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.shiken.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- DÃ©claration d'assurance Ethias -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.accident.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= e(kc_t('home.documents.accident.body')) ?>
            </p>
          </div>
          <a href="/docs/Ethias_D_E9clarationAccident_45.339.711.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.accident.pdf')) ?></span>
            </span>
          </a>
        </article>
      </div>

      <!-- Ligne 2 -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Prime MC -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.mc.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.mc.body_html') ?>
            </p>
          </div>
          <a href="/docs/mc_formulaire_AC_SPORT_A4_FR_2024_V2.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.mc.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- Prime Solidaris -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.solidaris.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.solidaris.body_html') ?>
            </p>
          </div>
          <a href="/docs/Formulaire-de-demande-dintervention-Sports-2025.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.solidaris.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- Prime MutualitÃ© neutre -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.neutral.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.neutral.body_html') ?>
            </p>
          </div>
          <a href="/docs/SC - sport.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.neutral.pdf')) ?></span>
            </span>
          </a>
        </article>
      </div>
      
      <!-- Ligne 3 -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Prime MutualitÃ© libÃ©rale -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.liberal.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.liberal.body_html') ?>
            </p>
          </div>
          <a href="/docs/409-FACVA024.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.liberal.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- Prime Mutualia -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.mutualia.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.mutualia.body_html') ?>
            </p>
          </div>
          <a href="/docs/mutualia-ac-sport-fr.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.mutualia.pdf')) ?></span>
            </span>
          </a>
        </article>

        <!-- Prime Partenamut -->
        <article class="rounded-2xl border border-slate-800 p-6 flex flex-col justify-between">
          <div>
            <h3 class="text-xl font-semibold"><?= e(kc_t('home.documents.partenamut.title')) ?></h3>
            <p class="mt-2 text-slate-300 text-sm">
              <?= kc_t('home.documents.partenamut.body_html') ?>
            </p>
          </div>
          <a href="/docs/avantage-inscription club sportif.pdf" download
             class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-sky-400/70 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-100 hover:bg-sky-500 hover:text-slate-900 hover:shadow-md hover:-translate-y-[1px] transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/20">
              â¬‡ï¸
            </span>
            <span class="text-left leading-tight">
              <span class="block"><?= e(kc_t('home.documents.download')) ?></span>
              <span class="block text-[0.7rem] font-normal opacity-80"><?= e(kc_t('home.documents.partenamut.pdf')) ?></span>
            </span>
          </a>
        </article>
      </div>
    </div>
  </section>

  <!-- CompÃ©titions -->
  <section id="competitions" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.competitions.title')) ?></h2>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6">
          <img
            src="./assets/competitions/carolo_cup_26.jpg"
            alt="Carolo Cup 2026"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />

          <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
              href="./assets/competitions/Carolo Cup 2026 - formulaire inscriptions.xls"
              download
              class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 border border-slate-700 transition"
            >
              <?= e(kc_t('home.competitions.registration')) ?>
            </a>

            <a
              href="./assets/competitions/Carolo Cup - rÃ¨glement.pdf"
              download
              class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 border border-slate-700 transition"
            >
              <?= e(kc_t('home.competitions.rules')) ?>
            </a>
          </div>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <img
            src="./assets/competitions/gfk_ransart_26.jpg"
            alt="Ransart 2026"
            class="h-80 max-w-xs mx-auto rounded-xl object-cover"
          />

          <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
              href="https://gf-karate.com/organisations-cercles/article/135/championnat-gfk"
              download
              class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 border border-slate-700 transition"
            >
              <?= e(kc_t('home.competitions.link')) ?>
            </a>
            <!--
            <a
              href="./assets/competitions/Carolo Cup - rÃ¨glement.pdf"
              download
              class="inline-flex items-center justify-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 border border-slate-700 transition"
            >
              RÃ¨glement
            </a>
            -->
          </div>

        </article>
      </div>
    </div>
  </section>

  <!-- Actus -->
  <section id="actus" class="section bg-slate-900/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.news.title')) ?></h2>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.feb_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.feb_2026.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.easter_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.easter_2026.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.grade_june_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.grade_june_2026.body')) ?></p>
        </article>
      </div>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.restart_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.restart_2026.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.autumn_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.autumn_2026.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.saint_nicolas_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.saint_nicolas_2026.body')) ?></p>
        </article>
      </div>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.december_2026.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.december_2026.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.grade_jan_2027.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.grade_jan_2027.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.feb_2027.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.feb_2027.body')) ?></p>
        </article>
      </div>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.easter_2027.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.easter_2027.body')) ?></p>
        </article>
        <article class="rounded-2xl border border-slate-800 p-6">
          <h3 class="text-xl font-semibold"><?= e(kc_t('home.news.grade_june_2027.title')) ?></h3>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.news.grade_june_2027.body')) ?></p>
        </article>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-3xl font-bold"><?= e(kc_t('home.faq.title')) ?></h2>
      <div class="mt-6 space-y-4">
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.equipment.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.equipment.answer')) ?></p>
        </details>
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.trial.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.trial.answer')) ?></p>
        </details>
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.age.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.age.answer')) ?></p>
        </details>
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.location.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.location.answer')) ?></p>
        </details>
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.schedule.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.schedule.answer')) ?></p>
        </details>
        <details class="rounded-xl border border-slate-800 p-4">
          <summary class="font-semibold"><?= e(kc_t('home.faq.contact.question')) ?></summary>
          <p class="mt-2 text-slate-300"><?= e(kc_t('home.faq.contact.answer')) ?></p>
        </details>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section id="contact" class="section">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 grid lg:grid-cols-2 gap-10 items-start">
      <div>
        <h2 class="text-3xl font-bold"><?= e(kc_t('home.contact.title')) ?></h2>
        <p class="mt-2 text-slate-300"><?= e(kc_t('home.contact.address')) ?></p>

        <div class="mt-4 rounded-2xl border border-slate-800 overflow-hidden">
          <iframe title="<?= e(kc_t('home.contact.map_title')) ?>"
                  class="w-full h-64"
                  style="border:0"
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  src="https://www.google.com/maps?q=18%20Rue%20des%20Monts%2C%206120%20Nalinnes%2C%20Belgique&output=embed">
          </iframe>
          <p class="mt-2 text-sm px-2 py-1">
            <a class="underline decoration-sky-500/50 underline-offset-4" target="_blank" rel="noopener"
               href="https://maps.google.com/?q=18%20rue%20des%20Monts%2C%206120%20Nalinnes%2C%20Belgique"><?= e(kc_t('home.contact.directions')) ?></a>
          </p>
        </div>

        <ul class="mt-4 space-y-2 text-slate-300">
          <li>ðŸ“§ <a class="underline decoration-sky-500/50 underline-offset-4" href="mailto:info@kc-nalinnes.be">info@kc-nalinnes.be</a></li>
          <li>ðŸ“ž <a class="underline decoration-sky-500/50 underline-offset-4" href="tel:+32497251214">Olivier Lowie: +32 497 25 12 14</a></li>
          <li>ðŸ“ž <a class="underline decoration-sky-500/50 underline-offset-4" href="tel:+32488095027">Frank Duchesne: +32 488 09 50 27</a></li>
          <li>ðŸ“± <a class="underline decoration-sky-500/50 underline-offset-4" href="https://www.facebook.com/KarateClubNalinnes">Facebook</a><!-- Â· <a class="underline decoration-sky-500/50 underline-offset-4" href="#">Instagram</a></li>-->
        </ul>
      </div>

      <form name="contact" method="POST" action="/contact.php" class="rounded-2xl border border-slate-800 p-6 space-y-4" name="contact" netlify>
        <input type="hidden" name="lang" value="<?= e($locale) ?>">
        <p class="hidden">
          <label><?= e(kc_t('home.contact.honeypot')) ?> <input name="website" /></label>
        </p>
        <div>
          <label class="block text-sm text-slate-300"><?= e(kc_t('home.contact.name')) ?></label>
          <input class="mt-1 w-full rounded-lg bg-slate-900 border border-slate-800 px-3 py-2" name="name" required />
        </div>
        <div>
          <label class="block text-sm text-slate-300"><?= e(kc_t('home.contact.email')) ?></label>
          <input type="email" class="mt-1 w-full rounded-lg bg-slate-900 border border-slate-800 px-3 py-2" name="email" required />
        </div>
        <div>
          <label class="block text-sm text-slate-300"><?= e(kc_t('home.contact.message')) ?></label>
          <textarea class="mt-1 w-full rounded-lg bg-slate-900 border border-slate-800 px-3 py-2" rows="4" name="message" required></textarea>
        </div>
        <button class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-500 hover:translate-y-[1px] transition shadow-sm shadow-red-900/40"><?= e(kc_t('home.contact.submit')) ?></button>
      </form>
    </div>
  </section>

  <!-- Badges -->
  <section class="section">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-2 md:grid-cols-4 gap-4">
      <a href="karate-shotokan.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ¥‹</span>
        <span class="kc-badge-label"><?= e(kc_t('page.karate_shotokan.heading')) ?></span>
      </a>
      <a href="kata-shotokan.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸŒ€</span>
        <span class="kc-badge-label"><?= e(kc_t('page.kata_shotokan.heading')) ?></span>
      </a>
      <a href="vocabulaire-karate-shotokan.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ“–</span>
        <span class="kc-badge-label"><?= e(kc_t('page.vocabulaire.heading')) ?></span>
      </a>
      <a href="dojo-kun.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ§ </span>
        <span class="kc-badge-label"><?= e(kc_t('page.dojo_kun.heading')) ?></span>
      </a>
      <a href="technique_base.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ‘Š</span>
        <span class="kc-badge-label"><?= e(kc_t('page.technique_base.heading')) ?></span>
      </a>
      <a href="techniques_kumite.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ¥Š</span>
        <span class="kc-badge-label"><?= e(kc_t('page.techniques_kumite.heading')) ?></span>
      </a>
      <a href="reviser_katas.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ“</span>
        <span class="kc-badge-label"><?= e(kc_t('page.reviser_katas.heading')) ?></span>
      </a>
      <a href="stretching.php" class="kc-badge-link">
        <span class="kc-badge-icon">ðŸ§˜</span>
        <span class="kc-badge-label"><?= e(kc_t('page.stretching.heading')) ?></span>
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 text-sm text-slate-400 flex flex-col md:flex-row gap-3 items-center justify-between">
      <p>Â© <span id="year"></span> KC Nalinnes. <?= e(kc_t('common.footer.rights')) ?> - <?= e(kc_t('common.footer.developed_by')) ?> <a href="https://smartappli.eu">SmartAppli&reg;</a></p>
      <nav class="flex gap-4">
        <a href="/mentions-legales.php" class="hover:text-orange-600"><?= e(kc_t('common.footer.legal')) ?></a>
        <a href="/politique-confidentialite.php" class="hover:text-orange-600"><?= e(kc_t('common.footer.privacy')) ?></a>
      </nav>
    </div>
  </footer>

  <!-- CTA mobile fixe -->
  <div class="fixed inset-x-0 bottom-0 z-40 bg-slate-950/95 backdrop-blur border-t border-slate-800 md:hidden">
    <div class="mx-auto max-w-7xl px-4 py-2 flex items-center justify-between gap-3">
      <span class="text-xs text-slate-200">
        <?= kc_t('home.mobile.trial_html') ?>
      </span>
      <a href="#inscription"
         class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
        <?= e(kc_t('home.mobile.trial_cta')) ?>
      </a>
    </div>
  </div>

  <script>
    window.kcFullCalendarReady = Promise.all([
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/+esm'),
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/daygrid/+esm'),
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/timegrid/+esm'),
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/list/+esm'),
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/interaction/+esm'),
      import('https://cdn.jsdelivr.net/npm/fullcalendar@7.0.0/themes/classic/+esm')
    ]).then(function (modules) {
      window.FullCalendar = {
        Calendar: modules[0].Calendar || modules[0].default,
        plugins: [
          modules[1].default,
          modules[2].default,
          modules[3].default,
          modules[4].default,
          modules[5].default
        ].filter(Boolean)
      };

      return window.FullCalendar;
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const homeI18n = {
        themeLight: <?= json_encode(kc_t('home.theme.light'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        themeDark: <?= json_encode(kc_t('home.theme.dark'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventChildren: <?= json_encode(kc_t('home.calendar.event.children'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventTeens: <?= json_encode(kc_t('home.calendar.event.teens'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventAdults: <?= json_encode(kc_t('home.calendar.event.adults'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventSaintNicholas: <?= json_encode(kc_t('home.calendar.event.saint_nicholas'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventSaintNicholasChildren: <?= json_encode(kc_t('home.calendar.event.saint_nicholas_children'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventGrading: <?= json_encode(kc_t('home.calendar.event.grading'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventTeensAdults: <?= json_encode(kc_t('home.calendar.event.teens_adults'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventTeensAdultsOctober: <?= json_encode(kc_t('home.calendar.event.teens_adults_october'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventTeensAdultsFebruary: <?= json_encode(kc_t('home.calendar.event.teens_adults_february'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        eventTeensAdultsEaster: <?= json_encode(kc_t('home.calendar.event.teens_adults_easter'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        icsChildrenName: <?= json_encode(kc_t('home.calendar.ics.name.children'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        icsTeensName: <?= json_encode(kc_t('home.calendar.ics.name.teens'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        icsAdultsName: <?= json_encode(kc_t('home.calendar.ics.name.adults'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        icsClubName: <?= json_encode(kc_t('home.calendar.ics.name.club'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
      };

      // --- Sakura (actif du 1/3 au 31/3) -----------------------------
      try {
        const now = new Date();
        const month = now.getMonth(); // 0=janvier ... 2=mars
        const day = now.getDate();

        // 21 mars -> 31 mars (mars = 2)
        const isSakuraSeason = (month === 2 && day >= 1 && day <= 31);

        if (isSakuraSeason) {
          const PETAL_COUNT = 45; // ajuste (30..80)
          const container = document.createElement('div');
          container.id = 'sakuraContainer';
          document.body.appendChild(container);

          const petals = ['ðŸŒ¸', 'ðŸŒ¸', 'ðŸŒ¸']; 

          for (let i = 0; i < PETAL_COUNT; i++) {
            const petal = document.createElement('span');
            petal.className = 'sakura-petal';
            petal.textContent = petals[Math.floor(Math.random() * petals.length)];

            // Position / taille / vitesse
            const startLeft = Math.random() * 100;        // 0..100 vw
            const size = 0.8 + Math.random() * 1.4;       // 0.8rem..2.2rem
            const duration = 9 + Math.random() * 10;      // 9s..19s
            const delay = -Math.random() * 20;            // dÃ©marrage Ã©chelonnÃ©
            const drift = -40 + Math.random() * 120;      // dÃ©rive gauche/droite
            const spin = (Math.random() < 0.5 ? -1 : 1) * (180 + Math.random() * 540);

            petal.style.left = startLeft + 'vw';
            petal.style.fontSize = size + 'rem';
            petal.style.animationDuration = duration + 's';
            petal.style.animationDelay = delay + 's';
            petal.style.opacity = (0.45 + Math.random() * 0.45).toFixed(2);

            // petite variation par pÃ©tale (dÃ©rive + rotation) via variable CSS
            // on â€œtricheâ€ en ajoutant un transform initial via translateX et rotate
            petal.style.transform = `translate3d(0, -120%, 0) rotate(${Math.random() * 360}deg)`;
            petal.style.animationName = 'sakuraFall';

            // Pour varier la trajectoire : on adapte la keyframe avec une CSS variable via animation + translateX
            // -> solution simple : on ajoute une animation secondaire "oscillation" avec drift
            petal.animate(
              [
                { transform: `translate3d(0, -120%, 0) rotate(0deg)` },
                { transform: `translate3d(${drift}px, 110vh, 0) rotate(${spin}deg)` }
              ],
              { duration: duration * 1000, iterations: Infinity, delay: delay * 1000, easing: 'linear' }
            );

            container.appendChild(petal);
          }
        }
      } catch (e) {
        console.error('Erreur sakura :', e);
      }

      // --- Feuilles d'automne (actives en OCTOBRE) -----------------------
      try {
        const now = new Date();
        const month = now.getMonth(); // 0=janvier ... 9=octobre

        const isOctober = (month === 9);

        if (isOctober) {
          const LEAF_COUNT = 40; // ajuste (25..80)
          const container = document.createElement('div');
          container.id = 'leavesContainer';
          document.body.appendChild(container);

          // Emojis feuilles (simple et efficace)
          const leaves = ['ðŸ','ðŸ‚','ðŸƒ'];

          for (let i = 0; i < LEAF_COUNT; i++) {
            const leaf = document.createElement('span');
            leaf.className = 'autumn-leaf';
            leaf.textContent = leaves[Math.floor(Math.random() * leaves.length)];

            const startLeft = Math.random() * 100;         // vw
            const size = 0.9 + Math.random() * 1.8;        // rem
            const duration = 10 + Math.random() * 14;      // s
            const delay = -Math.random() * 25;             // s
            const drift = -70 + Math.random() * 160;       // px
            const spin = (Math.random() < 0.5 ? -1 : 1) * (240 + Math.random() * 900);

            leaf.style.left = startLeft + 'vw';
            leaf.style.fontSize = size + 'rem';
            leaf.style.opacity = (0.35 + Math.random() * 0.55).toFixed(2);

            // Fallback CSS (au cas oÃ¹)
            leaf.style.animationName = 'leafFall';
            leaf.style.animationDuration = duration + 's';
            leaf.style.animationDelay = delay + 's';

            // Animation plus â€œvivanteâ€ (trajet personnalisÃ©)
            if (leaf.animate) {
              leaf.animate(
                [
                  { transform: `translate3d(0, -120%, 0) rotate(0deg)` },
                  { transform: `translate3d(${drift}px, 110vh, 0) rotate(${spin}deg)` }
                ],
                {
                  duration: duration * 1000,
                  iterations: Infinity,
                  delay: delay * 1000,
                  easing: 'linear'
                }
              );
            }

            container.appendChild(leaf);
          }
        }
      } catch (e) {
        console.error('Erreur feuilles :', e);
      }

      // --- Flocons de neige (actifs du 1/12 au 6/1) ----------------
      try {
        const now = new Date();
        const month = now.getMonth();  // 0 = janvier, 11 = dÃ©cembre
        const day   = now.getDate();   // 1..31

        const isSnowSeason =
          (month === 11 && day >= 1) ||   // du 1 au 31 dÃ©cembre
          (month === 0  && day <= 6);     // du 1 au 6 janvier

        if (isSnowSeason) {
          const SNOWFLAKE_COUNT = 60;
          const container = document.createElement('div');
          container.id = 'snowContainer';
          document.body.appendChild(container);

          for (let i = 0; i < SNOWFLAKE_COUNT; i++) {
            const flake = document.createElement('span');
            flake.className = 'snowflake';
            flake.textContent = 'â„';

            const size = 0.6 + Math.random() * 1.1;   // 0.6rem Ã  1.7rem
            const startLeft = Math.random() * 100;    // 0 Ã  100 vw
            const duration = 8 + Math.random() * 10;  // 8s Ã  18s
            const delay = -Math.random() * 20;        // dÃ©marrage Ã©chelonnÃ©

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

      // --- ThÃ¨me Light/Dark -----------------------------------------
      function setTheme(mode){
        const root = document.documentElement;
        const isLight = mode === 'light';
        root.classList.toggle('light', isLight);
        try { localStorage.setItem('themeMode', mode); } catch(e){}
        const label = document.getElementById('themeLabel');
        const labelM = document.getElementById('themeLabelMobile');
        const sun = document.getElementById('iconSun'), moon = document.getElementById('iconMoon');
        if(label) label.textContent = isLight ? homeI18n.themeLight : homeI18n.themeDark;
        if(labelM) labelM.textContent = isLight ? homeI18n.themeLight : homeI18n.themeDark;
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

      // --- Calendrier (FullCalendar) --------------------------------
      var calendarEl = document.getElementById('calendar');
      if (calendarEl) {
        (window.kcFullCalendarReady || Promise.resolve(window.FullCalendar))
          .then(function (FullCalendar) {
            if (!FullCalendar || typeof FullCalendar.Calendar !== 'function') {
              return;
            }

          var calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: FullCalendar.plugins || [],
            initialView: (window.matchMedia && window.matchMedia('(max-width: 640px)').matches) ? 'listWeek' : 'dayGridMonth',
            height: 'auto',
            contentHeight: 'auto',
            expandRows: true,
            locale: <?= json_encode($locale, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            firstDay: 1,
            dayMaxEventRows: 3,
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
            buttonText: {
              today: <?= json_encode(kc_t('home.calendar.button.today'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
              month: <?= json_encode(kc_t('home.calendar.button.month'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
              week: <?= json_encode(kc_t('home.calendar.button.week'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
              day: <?= json_encode(kc_t('home.calendar.button.day'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
              list: <?= json_encode(kc_t('home.calendar.button.list'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
            },
            views: {
              dayGridMonth: { buttonText: <?= json_encode(kc_t('home.calendar.button.month'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> },
              timeGridWeek: { buttonText: <?= json_encode(kc_t('home.calendar.button.week'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> },
              listWeek:     { buttonText: <?= json_encode(kc_t('home.calendar.button.list'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> }
            },
            navLinks: true,
            nowIndicator: true,
            eventDisplay: 'block',
            dayHeaderFormat: { weekday: 'short' },
            slotMinTime: '16:00:00',
            slotMaxTime: '21:30:00',
            events: [
              // =========================
              // COURS RÃ‰CURRENTS (SAISON)
              // =========================
              {
                groupId: 'cours-enfants-p1',
                title: homeI18n.eventChildren,
                daysOfWeek: [1],
                startTime: '17:00',
                endTime: '18:00',
                startRecur: '2025-09-01',
                endRecur: '2025-10-19',
                color: '#3b82f6'
              },
              {
                groupId: 'cours-enfants-p2',
                title: homeI18n.eventChildren,
                daysOfWeek: [1],
                startTime: '17:00',
                endTime: '18:00',
                startRecur: '2025-11-03',
                endRecur: '2025-12-20',
                color: '#3b82f6'
              },
              {
                groupId: 'cours-enfants-p3',
                title: homeI18n.eventChildren,
                daysOfWeek: [1],
                startTime: '17:00',
                endTime: '18:00',
                startRecur: '2026-01-05',
                endRecur: '2026-02-15',
                color: '#3b82f6'
              },
              {
                groupId: 'cours-enfants-p4',
                title: homeI18n.eventChildren,
                daysOfWeek: [1],
                startTime: '17:00',
                endTime: '18:00',
                startRecur: '2026-03-02',
                endRecur: '2026-04-24',
                color: '#3b82f6'
              },
              {
                groupId: 'cours-enfants-p5',
                title: homeI18n.eventChildren,
                daysOfWeek: [1],
                startTime: '17:00',
                endTime: '18:00',
                startRecur: '2026-05-11',
                endRecur: '2026-06-26',
                color: '#3b82f6'
              },
              {
                groupId: 'cours-ados-p1',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2025-09-01',
                endRecur: '2025-10-19',
                color: '#f97316'
              },
              {
                groupId: 'cours-ados-p2',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2025-11-03',
                endRecur: '2025-12-22',
                color: '#f97316'
              },
              {
                groupId: 'cours-ados-p3a',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2026-01-05',
                endRecur: '2026-01-29',
                color: '#f97316'
              },
              {
                groupId: 'cours-ados-p3b',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2026-01-31',
                endRecur: '2026-02-15',
                color: '#f97316'
              },
              {
                groupId: 'cours-ados-p4',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2026-03-02',
                endRecur: '2026-04-24',
                color: '#f97316'
              },
              {
                groupId: 'cours-ados-p5',
                title: homeI18n.eventTeens,
                daysOfWeek: [1, 5],
                startTime: '18:00',
                endTime: '19:00',
                startRecur: '2026-05-11',
                endRecur: '2026-06-26',
                color: '#f97316'
              },
              {
                groupId: 'cours-adultes-p1',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2025-09-01',
                endRecur: '2025-10-19',
                color: '#22c55e'
              },
              {
                groupId: 'cours-adultes-p2',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2025-11-03',
                endRecur: '2025-12-22',
                color: '#22c55e'
              },
              {
                groupId: 'cours-adultes-p3a',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2026-01-05',
                endRecur: '2026-01-29',
                color: '#22c55e'
              },
              {
                groupId: 'cours-adultes-p3b',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2026-01-31',
                endRecur: '2026-02-15',
                color: '#22c55e'
              },
              {
                groupId: 'cours-adultes-p4',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2026-03-02',
                endRecur: '2026-04-24',
                color: '#22c55e'
              },
              {
                groupId: 'cours-adultes-p5',
                title: homeI18n.eventAdults,
                daysOfWeek: [1, 5],
                startTime: '19:00',
                endTime: '20:30',
                startRecur: '2026-05-11',
                endRecur: '2026-06-26',
                color: '#22c55e'
              },

              // =========================
              // Ã‰VÃ‰NEMENTS SPÃ‰CIAUX
              // =========================
              {
                title: homeI18n.eventSaintNicholas,
                start: '2025-12-01T17:00:00',
                end:   '2025-12-01T19:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventGrading,
                start: '2026-01-30T18:00:00',
                end:   '2026-01-30T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventGrading,
                start: '2026-06-26T18:00:00',
                end:   '2026-06-26T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2025-10-27T18:00:00',
                end:   '2025-10-27T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2025-10-31T18:00:00',
                end:   '2025-10-31T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-02-16T18:00:00',
                end:   '2026-02-16T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-02-20T18:00:00',
                end:   '2026-02-20T20:00:00',
                color: '#b91c1c'
              },              
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-02-23T18:00:00',
                end:   '2026-02-23T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-02-27T18:00:00',
                end:   '2026-02-27T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-05-04T18:00:00',
                end:   '2026-05-04T20:00:00',
                color: '#b91c1c'
              },
              {
                title: homeI18n.eventTeensAdults,
                start: '2026-05-08T18:00:00',
                end:   '2026-05-08T20:00:00',
                color: '#b91c1c'
              }
            ],
            eventClick: function(info){
              if(info.event.url){ return; }
              const t = info.event.title + (info.event.start ? (' â€” ' + info.event.start.toLocaleString()) : '');
              alert(t);
            }
          });
          calendar.render();
          })
          .catch(function (e) { console.error('Erreur FullCalendar:', e); });
      }

      // --- Export ICS : outils communs -------------------------------
      function parseDate(str) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
      }

      function dateKey(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
      }

      function generateWeeklySessions(options) {
        const { title, dayOfWeek, timeStart, timeEnd, segments, skipDates } = options;
        const out = [];
        const skipSet = new Set(skipDates || []);

        segments.forEach(seg => {
          const start = parseDate(seg.start);
          const endExclusive = parseDate(seg.end); // [start, end)
          for (let d = new Date(start); d < endExclusive; d.setDate(d.getDate() + 1)) {
            if (d.getDay() === dayOfWeek) {
              const key = dateKey(d);
              if (!skipSet.has(key)) {
                out.push({
                  title,
                  start: `${key}T${timeStart}:00`,
                  end:   `${key}T${timeEnd}:00`
                });
              }
            }
          }
        });

        return out;
      }

      function escapeICSText(str) {
        return String(str || '')
          .replace(/\\/g, '\\\\')
          .replace(/;/g, '\\;')
          .replace(/,/g, '\\,')
          .replace(/\r?\n/g, ' ');
      }

      function formatDateTimeToICS(isoStr) {
        return isoStr.replace(/[-:]/g, '').split('.')[0];
      }

      function buildICS(events, calName) {
        const now = new Date();
        const dtstamp = formatDateTimeToICS(now.toISOString());
        let ics = '';
        ics += 'BEGIN:VCALENDAR\r\n';
        ics += 'VERSION:2.0\r\n';
        ics += 'PRODID:-//KC Nalinnes//Calendrier 2025-2026//FR\r\n';
        ics += 'CALSCALE:GREGORIAN\r\n';
        ics += 'METHOD:PUBLISH\r\n';
        if (calName) {
          ics += 'X-WR-CALNAME:' + escapeICSText(calName) + '\r\n';
        }

        events.forEach((ev, idx) => {
          const uid = `kc-${idx}-${formatDateTimeToICS(ev.start)}@kcnalinnes.be`;
          ics += 'BEGIN:VEVENT\r\n';
          ics += 'UID:' + uid + '\r\n';
          ics += 'DTSTAMP:' + dtstamp + '\r\n';
          ics += 'SUMMARY:' + escapeICSText(ev.title) + '\r\n';
          if (ev.description) {
            ics += 'DESCRIPTION:' + escapeICSText(ev.description) + '\r\n';
          }
          ics += 'DTSTART:' + formatDateTimeToICS(ev.start) + '\r\n';
          ics += 'DTEND:' + formatDateTimeToICS(ev.end) + '\r\n';
          ics += 'END:VEVENT\r\n';
        });

        ics += 'END:VCALENDAR\r\n';
        return ics;
      }

      function downloadICS(events, filename, calName) {
        const ics = buildICS(events, calName);
        const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }

      function dedupeEvents(events) {
        const seen = new Set();
        const out = [];
        for (const ev of events) {
          const key = `${ev.title}||${ev.start}||${ev.end}`;
          if (!seen.has(key)) {
            seen.add(key);
            out.push(ev);
          }
        }
        return out;
      }

      // Segments pour enfants (lundi 17â€“18)
      const SEGMENTS_ENFANTS = [
        { start: '2025-09-01', end: '2025-10-19' },
        { start: '2025-11-03', end: '2025-12-20' },
        { start: '2026-01-05', end: '2026-02-15' },
        { start: '2026-03-02', end: '2026-04-24' },
        { start: '2026-05-11', end: '2026-06-26' }
      ];

      // Segments pour ados/adultes (lundi & vendredi, avec coupures pour les passages de grade)
      const SEGMENTS_ADOS_ADULTES = [
        { start: '2025-09-01', end: '2025-10-19' },
        { start: '2025-11-03', end: '2025-12-22' },
        { start: '2026-01-05', end: '2026-01-29' },
        { start: '2026-01-31', end: '2026-02-15' },
        { start: '2026-03-02', end: '2026-04-24' },
        { start: '2026-05-11', end: '2026-06-26' }
      ];

      // --- Construction des Ã©vÃ©nements par groupe -------------------
      function buildEventsEnfants() {
        const events = [];

        // Cours hebdomadaires (lundi 17â€“18), sans exclusion : Saint Nicolas est en plus
        events.push(
          ...generateWeeklySessions({
            title: homeI18n.eventChildren,
            dayOfWeek: 1, // lundi
            timeStart: '17:00',
            timeEnd: '18:00',
            segments: SEGMENTS_ENFANTS,
            skipDates: []
          })
        );

        // Saint Nicolas (en plus des cours)
        events.push({
          title: homeI18n.eventSaintNicholasChildren,
          start: '2025-12-01T17:00:00',
          end:   '2025-12-01T19:00:00',
          description: 'Visite de Saint Nicolas au dojo KC Nalinnes.'
        });

        // Passages de grade (communs, ils peuvent y participer)
        events.push(
          {
            title: homeI18n.eventGrading,
            start: '2026-01-30T18:00:00',
            end:   '2026-01-30T20:00:00',
            description: 'Passage de grade - tous niveaux.'
          },
          {
            title: homeI18n.eventGrading,
            start: '2026-06-26T18:00:00',
            end:   '2026-06-26T20:00:00',
            description: 'Passage de grade - tous niveaux.'
          }
        );

        events.sort((a, b) => a.start.localeCompare(b.start));
        return events;
      }

      function buildEventsAdos() {
        const events = [];
        const skipDates = []; // Saint Nicolas nâ€™est plus un remplacement

        // Lundi 18â€“19
        events.push(
          ...generateWeeklySessions({
            title: homeI18n.eventTeens,
            dayOfWeek: 1,
            timeStart: '18:00',
            timeEnd: '19:00',
            segments: SEGMENTS_ADOS_ADULTES,
            skipDates
          })
        );

        // Vendredi 18â€“19
        events.push(
          ...generateWeeklySessions({
            title: homeI18n.eventTeens,
            dayOfWeek: 5,
            timeStart: '18:00',
            timeEnd: '19:00',
            segments: SEGMENTS_ADOS_ADULTES,
            skipDates
          })
        );

        // SpÃ©ciaux / vacances + Saint Nicolas + passages de grade
        events.push(
          {
            title: homeI18n.eventSaintNicholas,
            start: '2025-12-01T17:00:00',
            end:   '2025-12-01T19:00:00',
            description: 'Visite de Saint Nicolas au dojo KC Nalinnes.'
          },
          {
            title: homeI18n.eventTeensAdultsOctober,
            start: '2025-10-27T18:00:00',
            end:   '2025-10-27T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsOctober,
            start: '2025-10-31T18:00:00',
            end:   '2025-10-31T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-15T18:00:00',
            end:   '2026-02-15T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-20T18:00:00',
            end:   '2026-02-20T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-23T18:00:00',
            end:   '2026-02-23T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-27T18:00:00',
            end:   '2026-02-27T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsEaster,
            start: '2026-05-04T18:00:00',
            end:   '2026-05-04T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsEaster,
            start: '2026-05-08T18:00:00',
            end:   '2026-05-08T20:00:00'
          },
          {
            title: homeI18n.eventGrading,
            start: '2026-01-30T18:00:00',
            end:   '2026-01-30T20:00:00'
          },
          {
            title: homeI18n.eventGrading,
            start: '2026-06-26T18:00:00',
            end:   '2026-06-26T20:00:00'
          }
        );

        events.sort((a, b) => a.start.localeCompare(b.start));
        return events;
      }

      function buildEventsAdultes() {
        const events = [];
        const skipDates = []; // Saint Nicolas nâ€™est plus un remplacement

        // Lundi 19â€“20:30
        events.push(
          ...generateWeeklySessions({
            title: homeI18n.eventAdults,
            dayOfWeek: 1,
            timeStart: '19:00',
            timeEnd: '20:30',
            segments: SEGMENTS_ADOS_ADULTES,
            skipDates
          })
        );

        // Vendredi 19â€“20:30
        events.push(
          ...generateWeeklySessions({
            title: homeI18n.eventAdults,
            dayOfWeek: 5,
            timeStart: '19:00',
            timeEnd: '20:30',
            segments: SEGMENTS_ADOS_ADULTES,
            skipDates
          })
        );

        // SpÃ©ciaux / vacances + Saint Nicolas + passages de grade
        events.push(
          {
            title: homeI18n.eventSaintNicholas,
            start: '2025-12-01T17:00:00',
            end:   '2025-12-01T19:00:00',
            description: 'Visite de Saint Nicolas au dojo KC Nalinnes.'
          },
          {
            title: homeI18n.eventTeensAdultsOctober,
            start: '2025-10-27T18:00:00',
            end:   '2025-10-27T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsOctober,
            start: '2025-10-31T18:00:00',
            end:   '2025-10-31T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-15T18:00:00',
            end:   '2026-02-15T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-20T18:00:00',
            end:   '2026-02-20T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-23T18:00:00',
            end:   '2026-02-23T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsFebruary,
            start: '2026-02-27T18:00:00',
            end:   '2026-02-27T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsEaster,
            start: '2026-05-04T18:00:00',
            end:   '2026-05-04T20:00:00'
          },
          {
            title: homeI18n.eventTeensAdultsEaster,
            start: '2026-05-08T18:00:00',
            end:   '2026-05-08T20:00:00'
          },
          {
            title: homeI18n.eventGrading,
            start: '2026-01-30T18:00:00',
            end:   '2026-01-30T20:00:00'
          },
          {
            title: homeI18n.eventGrading,
            start: '2026-06-26T18:00:00',
            end:   '2026-06-26T20:00:00'
          }
        );

        events.sort((a, b) => a.start.localeCompare(b.start));
        return events;
      }

      function buildEventsToutClub() {
        const enfants = buildEventsEnfants();
        const ados    = buildEventsAdos();
        const adultes = buildEventsAdultes();
        const all = [...enfants, ...ados, ...adultes];
        const deduped = dedupeEvents(all);
        deduped.sort((a, b) => a.start.localeCompare(b.start));
        return deduped;
      }

      // --- Boutons : tÃ©lÃ©chargement des 4 fichiers ------------------
      const btnEnfants = document.getElementById('btnExportICSenfants');
      const btnAdos    = document.getElementById('btnExportICSados');
      const btnAdultes = document.getElementById('btnExportICSadultes');
      const btnTout    = document.getElementById('btnExportICStout');

      if (btnEnfants) {
        btnEnfants.addEventListener('click', function () {
          const evts = buildEventsEnfants();
          downloadICS(evts, 'kc-nalinnes-enfants-2025-2026.ics', homeI18n.icsChildrenName);
        });
      }
      if (btnAdos) {
        btnAdos.addEventListener('click', function () {
          const evts = buildEventsAdos();
          downloadICS(evts, 'kc-nalinnes-ados-2025-2026.ics', homeI18n.icsTeensName);
        });
      }
      if (btnAdultes) {
        btnAdultes.addEventListener('click', function () {
          const evts = buildEventsAdultes();
          downloadICS(evts, 'kc-nalinnes-adultes-2025-2026.ics', homeI18n.icsAdultsName);
        });
      }
      if (btnTout) {
        btnTout.addEventListener('click', function () {
          const evts = buildEventsToutClub();
          downloadICS(evts, 'kc-nalinnes-tout-club-2025-2026.ics', homeI18n.icsClubName);
        });
      }

      // --- Tarifs (toggle annuel/mensuel) ---------------------------
      try {
        const btnAnnual = document.getElementById('btn-annual');
        const btnMonthly = document.getElementById('btn-monthly');
        const amounts = document.querySelectorAll('.price-amount');
        const periods = document.querySelectorAll('.price-period');

        function setActive(mode) {
          if (!btnAnnual || !btnMonthly) return;
          const isAnnual = mode === 'annual';
          btnAnnual.classList.toggle('bg-slate-800', isAnnual);
          btnMonthly.classList.toggle('bg-slate-800', !isAnnual);
          btnAnnual.setAttribute('aria-selected', String(isAnnual));
          btnMonthly.setAttribute('aria-selected', String(!isAnnual));
        }

        function updatePrices(mode) {
          amounts.forEach(el => {
            const raw = el.dataset[mode];
            if (raw !== undefined) {
              const num = Number(raw);
              el.textContent = Number.isFinite(num) ? String(num) : raw;
            }
          });
          periods.forEach(el => { el.textContent = mode === 'monthly' ? <?= json_encode(kc_t('home.prices.period.month'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> : <?= json_encode(kc_t('home.prices.period.year'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>; });
          try { localStorage.setItem('pricingMode', mode); } catch (_) {}
          setActive(mode === 'monthly' ? 'monthly' : 'annual');
        }

        if (btnAnnual && btnMonthly) {
          btnAnnual.addEventListener('click', (e) => { e.preventDefault(); updatePrices('annual'); });
          btnMonthly.addEventListener('click', (e) => { e.preventDefault(); updatePrices('monthly'); });
          let saved = 'annual';
          try { saved = localStorage.getItem('pricingMode') || 'annual'; } catch (_) {}
          updatePrices(saved);
        }
      } catch (e) { console.error('Erreur toggle tarifs:', e); }

      // --- Tarifs2 (toggle annuel/mensuel) --------------------------
      try {
        const btnAnnual2 = document.getElementById('btn-annual2');
        const btnMonthly2 = document.getElementById('btn-monthly2');
        const amounts2 = document.querySelectorAll('.price-amount2');
        const periods2 = document.querySelectorAll('.price-period2');

        function setActive2(mode) {
          if (!btnAnnual2 || !btnMonthly2) return;
          const isAnnual = mode === 'annual';
          btnAnnual2.classList.toggle('bg-slate-800', isAnnual);
          btnMonthly2.classList.toggle('bg-slate-800', !isAnnual);
          btnAnnual2.setAttribute('aria-selected', String(isAnnual));
          btnMonthly2.setAttribute('aria-selected', String(!isAnnual));
        }

        function updatePrices2(mode) {
          amounts2.forEach(el => {
            const raw = el.dataset[mode];
            if (raw !== undefined) {
              const num = Number(raw);
              el.textContent = Number.isFinite(num) ? String(num) : raw;
            }
          });
          periods2.forEach(el => { el.textContent = mode === 'monthly' ? <?= json_encode(kc_t('home.prices.period.month'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> : <?= json_encode(kc_t('home.prices.period.year'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>; });
          try { localStorage.setItem('pricingMode2', mode); } catch (_) {}
          setActive2(mode === 'monthly' ? 'monthly' : 'annual');
        }

        if (btnAnnual2 && btnMonthly2) {
          btnAnnual2.addEventListener('click', (e) => { e.preventDefault(); updatePrices2('annual'); });
          btnMonthly2.addEventListener('click', (e) => { e.preventDefault(); updatePrices2('monthly'); });
          let saved = 'annual';
          try { saved = localStorage.getItem('pricingMode2') || 'annual'; } catch (_) {}
          updatePrices2(saved);
        }
      } catch (e) { console.error('Erreur toggle tarifs2:', e); }

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
            console.log('ServiceWorker enregistrÃ© avec succÃ¨s :', registration.scope);
          })
          .catch(function (error) {
            console.error('Ã‰chec de l\'enregistrement du ServiceWorker :', error);
          });
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      const videoIds = [
        "oyF3wvGo9h4",
        "aaIJ1yXx72Q",
        "VRtD-9_hNdw",
        "65QCHZDXKmA",
        "q1Rg8rUpjjw",
        "cuaRRRj8Xdc",
      ];

      const iframe = document.getElementById("random-youtube");
      if (!iframe || videoIds.length === 0) return;

      const pick = videoIds[Math.floor(Math.random() * videoIds.length)];

      const params = new URLSearchParams({
        autoplay: "1",
        mute: "1",             // essentiel pour lâ€™autoplay
        playsinline: "1",
        rel: "0",
        modestbranding: "1",
        iv_load_policy: "3",
        loop: "1",
        playlist: pick         // obligatoire pour que loop fonctionne
      });

      iframe.src = `https://www.youtube.com/embed/${pick}?${params.toString()}`;
      iframe.title = `YouTube video ${pick}`;
    });

  </script>
</body>
</html>
