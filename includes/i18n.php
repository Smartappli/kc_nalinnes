<?php
declare(strict_types=1);

function kc_supported_locales(): array {
    return [
        'bg',
        'cs',
        'da',
        'de',
        'el',
        'en',
        'es',
        'et',
        'fi',
        'fr',
        'ga',
        'hr',
        'hu',
        'it',
        'ja',
        'lt',
        'lv',
        'mt',
        'nl',
        'pl',
        'pt',
        'ro',
        'sk',
        'sl',
        'sv',
    ];
}

function kc_translation_modules(): array {
    return ['common', 'reservation-repas', 'member-dashboard', 'manager-dashboard', 'contact', 'membres', 'commandes', 'legal', 'pages', 'home', 'fill-licence'];
}

function kc_default_locale(): string {
    return 'fr';
}

function kc_fallback_locales(string $locale): array {
    $locale = kc_normalize_locale($locale);
    $fallbacks = [$locale];

    foreach (['en', kc_default_locale()] as $fallback) {
        if (!in_array($fallback, $fallbacks, true)) {
            $fallbacks[] = $fallback;
        }
    }

    return $fallbacks;
}

function kc_locale_labels(): array {
    return [
        'bg' => 'Български',
        'cs' => 'Čeština',
        'da' => 'Dansk',
        'de' => 'Deutsch',
        'el' => 'Ελληνικά',
        'en' => 'English',
        'es' => 'Español',
        'et' => 'Eesti',
        'fi' => 'Suomi',
        'fr' => 'Français',
        'ga' => 'Gaeilge',
        'hr' => 'Hrvatski',
        'hu' => 'Magyar',
        'it' => 'Italiano',
        'ja' => '日本語',
        'lt' => 'Lietuvių',
        'lv' => 'Latviešu',
        'mt' => 'Malti',
        'nl' => 'Nederlands',
        'pl' => 'Polski',
        'pt' => 'Português',
        'ro' => 'Română',
        'sk' => 'Slovenčina',
        'sl' => 'Slovenščina',
        'sv' => 'Svenska',
    ];
}

function kc_locale_flags(): array {
    return [
        'bg' => '🇧🇬',
        'cs' => '🇨🇿',
        'da' => '🇩🇰',
        'de' => '🇩🇪',
        'el' => '🇬🇷',
        'en' => '🇬🇧',
        'es' => '🇪🇸',
        'et' => '🇪🇪',
        'fi' => '🇫🇮',
        'fr' => '🇫🇷',
        'ga' => '🇮🇪',
        'hr' => '🇭🇷',
        'hu' => '🇭🇺',
        'it' => '🇮🇹',
        'ja' => '🇯🇵',
        'lt' => '🇱🇹',
        'lv' => '🇱🇻',
        'mt' => '🇲🇹',
        'nl' => '🇳🇱',
        'pl' => '🇵🇱',
        'pt' => '🇵🇹',
        'ro' => '🇷🇴',
        'sk' => '🇸🇰',
        'sl' => '🇸🇮',
        'sv' => '🇸🇪',
    ];
}

function kc_normalize_locale(?string $locale): string {
    $locale = strtolower(trim((string)$locale));
    $locale = str_replace('_', '-', $locale);
    $locale = explode('-', $locale, 2)[0];

    return in_array($locale, kc_supported_locales(), true) ? $locale : kc_default_locale();
}

function kc_current_locale(): string {
    static $locale = null;

    if ($locale !== null) {
        return $locale;
    }

    if (isset($_GET['lang'])) {
        $locale = kc_normalize_locale((string)$_GET['lang']);
        setcookie('kc_locale', $locale, [
            'expires' => time() + 31536000,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);

        return $locale;
    }

    if (isset($_POST['lang'])) {
        $locale = kc_normalize_locale((string)$_POST['lang']);
        setcookie('kc_locale', $locale, [
            'expires' => time() + 31536000,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);

        return $locale;
    }

    if (isset($_COOKIE['kc_locale'])) {
        $locale = kc_normalize_locale((string)$_COOKIE['kc_locale']);
        return $locale;
    }

    $locale = kc_normalize_locale($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
    return $locale;
}

function kc_load_translation_file(string $locale, string $module): array {
    $locale = kc_normalize_locale($locale);
    $module = basename($module);
    $path = dirname(__DIR__) . '/translations/' . $locale . '/' . $module . '.php';

    if (!is_file($path)) {
        return [];
    }

    $translations = require $path;
    return is_array($translations) ? $translations : [];
}

function kc_translations(): array {
    static $translations = null;

    if ($translations !== null) {
        return $translations;
    }

    $translations = [];
    foreach (kc_supported_locales() as $locale) {
        $translations[$locale] = [];
        foreach (kc_translation_modules() as $module) {
            $translations[$locale] = array_replace(
                $translations[$locale],
                kc_load_translation_file($locale, $module)
            );
        }
    }

    return $translations;
}

function kc_t(string $key, array $replace = [], ?string $locale = null): string {
    $locale = kc_normalize_locale($locale ?? kc_current_locale());
    $translations = kc_translations();
    $value = $key;

    foreach (kc_fallback_locales($locale) as $candidateLocale) {
        if (isset($translations[$candidateLocale][$key])) {
            $value = $translations[$candidateLocale][$key];
            break;
        }
    }

    foreach ($replace as $name => $replacement) {
        $value = str_replace('{' . $name . '}', (string)$replacement, $value);
    }

    return $value;
}

function kc_localized_url(string $locale, ?string $path = null): string {
    $locale = kc_normalize_locale($locale);
    $path = $path ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $query = $_GET;
    $query['lang'] = $locale;
    $queryString = http_build_query($query);

    return $path . ($queryString !== '' ? '?' . $queryString : '');
}

function kc_redirect_url_with_locale(string $path): string {
    return kc_localized_url(kc_current_locale(), $path);
}

function kc_translate_guard_attr(string $locale): string {
    return kc_normalize_locale($locale) === kc_default_locale() ? ' translate="no"' : '';
}

function kc_google_notranslate_meta(string $locale): string {
    return kc_normalize_locale($locale) === kc_default_locale()
        ? '<meta name="google" content="notranslate">' . PHP_EOL
        : '';
}

function kc_should_use_page_translation(): bool {
    $script = basename((string)($_SERVER['SCRIPT_NAME'] ?? $_SERVER['SCRIPT_FILENAME'] ?? ''));

    return in_array($script, [
        'index.php',
        'commandes.php',
        'contact.php',
        'dojo-kun.php',
        'karate-shotokan.php',
        'kata-shotokan.php',
        'membres.php',
        'reservation-repas.php',
        'reviser_katas.php',
        'stretching.php',
        'technique_base.php',
        'techniques_kumite.php',
        'vocabulaire-karate-shotokan.php',
        'mentions-legales.php',
        'politique-confidentialite.php',
        'dashboard.php',
    ], true);
}

function kc_auto_translation_map(string $locale): array {
    $locale = kc_normalize_locale($locale);

    if ($locale === kc_default_locale()) {
        return [];
    }

    $path = dirname(__DIR__) . '/translations/' . $locale . '/auto.php';

    if (!is_file($path)) {
        return [];
    }

    $translations = require $path;
    return is_array($translations) ? $translations : [];
}

function kc_page_translation_script(string $locale): string {
    if (!kc_should_use_page_translation()) {
        return '';
    }

    $locale = kc_normalize_locale($locale);
    $map = kc_auto_translation_map($locale);

    if ($map === []) {
        return '';
    }

    return '<script>(function(){var map='
        . json_encode($map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . ';function skip(n){var e=n.nodeType===1?n:n.parentElement;return !e||e.closest("script,style,noscript,.notranslate,[translate=no]")}function tr(v){var t=(v||"").trim();return t&&map[t]?v.replace(t,map[t]):v}function run(){var w=document.createTreeWalker(document.body,NodeFilter.SHOW_TEXT,{acceptNode:function(n){return skip(n)||!n.nodeValue.trim()?NodeFilter.FILTER_REJECT:NodeFilter.FILTER_ACCEPT}});var nodes=[];while(w.nextNode())nodes.push(w.currentNode);nodes.forEach(function(n){n.nodeValue=tr(n.nodeValue)});["alt","title","aria-label","placeholder","value"].forEach(function(a){document.querySelectorAll("["+a+"]").forEach(function(e){if(!skip(e))e.setAttribute(a,tr(e.getAttribute(a)||""))})})}if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",run)}else{run()}})();</script>';
}

function kc_language_switcher(string $class = ''): string {
    static $preserveScriptRendered = false;

    $current = kc_current_locale();
    $labels = kc_locale_labels();
    $flags = kc_locale_flags();
    $label = kc_t('common.language.label');
    $class = trim($class);
    $currentLabel = trim(($flags[$current] ?? '') . ' ' . ($labels[$current] ?? strtoupper($current)));
    $html = '<details class="notranslate relative ' . htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
    $html .= '<summary class="list-none cursor-pointer rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm font-semibold text-slate-100 hover:border-sky-500 focus:border-sky-500 focus:outline-none" aria-label="' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($currentLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</summary>';
    $html .= '<div class="absolute right-0 z-50 mt-2 max-h-80 min-w-48 overflow-y-auto rounded-lg border border-slate-700 bg-slate-950 p-2 shadow-xl">';

    foreach (kc_supported_locales() as $locale) {
        $optionLabel = trim(($flags[$locale] ?? '') . ' ' . ($labels[$locale] ?? strtoupper($locale)));
        $classes = 'block rounded-md px-3 py-2 text-sm font-semibold hover:bg-slate-800 ' . ($locale === $current ? 'bg-slate-800 text-sky-300' : 'text-slate-100');
        $html .= '<a class="' . htmlspecialchars($classes, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" href="' . htmlspecialchars(kc_localized_url($locale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" lang="' . htmlspecialchars($locale, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($optionLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    }

    $html .= '</div></details>';

    if (!$preserveScriptRendered) {
        $preserveScriptRendered = true;
        $html .= '<script>(function(){var lang=' . json_encode($current) . ';if(!lang)return;function keepLang(){document.querySelectorAll("a[href]").forEach(function(a){try{var raw=a.getAttribute("href")||"";if(!raw||raw.charAt(0)==="#"||raw.indexOf("mailto:")===0||raw.indexOf("tel:")===0)return;var u=new URL(raw,window.location.href);if(u.origin!==window.location.origin)return;if(u.searchParams.has("lang"))return;if(!/^\/($|[^?#]*\.php$|#)/.test(u.pathname)&&u.pathname!=="/")return;u.searchParams.set("lang",lang);a.href=u.pathname+u.search+u.hash;}catch(e){}})}if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",keepLang)}else{keepLang()}})();</script>';
        $html .= kc_page_translation_script($current);
    }

    return $html;
}
