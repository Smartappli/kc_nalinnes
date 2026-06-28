<?php
declare(strict_types=1);

function load_env_file(string $path): void {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $separatorPosition = strpos($line, '=');
        if ($separatorPosition === false) {
            continue;
        }

        $name = trim(substr($line, 0, $separatorPosition));
        $value = trim(substr($line, $separatorPosition + 1));

        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            continue;
        }

        if (env_value_is_defined($name)) {
            continue;
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        @putenv($name . '=' . $value);
    }
}

function ensure_env_loaded(): void {
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;
    load_env_file(dirname(__DIR__) . '/.env');
}

function env_value_is_defined(string $name): bool {
    $value = getenv($name);

    if ($value !== false && $value !== '') {
        return true;
    }

    return isset($_ENV[$name], $_SERVER[$name]) || isset($_ENV[$name]) || isset($_SERVER[$name]);
}

function env_value(string $name, ?string $default = null): ?string {
    ensure_env_loaded();

    $value = getenv($name);

    if ($value === false || $value === '') {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
    }

    if ($value === null || $value === '') {
        return $default;
    }

    return (string) $value;
}

function env_flag(string $name, bool $default = false): bool {
    $value = env_value($name);

    if ($value === null) {
        return $default;
    }

    return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
}

function configure_error_reporting_from_env(): void {
    $debug = env_flag('APP_DEBUG', false);

    ini_set('display_errors', $debug ? '1' : '0');
    ini_set('display_startup_errors', $debug ? '1' : '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}
