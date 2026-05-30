<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Les kata en karaté Shotokan — KC Nalinnes</title>

  <meta name="description" content="Comprendre ce qu’est un kata en karaté Shotokan, découvrir les principaux kata par niveau et apprendre à les réviser. Ressource pédagogique pour les élèves du KC Nalinnes." />
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="https://kc-nalinnes.be/kata-shotokan.php" />

  <!-- Open Graph -->
  <meta property="og:title" content="Les kata en karaté Shotokan — KC Nalinnes" />
  <meta property="og:description" content="Qu’est-ce qu’un kata ? À quoi servent-ils ? Quels sont les kata en karaté Shotokan et comment les réviser ? Une page pour les élèves du KC Nalinnes." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://kc-nalinnes.be/kata-shotokan.php" />
  <meta property="og:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />
  <meta property="og:locale" content="fr_BE" />
  <meta property="og:site_name" content="Karaté Club Nalinnes" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Les kata en karaté Shotokan — KC Nalinnes" />
  <meta name="twitter:description" content="Une introduction claire aux kata de karaté Shotokan pour les élèves du KC Nalinnes." />
  <meta name="twitter:image" content="https://kc-nalinnes.be/assets/og-karate.jpg" />

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

    /* Petites tables pédagogiques */
    .vocab-table td,
    .vocab-table th {
      padding: 0.4rem 0.6rem;
    }

    /* -------- Bouton "Imprimer cette page" : mode Light lisible -------- */

    html.light #printBtn {
      background-color: #e5e7eb !important;  /* slate-200 */
      color: #0f172a !important;             /* texte foncé */
      border-color: #cbd5e1 !important;      /* slate-300 */
    }

    html.light #printBtn:hover {
      background-color: #cbd5e1 !important;  /* un peu plus foncé au survol */
      color: #0f172a !important;
    }

    /* -------- Impression : cacher header / nav / bouton / fond sombre -------- */

    .no-print {}

    @media print {
      html, body {
        background: #ffffff !important;
        color: #000000 !important;
      }

      body {
        margin: 0;
      }

      header,
      #mobileNav,
      .no-print {
        display: none !important;
      }

      main {
        max-width: 100%;
        padding: 1.5cm;
      }

      a {
        color: #000000 !important;
        text-decoration: none;
      }

      a::after {
        content: " (" attr(href) ")";
        font-size: 0.8em;
      }
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
      Les kata en karaté Shotokan
    </h1>

    <p class="text-sm text-slate-400 mb-8">
      Une page pour mieux comprendre les kata&nbsp;: ce qu’ils sont, à quoi ils servent et
      quels sont les kata que tu rencontreras au <strong>KC Nalinnes</strong>.
    </p>

    <!-- 1. Qu’est-ce qu’un kata ? -->
    <section id="definition" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">1. Qu’est-ce qu’un kata&nbsp;?</h2>
      <p class="mb-3 text-slate-100/90">
        Un <strong>kata</strong> est une suite de techniques codifiées (positions, blocages, coups de poing,
        coups de pied…) réalisée dans un ordre précis et sur une trajectoire définie.
        On peut l’imaginer comme un «&nbsp;combat imaginaire&nbsp;» contre plusieurs adversaires.
      </p>
      <p class="mb-3 text-slate-100/90">
        Chaque kata a son propre rythme, sa propre personnalité&nbsp;: certains sont plutôt calmes
        et puissants, d’autres rapides et explosifs. En les répétant, on travaille&nbsp;:
      </p>
      <ul class="list-disc pl-6 space-y-1 text-slate-100/90">
        <li>la mémoire et la concentration&nbsp;;</li>
        <li>les déplacements et les changements de direction&nbsp;;</li>
        <li>la précision des techniques (hauteur, trajectoire, timing)&nbsp;;</li>
        <li>la respiration, la puissance et la maîtrise de soi.</li>
      </ul>
      <p class="mt-4 text-slate-100/80">
        Aux passages de grade et en compétition, les kata jouent un rôle très important.
      </p>
    </section>

    <!-- 2. Le rôle des kata au KC Nalinnes -->
    <section id="role" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">2. Le rôle des kata au KC Nalinnes</h2>
      <p class="mb-3 text-slate-100/90">
        Au <strong>KC Nalinnes</strong>, les kata ne sont pas juste «&nbsp;un exercice à apprendre par cœur&nbsp;».
        Ils sont utilisés comme un outil pédagogique complet.
      </p>

      <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <h3 class="text-lg font-semibold mb-2">Technique</h3>
          <p class="text-slate-100/90">
            Travailler un kata permet de revoir presque toutes les bases&nbsp;: positions, blocages,
            coups de poing et coups de pied, transitions…
          </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <h3 class="text-lg font-semibold mb-2">Mental</h3>
          <p class="text-slate-100/90">
            Devant le groupe ou lors d’un passage de grade, le kata aide
            à gérer le stress, la timidité et la concentration.
          </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <h3 class="text-lg font-semibold mb-2">Valeurs</h3>
          <p class="text-slate-100/90">
            Respect des consignes, persévérance, courage d’essayer,
            acceptation de l’erreur&nbsp;: tout cela se retrouve dans le travail des kata.
          </p>
        </div>
      </div>
    </section>

    <!-- 3. Quelques kata par niveau (indicatif) -->
    <section id="liste" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">3. Quelques kata par niveau (indicatif)</h2>
      <p class="mb-3 text-slate-100/90">
        Voici une liste <strong>indicative</strong> de kata que l’on retrouve en karaté Shotokan.
        L’ordre exact et les exigences peuvent varier selon l’âge et la progression&nbsp;:
        c’est toujours ton <strong>Sensei</strong> qui te dira quel kata travailler pour le prochain grade.
      </p>

      <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60">
        <table class="min-w-full text-sm vocab-table">
          <thead class="border-b border-slate-800 text-left text-slate-100/80">
            <tr>
              <th>Niveau approximatif</th>
              <th>Ceintures</th>
              <th>Kata principaux</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800">
            <tr>
              <td><strong>Débutants</strong></td>
              <td>Blanche, jaune, orange</td>
              <td>
                <ul class="list-disc pl-4 space-y-1">
                  <li><strong>Heian Shodan</strong></li>
                  <li><strong>Heian Nidan</strong></li>
                </ul>
              </td>
            </tr>
            <tr>
              <td><strong>Intermédiaire</strong></td>
              <td>Verte, bleue</td>
              <td>
                <ul class="list-disc pl-4 space-y-1">
                  <li><strong>Heian Sandan</strong></li>
                  <li><strong>Heian Yondan</strong></li>
                  <li><strong>Heian Godan</strong></li>
                  <li><strong>Tekki Shodan</strong></li>
                </ul>
              </td>
            </tr>
            <tr>
              <td><strong>Avancé</strong></td>
              <td>Marron, noire</td>
              <td>
                <ul class="list-disc pl-4 space-y-1">
                  <li><strong>Bassai Dai</strong></li>
                  <li><strong>Kanku Dai</strong></li>
                  <li><strong>Jion</strong></li>
                  <li><strong>Enpi</strong></li>
                  <li><strong>Hangetsu</strong></li>
                  <li><strong>Gankaku</strong></li>
                  <li><strong>Jitte</strong></li>
                </ul>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="mt-4 text-slate-100/80">
        Si tu ne connais pas encore tous ces noms, ce n’est pas grave&nbsp;: l’important est
        d’apprendre <strong>kata par kata</strong>, étape par étape.
      </p>
    </section>

    <!-- 4. Comment réviser un kata chez soi -->
    <section id="revision" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">4. Comment réviser un kata chez soi&nbsp;?</h2>
      <p class="mb-3 text-slate-100/90">
        Tu peux réviser ton kata même dans un petit espace. L’idée n’est pas d’aller vite,
        mais de comprendre ce que tu fais et dans quel ordre.
      </p>

      <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <h3 class="text-lg font-semibold mb-2">Étape par étape</h3>
          <ul class="space-y-2 text-slate-100/90">
            <li>Refais le kata <strong>lentement</strong>, technique par technique.</li>
            <li>Vérifie ta <strong>direction</strong> (avant, arrière, côté droit, côté gauche).</li>
            <li>Contrôle ta <strong>position</strong> (largeur, longueur, genoux fléchis).</li>
            <li>Pense à ta <strong>respiration</strong> : ne bloque pas l’air.</li>
          </ul>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
          <h3 class="text-lg font-semibold mb-2">Quelques astuces</h3>
          <ul class="space-y-2 text-slate-100/90">
            <li>Écris la liste des mouvements sur une feuille.</li>
            <li>Répète le kata <strong>sans puissance</strong> pour mémoriser le chemin.</li>
            <li>Entraîne-toi devant un miroir ou une fenêtre pour corriger tes positions.</li>
            <li>Demande à ton professeur ce que signifient certaines parties du kata
              (<em>bunkai</em>).</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- 5. Le déroulement d’un kata en passage de grade / compétition -->
    <section id="examen" class="mb-10">
      <h2 class="text-2xl font-bold mb-3">5. Le déroulement d’un kata en passage de grade ou en compétition</h2>
      <p class="mb-3 text-slate-100/90">
        Même si les règles exactes peuvent varier, le déroulement est souvent similaire.
      </p>

      <ol class="list-decimal pl-6 space-y-3 text-slate-100/90">
        <li>
          <strong>Entrée et salut</strong><br>
          Tu entres sur l’aire de travail, tu te places, puis tu salues (vers le jury ou le Sensei).
        </li>
        <li>
          <strong>Annonce du kata</strong><br>
          Tu annonces clairement le nom du kata (par exemple&nbsp;: <em>« Heian Nidan »</em>),
          puis tu te mets en position de départ.
        </li>
        <li>
          <strong>Exécution du kata</strong><br>
          Tu réalises le kata du début à la fin, sans t’arrêter,
          en essayant de montrer&nbsp;: stabilité, précision, puissance, contrôle.
        </li>
        <li>
          <strong>Fin et salut</strong><br>
          Tu reviens à la position de départ, tu attends un petit instant,
          puis tu salues à nouveau avant de sortir.
        </li>
      </ol>

      <p class="mt-4 text-slate-100/80">
        Même si tu te trompes, l’important est de rester calme, de te replacer
        et de continuer jusqu’au bout.
      </p>
    </section>

    <!-- 6. Petit glossaire autour des kata -->
    <section id="glossaire" class="mt-12 mb-10">
      <div class="rounded-2xl border border-slate-800 bg-slate-900/60 px-6 py-6 md:px-8 md:py-8">
        <h2 class="text-xl md:text-2xl font-bold mb-3">
          Petit glossaire autour des kata
        </h2>
        <ul class="list-disc pl-6 space-y-2 text-slate-100/90">
          <li><strong>Embusen</strong> — le «&nbsp;chemin&nbsp;» suivi par le kata (trajectoire au sol).</li>
          <li><strong>Kiai</strong> — cri bref et puissant pour marquer un moment fort du kata.</li>
          <li><strong>Bunkai</strong> — application du kata avec un partenaire, pour comprendre
            à quoi servent les mouvements.</li>
          <li><strong>Kihon</strong> — techniques de base (que l’on retrouve dans les kata).</li>
          <li><strong>Kata</strong> — forme codifiée, enchaînement structuré de techniques.</li>
        </ul>
        <p class="mt-4 text-slate-100/80">
          Tu peux aussi jeter un œil à la page
          <a href="/vocabulaire-karate-shotokan.php" class="text-sky-400 hover:text-sky-300 underline">
            Vocabulaire & compter en japonais
          </a>
          pour revoir les mots japonais que tu entends au dojo.
        </p>
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
