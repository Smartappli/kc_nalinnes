<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>S'entraîner aux kata Shotokan — KC Nalinnes</title>

  <meta name="description" content="S'entraîner aux kata du karaté Shotokan avec le KC Nalinnes : vidéos YouTube par niveau (débutants, intermédiaires, avancés), conseils pour réviser avant les passages de grade." />
  <meta name="robots" content="index,follow" />
  <!-- Aligné avec og:url + lien du menu -->
  <link rel="canonical" href="https://www.kc-nalinnes.be/entrainement-kata.html" />

  <!-- Open Graph -->
  <meta property="og:title" content="S'entraîner aux kata Shotokan — KC Nalinnes" />
  <meta property="og:description" content="Vidéos de kata du KC Nalinnes pour s'entraîner à la maison : Heian, Tekki, Bassai Dai, Jion... par niveau et par ceinture." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://www.kc-nalinnes.be/entrainement-kata.html" />
  <meta property="og:image" content="https://www.kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="Karaté Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="S'entraîner aux kata Shotokan — KC Nalinnes" />
  <meta name="twitter:description" content="Révise tes kata Shotokan avec les vidéos du KC Nalinnes : par niveau, par ceinture, avec conseils pratiques." />
  <meta name="twitter:image" content="https://www.kc-nalinnes.be/assets/og-karate.jpg" />

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
        <a href="#kata-debutants" class="underline underline-offset-4 hover:text-sky-400">Débutants</a>
        <span>•</span>
        <a href="#kata-intermediaires" class="underline underline-offset-4 hover:text-sky-400">Intermédiaires</a>
        <span>•</span>
        <a href="#kata-avances" class="underline underline-offset-4 hover:text-sky-400">Avancés</a>
      </div>

      <button
        id="printBtn"
        type="button"
        onclick="window.print()"
        class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 border border-slate-700 hover:bg-slate-700 transition"
      >
        🖨️ Imprimer la liste des kata
      </button>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
      S'entraîner aux kata Shotokan
    </h1>

    <p class="text-sm text-slate-400 mb-6">
      Une sélection de vidéos pour réviser tes kata à la maison. Utilise-les comme support :
      rien ne remplace les corrections du dojo, mais cela t’aidera à mémoriser les enchaînements.
    </p>

    <!-- Bloc conseils -->
    <section class="mb-10 section">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-6">
        <h2 class="text-xl font-bold mb-3">Quelques conseils avant de commencer</h2>
        <ul class="list-disc pl-6 space-y-2 text-slate-100/90">
          <li>Fais un petit échauffement (articulations, jambes, hanches, épaules) avant de travailler tes kata.</li>
          <li>Commence par <strong>regarder</strong> la vidéo une fois sans bouger, puis refais le kata en même temps que la vidéo.</li>
          <li>Travaille d’abord <strong>lentement</strong>, en cherchant la précision des positions plutôt que la vitesse.</li>
          <li>Note les parties où tu hésites, et demande au sensei au cours suivant.</li>
        </ul>
      </div>
    </section>

    <!-- Kata débutants -->
    <section id="kata-debutants" class="mb-12 section">
      <h2 class="text-2xl font-bold mb-3">Kata débutants – Ceintures blanches / jaunes / orange</h2>
      <p class="mb-4 text-slate-100/90">
        Idéal pour les enfants et débutants. Concentre-toi sur les positions, les directions et le rythme.
      </p>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Heian Shodan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/q1Rg8rUpjjw"
              title="Heian Shodan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Heian Shodan (Paix et tranquillité)</h3>
          <p class="text-sm text-slate-400 mb-1">Ceintures blanches → jaunes</p>
          <p class="text-sm text-slate-100/80">
            Kata de base : embusen clair, pivots propres et stabilité en zenkutsu dachi. Cherche des blocages nets (gedan barai) et un oi-zuki bien aligné.
          </p>
        </article>

        <!-- Heian Nidan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/rgs1ysn0R-0"
              title="Heian Nidan - Tutoriel complet"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Heian Nidan (Paix et tranquillité)</h3>
          <p class="text-sm text-slate-400 mb-1">Ceintures jaunes / orange</p>
          <p class="text-sm text-slate-100/80">
            Introduit davantage de coordination et de variations de positions (notamment kokutsu dachi). Soigne le timing bras/jambes et la précision des techniques en hauteur.
          </p>
        </article>

        <!-- Heian Sandan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/1MrRmimBJoA"
              title="Heian Sandan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Heian Sandan (Paix et tranquillité)</h3>
          <p class="text-sm text-slate-400 mb-1">Fin débutants / début intermédiaires</p>
          <p class="text-sm text-slate-100/80">
            Kata charnière : transitions plus riches, travail du bassin et positions plus variées (dont kiba dachi). Vise la stabilité et des changements de direction “sans flottement”.
          </p>
        </article>
      </div>
    </section>

    <!-- Kata intermédiaires -->
    <section id="kata-intermediaires" class="mb-12 section">
      <h2 class="text-2xl font-bold mb-3">Kata intermédiaires – Ceintures verte / bleue / marron</h2>
      <p class="mb-4 text-slate-100/90">
        Tu maîtrises déjà les bases : ici, on ajoute du rythme, des changements de niveau et plus de puissance.
      </p>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Heian Yondan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/k72E1u962Qg"
              title="Heian Yondan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Heian Yondan (Paix et tranquillité)</h3>
          <p class="text-sm text-slate-400 mb-1">Intermédiaire</p>
          <p class="text-sm text-slate-100/80">
            Mets l’accent sur les changements de niveau, les pivots et la précision des blocages. Cherche des positions basses, mais vivantes (pas “écrasées”).
          </p>
        </article>

        <!-- Heian Godan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/JA0Ym97vjLg"
              title="Heian Godan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Heian Godan</h3>
          <p class="text-sm text-slate-400 mb-1">Intermédiaire +</p>
          <p class="text-sm text-slate-100/80">
            Kata plus explosif : garde le contrôle sur les transitions rapides et le saut. Le but : vitesse + kime, sans perdre l’équilibre ni la ligne.
          </p>
        </article>

        <!-- Tekki Shodan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/uifXvEXd_NU"
              title="Tekki Shodan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Tekki Shodan (Cavalier de fer)</h3>
          <p class="text-sm text-slate-400 mb-1">Travail des hanches et de la stabilité</p>
          <p class="text-sm text-slate-100/80">
            Tout se joue sur la stabilité en kiba dachi et la rotation des hanches. Puissance “à plat” (sans avancer), épaules relâchées, bassin moteur.
          </p>
        </article>
      </div>
    </section>

    <!-- Kata avancés -->
    <section id="kata-avances" class="mb-12 section">
      <h2 class="text-2xl font-bold mb-3">Kata avancés – ceinture noire</h2>
      <p class="mb-4 text-slate-100/90">
        Pour les ceintures marron et noires, ou les élèves motivés qui veulent déjà regarder ce qui les attend.
      </p>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Bassai Dai -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/Qpt3W7Y06Kg"
              title="Bassai Dai - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Bassai Dai (Pénétrer la forteresse)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé classique</p>
          <p class="text-sm text-slate-100/80">
            Travaille l’alternance puissance/contrôle : cassures de rythme, changements de direction tranchants et transitions propres entre positions (kokutsu ↔ zenkutsu).
          </p>
        </article>

        <!-- Kanku Dai -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/5Hgi2vi9EbA"
              title="Kanku Dai (Full Tutorial) - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Kanku Dai (Regarder le ciel)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata long : vise un embusen impeccable, des transitions nettes et un rythme maîtrisé du début à la fin. Le saut doit rester contrôlé, pas “jeté”.
          </p>
        </article>

        <!-- Empi -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/IInNlHZQUrE"
              title="Empi - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Empi (Vol de l'hirondelle)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata rapide et technique : pivots précis, explosivité sur les actions en chaîne et contrôle du centre de gravité. Cherche la vitesse sans précipitation.
          </p>
        </article>

        <!-- Jion -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/KErsdtTwqM8"
              title="Jion - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Jion (Du nom d’un temple bouddhiste)</h3>
          <p class="text-sm text-slate-400 mb-1">Ceintures marron / noires</p>
          <p class="text-sm text-slate-100/80">
            Kata “solide” : posture haute, kime franc et cadence régulière. Idéal pour travailler la présence, la ligne et la puissance sobre.
          </p>
        </article>

        <!-- Hangetsu -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/vOP8dAalfms"
              title="Hangetsu - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Hangetsu (Demi-lune)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Accent sur respiration, tension/relâchement et stabilité en hangetsu dachi. Les déplacements doivent rester fluides et “tirés au sol”, sans rebond.
          </p>
        </article>

        <!-- Tekki Nidan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/uifXvEXd_NU"
              title="Tekki Nidan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Tekki Nidan (Cavalier de fer)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Même embusen latéral : renforce la coordination hanche/bras et la stabilité du kiba dachi. Les techniques doivent partir du bassin, pas des épaules.
          </p>
        </article>

        <!-- Bassai Sho -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/zpnA13Vg1lY"
              title="Bassai Sho - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Bassai Sho (Pénétrer la forteresse)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Plus compact et plus vif que Bassai Dai : pivots rapides, transitions courtes et précision des angles. Cherche la fluidité sans perdre l’impact.
          </p>
        </article>

        <!-- Gankaku -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/OJi3lwnx0jI"
              title="Gankaku - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Gankaku (Grue sur un rocher)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata d’équilibre : stabilité en gankaku dachi, montée de genou contrôlée et attaques bien “posées”. Le regard guide les pivots.
          </p>
        </article>

        <!-- Jiin -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/b8RcGoE7XiI"
              title="Jiin - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Jiin (Kata du groupe Jion)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Proche de Jion mais plus fluide : angles propres, continuité des déplacements et puissance sans rigidité. Le rythme doit rester “coulé” et maîtrisé.
          </p>
        </article>

        <!-- Jitte -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/IWZOQd6dvww"
              title="Jitte - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Jitte (Dix mains)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata de défenses fortes : positions solides, blocages amples et sensation de “contre” après chaque défense. Cherche la densité, pas la vitesse.
          </p>
        </article>

        <!-- Kanku Sho -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/veaayoYQ9D4"
              title="Kanku Sho - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Kanku Sho (Regarder le ciel)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Variante plus courte et nerveuse : tempo dynamique, changements de niveau et contrôle du saut. Kime très marqué sur les fins de séquences.
          </p>
        </article>

        <!-- Meikyo -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/HbX9X3JEI1E"
              title="Meikyo - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Meikyo (Polir le miroir)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Mélange subtil de lent/rapide : pivots propres, contrôle des positions et timing précis sur les techniques de mains ouvertes. Cherche la clarté des trajectoires.
          </p>
        </article>

        <!-- Nijushiho -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/ZE8EqPvwruE"
              title="Nijushiho - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Nijushiho (24 pas)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata très exigeant : précision des directions, transitions rapides et kime net tout en gardant de la fluidité. Rien ne doit “flotter”.
          </p>
        </article>

        <!-- Sochin -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/KqmlVtZZuxY"
              title="Sochin - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Sochin (Force tranquille)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata de puissance ancrée : stabilité en fudo dachi, poussée dans le sol et kime lourd. Le haut reste calme, le bas “porte” tout.
          </p>
        </article>

        <!-- Tekki Sandan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/uifXvEXd_NU"
              title="Tekki Sandan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Tekki Sandan (Cavalier de fer)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Le plus dense des Tekki : enchaînements courts, bassin très actif et stabilité constante. Objectif : puissance compacte, sans se redresser.
          </p>
        </article>

        <!-- Unsu -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/MjVKvHf_Ny0"
              title="Unsu - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Unsu (Main en nuage)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Très dynamique : coordination, rotations rapides et contrôle du saut/retombée. Priorité à la précision des axes et à l’équilibre en sortie.
          </p>
        </article>

        <!-- Chinte -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/RgwDv8MChWg"
              title="Chinte - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Chinte (Main secrète)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Positions courtes et techniques atypiques : précision des trajectoires, contrôle sur les petites bases et équilibre. Ne “raccourcis” pas : reste propre.
          </p>
        </article>

        <!-- Gojushiho Dai -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/qVGcqVEBRRs"
              title="Gojushiho Dai - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Gojushiho Dai (54 pas)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Beaucoup de mains ouvertes : relâchement des épaules, légèreté et transitions rapides (souvent en neko ashi dachi). Cherche la finesse, pas la force brute.
          </p>
        </article>

        <!-- Gojushiho Sho -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/c2qiJNrCYGw"
              title="Gojushiho Sho - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Gojushiho Sho (54 pas)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Variante plus compacte : angles serrés, vitesse contrôlée et enchaînements propres à mains ouvertes. Garde le corps “léger” et mobile.
          </p>
        </article>

        <!-- Wankan -->
        <article class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 flex flex-col">
          <div class="aspect-video w-full overflow-hidden rounded-xl border border-slate-800 mb-3">
            <iframe
              class="w-full h-full"
              src="https://www.youtube.com/embed/DzFP2UilMtI"
              title="Wankan - ULTIMATE KARATE"
              frameborder="0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
          <h3 class="font-semibold text-lg mb-1">Wankan (Couronne du roi)</h3>
          <p class="text-sm text-slate-400 mb-1">Kata avancé (Shotokan)</p>
          <p class="text-sm text-slate-100/80">
            Kata court : rythme, explosivité et netteté des techniques. Le défi : être tranchant sans accélérer au hasard.
          </p>
        </article>

      </div>
    </section>

    <!-- CTA Essai gratuit -->
    <section class="mt-12 mb-10">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-8">
        <h2 class="text-xl md:text-2xl font-bold mb-3">
          Tu veux t'entraîner aussi au dojo&nbsp;?
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
