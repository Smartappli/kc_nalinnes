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

function kc_language_switcher(string $class = ''): string {
    $current = kc_current_locale();
    $labels = kc_locale_labels();
    $flags = kc_locale_flags();
    $label = kc_t('common.language.label');
    $class = trim($class);
    $selectId = 'kc-language-select-' . substr(hash('sha256', $class . ($_SERVER['REQUEST_URI'] ?? '')), 0, 8);
    $action = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    $html = '<form class="' . htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" action="' . htmlspecialchars($action, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" method="get">';
    foreach ($_GET as $name => $value) {
        if ($name === 'lang' || is_array($value)) {
            continue;
        }

        $html .= '<input type="hidden" name="' . htmlspecialchars((string)$name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
    }
    $html .= '<label class="sr-only" for="' . htmlspecialchars($selectId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</label>';
    $html .= '<select id="' . htmlspecialchars($selectId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" name="lang" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm font-semibold text-slate-100 hover:border-sky-500 focus:border-sky-500 focus:outline-none" aria-label="' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" onchange="this.form.submit();">';

    foreach (kc_supported_locales() as $locale) {
        $optionLabel = trim(($flags[$locale] ?? '') . ' ' . ($labels[$locale] ?? strtoupper($locale)));
        $html .= '<option value="' . htmlspecialchars($locale, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" lang="' . htmlspecialchars($locale, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"' . ($locale === $current ? ' selected' : '') . '>' . htmlspecialchars($optionLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</option>';
    }

    return $html . '</select><noscript><button class="ml-2 rounded-md border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-100" type="submit">' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</button></noscript></form>';
}
