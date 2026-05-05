const VERSION = 'v15';

const STATIC_CACHE = `kc-nalinnes-static-${VERSION}`;
const RUNTIME_CACHE = `kc-nalinnes-runtime-${VERSION}`;

const PRECACHE_ASSETS = [
  '/',
  '/favicon.ico',
  '/index.php',
  '/offline.html',
  '/karate-shotokan.php',
  '/kata-shotokan.php',
  '/vocabulaire-karate-shotokan.php',
  '/dojo-kun.php',
  '/technique_base.php',
  '/techniques_kumite.php',
  '/reviser_katas.php',
  '/stretching.php',
  '/mentions-legales.php',
  '/politique-confidentialite.php',
  '/manifest.webmanifest',
  '/assets/hero-karate.jpg',
  '/assets/og-karate.jpg',
  '/assets/sensei1.png',
  '/assets/sensei2.jpg',
  '/assets/senpai1.png',
  '/assets/senpai2.png',
  '/docs/409-FACVA024.pdf',
  '/docs/avantage-inscription club sportif.pdf',
  '/docs/Ethias_D_E9clarationAccident_45.339.711.pdf',
  '/docs/fichier_modulable_licence_pratiquant_avec_carnet.pdf',
  '/docs/Formulaire-de-demande-dintervention-Sports-2025.pdf',
  '/docs/mc_formulaire_AC_SPORT_A4_FR_2024_V2.pdf',
  '/docs/SC - sport.pdf',
  '/docs/mutualia-ac-sport-fr.pdf'
];

// ---------- Helpers ----------

function isNavigationRequest(request) {
  return (
    request.mode === 'navigate' ||
    (request.method === 'GET' &&
      request.headers.get('accept') &&
      request.headers.get('accept').includes('text/html'))
  );
}

function isCacheableResponse(response) {
  if (!response) return false;
  // On accepte :
  // - 200 OK
  // - opaque (CDN no-cors)
  // - cors (CDN avec CORS correct)
  if (response.type === 'opaque' || response.type === 'cors') return true;
  return response.status === 200;
}

async function networkFirst(request) {
  const cache = await caches.open(STATIC_CACHE);
  try {
    const response = await fetch(request);
    if (isCacheableResponse(response)) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (err) {
    const cached = await cache.match(request);
    if (cached) return cached;

    const fallback = await cache.match('/offline.html') || await cache.match('/index.php');
    if (fallback) return fallback;

    throw err;
  }
}

async function staleWhileRevalidate(request, cacheName) {
  const cache = await caches.open(cacheName);
  const cached = await cache.match(request);

  const fetchPromise = fetch(request)
    .then((response) => {
      if (isCacheableResponse(response)) {
        cache.put(request, response.clone());
      }
      return response;
    })
    .catch(() => undefined);

  // On renvoie le cache si présent, sinon le réseau
  return cached || fetchPromise;
}

async function cacheFirst(request, cacheName) {
  const cache = await caches.open(cacheName);
  const cached = await cache.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (isCacheableResponse(response)) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (err) {
    // Pas de fallback spécifique ici (PDF/images) : on laisse l’erreur remonter
    throw err;
  }
}

// ---------- INSTALL ----------

self.addEventListener('install', (event) => {
  console.log('[SW] install', VERSION);
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) =>
        Promise.all(
          PRECACHE_ASSETS.map((url) =>
            cache.add(url).catch((err) => {
              console.warn('[SW] Échec de pré-cache pour', url, err);
            })
          )
        )
      )
      .then(() => {
        console.log('[SW] skipWaiting');
        return self.skipWaiting();
      })
  );
});

// ---------- ACTIVATE ----------

self.addEventListener('activate', (event) => {
  console.log('[SW] activate', VERSION);
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== STATIC_CACHE && key !== RUNTIME_CACHE)
          .map((key) => {
            console.log('[SW] delete old cache', key);
            return caches.delete(key);
          })
      )
    ).then(() => self.clients.claim())
  );
});

// ---------- FETCH ----------

self.addEventListener('fetch', (event) => {
  const request = event.request;

  // On ne gère que les GET
  if (request.method !== 'GET') return;

  const url = new URL(request.url);

  // 1. Navigations (pages HTML) : network-first + fallback cache
  if (isNavigationRequest(request)) {
    event.respondWith(networkFirst(request));
    return;
  }

  // 2. Même origine : stratégies par type de ressource
  if (url.origin === self.location.origin) {
    // CSS / JS : stale-while-revalidate (chargement rapide + mise à jour en fond)
    if (/\.(css|js)$/.test(url.pathname)) {
      event.respondWith(staleWhileRevalidate(request, STATIC_CACHE));
      return;
    }

    // Images & PDFs : cache-first
    if (/\.(png|jpe?g|gif|svg|webp|pdf)$/.test(url.pathname)) {
      event.respondWith(cacheFirst(request, RUNTIME_CACHE));
      return;
    }

    // Fallback pour le reste en même origine : stale-while-revalidate
    event.respondWith(staleWhileRevalidate(request, RUNTIME_CACHE));
    return;
  }

  // 3. Externe (CDN, Google Fonts, FullCalendar, etc.)
  //    → cache-first pour améliorer les perfs, mais pas obligatoire
  //    On EVITE de cacher Google Maps iframe (peu utile offline)
  if (/google\.com|maps\.googleapis\.com/.test(url.hostname)) {
    // Laisser passer en direct, pas de cache
    event.respondWith(fetch(request).catch(() => caches.match(request)));
    return;
  }

  // Autres CDNs (fonts, JS, CSS) : cache-first
  event.respondWith(cacheFirst(request, RUNTIME_CACHE));
});
