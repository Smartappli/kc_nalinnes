<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Karaté Shotokan à Nalinnes — KC Nalinnes</title>

  <meta name="description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="https://www.kc-nalinnes.be/karate-shotokan.html" />

  <!-- Open Graph -->
  <meta property="og:title" content="Karaté Shotokan à Nalinnes — KC Nalinnes" />
  <meta property="og:description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://www.kc-nalinnes.be/" />
  <meta property="og:image" content="https://www.kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="Karaté Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Karaté Shotokan à Nalinnes — KC Nalinnes" />
  <meta name="twitter:description" content="Karaté Shotokan pour enfants, ados et adultes à Nalinnes. Ambiance familiale, instructeurs diplômés, progression ceintures, stages & compétitions. 1er cours d’essai gratuit." />
  <meta name="twitter:image" content="https://www.kc-nalinnes.be/assets/og-karate.jpg" />

  <meta name="theme-color" content="#0f172a" />

  <!-- PWA -->
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">
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

  /* Bouton à ne pas afficher à l'impression */
  .no-print {
    /* rien de spécial en écran, juste un hook pour @media print */
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

    /* On enlève le header, la nav mobile, les boutons de thème, le menu burger, etc. */
    header,
    #mobileNav,
    #themeToggle,
    #themeToggleMobile,
    #menuBtn {
      display: none !important;
    }

    /* On masque aussi les éléments marqués explicitement comme "no-print" */
    .no-print {
      display: none !important;
    }

    /* Le contenu principal prend toute la largeur de la page avec une marge papier propre */
    main {
      max-width: 100% !important;
      padding: 10mm 15mm !important;
    }

    /* On neutralise les fonds très sombres pour économiser l'encre */
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

    /* Texte simple en noir */
    h1, h2, h3, p, li, a {
      color: #000000 !important;
    }

    /* On enlève les ombres et les effets inutiles à l'impression */
    * {
      box-shadow: none !important;
      text-shadow: none !important;
      background-image: none !important;
    }

    /* Optionnel : afficher l'URL des liens après le texte */
    a[href]::after {
      content: " (" attr(href) ")";
      font-size: 0.8em;
    }

    /* Pas de saut de page juste après les titres si possible */
    h1, h2, h3 {
      page-break-after: avoid;
    }
  }

  /* Bouton "Imprimer cette page" – lisible en mode Light */
  html.light #printBtn {
    background-color: #e5e7eb !important;  /* slate-200 */
    color: #0f172a !important;             /* texte foncé */
    border-color: #cbd5e1 !important;      /* slate-300 */
  }

  html.light #printBtn:hover {
    background-color: #cbd5e1 !important;  /* un peu plus foncé au survol */
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
    <div class="no-print mb-6 flex justify-end">
      <button
        id="printBtn"
        type="button"
        onclick="window.print()"
        class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 border border-slate-700 hover:bg-slate-700 transition"
      >
        🖨️ Imprimer cette page
      </button>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
      Le karaté Shotokan
    </h1>

    <p class="text-sm text-slate-400 mb-8">
      Style traditionnel japonais, valeurs éducatives fortes et progression structurée pour enfants, ados et adultes.
    </p>

    <!-- 1. Qu’est-ce que le karaté Shotokan ? -->
    <section id="shotokan" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">1. Qu’est-ce que le karaté Shotokan&nbsp;?</h2>
      <p class="mb-3 text-slate-100/90">
        Le karaté Shotokan est un style traditionnel de karaté japonais, fondé au début du 20ᵉ siècle.
        Il se caractérise par des positions solides, des techniques longues et puissantes, et un travail
        précis de la respiration et du timing.
      </p>
      <p class="mb-3 text-slate-100/90">
        Au <strong>KC Nalinnes</strong>, nous utilisons le karaté Shotokan comme un outil d’éducation&nbsp;:
        apprendre à se défendre, mais surtout à se connaître soi-même, à gérer ses émotions
        et à respecter les autres.
      </p>
      <p class="mb-3 text-slate-100/90">
        Les cours alternent trois types de travail complémentaires&nbsp;:
      </p>
      <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
        <li><strong>Kihon</strong> : les techniques de base (déplacements, blocages, coups de poing, coups de pied).</li>
        <li><strong>Kata</strong> : des enchaînements codifiés qui développent mémoire, coordination et concentration.</li>
        <li><strong>Kumite</strong> : travail avec partenaire, d’abord très contrôlé, pour apprendre la distance et la maîtrise de soi.</li>
      </ul>
    </section>

    <!-- 2. Les valeurs -->
    <section id="valeurs" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">2. Les valeurs du karaté Shotokan</h2>
      <p class="mb-3 text-slate-100/90">
        Le karaté n’est pas seulement un sport de combat, c’est aussi une école de vie.
        Au KC Nalinnes, nous insistons sur quelques valeurs essentielles&nbsp;:
      </p>
      <ul class="space-y-2 text-slate-100/90">
        <li>
          <strong>Respect</strong> : saluer le dojo, le professeur, les partenaires&nbsp;; respecter le corps de l’autre.
        </li>
        <li>
          <strong>Maîtrise de soi</strong> : apprendre à canaliser son énergie et à ne pas réagir sous l’impulsion.
        </li>
        <li>
          <strong>Persévérance</strong> : accepter de recommencer, encore et encore, pour progresser.
        </li>
        <li>
          <strong>Humilité</strong> : il y a toujours quelque chose à apprendre, quel que soit son grade.
        </li>
        <li>
          <strong>Courage</strong> : oser essayer, même quand on a peur ou qu’on doute.
        </li>
      </ul>
      <p class="mt-4 text-slate-100/80">
        Ces valeurs sont rappelées à chaque entraînement, particulièrement aux enfants et aux adolescents,
        pour que ce qui est appris au dojo serve aussi à l’école, en famille et plus tard au travail.
      </p>
    </section>

    <!-- 3. Une séance type -->
    <section id="seance-type" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">3. Une séance type au KC Nalinnes</h2>
      <p class="mb-3 text-slate-100/90">
        Une séance dure environ une heure à une heure et demie, selon l’âge du groupe.
        Elle suit toujours une structure claire pour que chacun sache à quoi s’attendre&nbsp;:
      </p>
      <ol class="list-decimal pl-6 space-y-3 text-slate-100/90">
        <li>
          <strong>Saluts et mise en route</strong><br>
          Entrée au dojo, salut collectif, puis échauffement ludique (enfants) ou plus physique (ados / adultes).
        </li>
        <li>
          <strong>Travail technique (kihon)</strong><br>
          Révision des bases : positions, blocages, coups de poing, coups de pied, avec des exercices adaptés au niveau de chacun.
        </li>
        <li>
          <strong>Kata ou travail avec partenaire</strong><br>
          Apprentissage ou révision d’un kata, exercices à deux toujours encadrés et contrôlés pour développer précision et respect.
        </li>
        <li>
          <strong>Retour au calme et saluts</strong><br>
          Étirements, respiration, petit message sur une valeur (respect, persévérance…), puis salut final.
        </li>
      </ol>
      <p class="mt-4 text-slate-100/80">
        Notre objectif&nbsp;: que chacun reparte fatigué «&nbsp;juste comme il faut&nbsp;», fier de lui
        et avec l’envie de revenir.
      </p>
    </section>

    <!-- 4. Ceintures et progression -->
    <section id="ceintures" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">4. Les ceintures et la progression</h2>
      <p class="mb-3 text-slate-100/90">
        La progression en karaté se fait par <strong>ceintures de couleur</strong>, qui reflètent
        le niveau technique mais aussi l’attitude au dojo (assiduité, respect, comportement).
      </p>
      <p class="mb-3 text-slate-100/90">
        Les débutants commencent par la <strong>ceinture blanche</strong>, puis passent progressivement
        par différentes couleurs jusqu’à la <strong>ceinture noire</strong>.
      </p>
      <p class="mb-3 text-slate-100/90">
        Au KC Nalinnes&nbsp;:
      </p>
      <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
        <li>les passages de grade se font généralement une à deux fois par an&nbsp;;</li>
        <li>les critères sont expliqués à l’avance et adaptés à l’âge&nbsp;;</li>
        <li>
          chacun progresse à son rythme&nbsp;: l’objectif n’est pas d’aller «&nbsp;le plus vite possible&nbsp;»,
          mais de construire des bases solides.
        </li>
      </ul>
    </section>

    <!-- CTA Essai gratuit -->
    <section class="mt-12 mb-10">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-8">
        <h2 class="text-xl md:text-2xl font-bold mb-3">
          Envie d’essayer le karaté Shotokan&nbsp;?
        </h2>
        <p class="mb-4 text-slate-100/90">
          Le meilleur moyen de découvrir le karaté Shotokan, c’est de monter sur le tatami.
          Le premier cours d’essai est gratuit, sans engagement.
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
