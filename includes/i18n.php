<?php
declare(strict_types=1);

function kc_supported_locales(): array {
    return ['fr', 'en', 'nl'];
}

function kc_translation_modules(): array {
    return ['common', 'reservation-repas', 'member-dashboard'];
}

function kc_normalize_locale(?string $locale): string {
    $locale = strtolower(trim((string)$locale));
    $locale = str_replace('_', '-', $locale);
    $locale = explode('-', $locale, 2)[0];

    return in_array($locale, kc_supported_locales(), true) ? $locale : 'fr';
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
    $value = $translations[$locale][$key] ?? $translations['fr'][$key] ?? $key;

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
    $html = '<div class="' . htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" aria-label="' . htmlspecialchars(kc_t('common.language.label'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';

    foreach (kc_supported_locales() as $locale) {
        $active = $locale === $current;
        $classes = $active
            ? 'rounded-md bg-sky-500 px-2 py-1 text-xs font-semibold text-white'
            : 'rounded-md border border-slate-700 px-2 py-1 text-xs font-semibold text-slate-300 hover:border-sky-500 hover:text-sky-300';
        $html .= '<a class="' . $classes . '" href="' . htmlspecialchars(kc_localized_url($locale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" hreflang="' . $locale . '" lang="' . $locale . '">' . htmlspecialchars(kc_t('common.language.' . $locale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    }

    return $html . '</div>';
}
