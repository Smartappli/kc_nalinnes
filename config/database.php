<?php
declare(strict_types=1);

function env_value(string $name, ?string $default = null): ?string {
    $value = getenv($name);

    if ($value === false || $value === '') {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
    }

    if ($value === null || $value === '') {
        return $default;
    }

    return (string) $value;
}

function create_database_connection(): \PDO {
    $dsn = env_value('DB_DSN');

    if ($dsn === null) {
        $host = env_value('DB_HOST', '127.0.0.1');
        $name = env_value('DB_NAME', 'my-database');
        $charset = env_value('DB_CHARSET', 'utf8mb4');
        $dsn = sprintf('mysql:dbname=%s;host=%s;charset=%s', $name, $host, $charset);
    }

    return new \PDO(
        $dsn,
        env_value('DB_USER', 'my-username'),
        env_value('DB_PASS', 'my-password'),
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]
    );
}
