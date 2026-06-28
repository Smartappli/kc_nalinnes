<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

function required_env_value(string $name): string {
    $value = env_value($name);

    if ($value === null) {
        throw new \RuntimeException('Configuration base de donnees incomplete: ' . $name . ' est manquant.');
    }

    return $value;
}

function database_connection_config(): array {
    $dsn = env_value('DB_DSN');

    if ($dsn === null) {
        $host = required_env_value('DB_HOST');
        $name = required_env_value('DB_NAME');
        $charset = env_value('DB_CHARSET', 'utf8mb4');
        $dsn = sprintf('mysql:dbname=%s;host=%s;charset=%s', $name, $host, $charset);
    }

    return [
        $dsn,
        required_env_value('DB_USER'),
        required_env_value('DB_PASS'),
    ];
}

function create_database_connection(): \PDO {
    [$dsn, $user, $password] = database_connection_config();

    return new \PDO(
        $dsn,
        $user,
        $password,
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]
    );
}
