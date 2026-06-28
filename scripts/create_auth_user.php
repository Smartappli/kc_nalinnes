<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../manager/admin_access.php';

function usage(): string {
    return <<<TEXT
Usage:
  php scripts/create_auth_user.php --email=user@example.com --password=secret [--username="Name"] [--admin] [--install-schema]

Options:
  --email           Login email address.
  --password        Password to set. If omitted, AUTH_BOOTSTRAP_PASSWORD is used.
  --username        Optional display name.
  --admin           Add this email to the local admin_users table.
  --install-schema  Create Delight Auth tables if they do not exist.

TEXT;
}

function fail(string $message, int $code = 1): void {
    fwrite(STDERR, $message . PHP_EOL);
    exit($code);
}

function delight_auth_schema_statements(string $sql): array {
    $statements = [];
    $current = '';

    foreach (preg_split('/\R/', $sql) ?: [] as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*!')) {
            continue;
        }

        $current .= $line . PHP_EOL;

        if (str_ends_with($trimmed, ';')) {
            $statement = trim(rtrim($current, ";\r\n\t "));
            $statement = preg_replace('/^CREATE TABLE `/i', 'CREATE TABLE IF NOT EXISTS `', $statement) ?? $statement;

            if ($statement !== '') {
                $statements[] = $statement;
            }

            $current = '';
        }
    }

    return $statements;
}

function install_delight_auth_schema(PDO $db): void {
    $schemaPath = __DIR__ . '/../vendor/delight-im/auth/Database/MySQL.sql';
    $sql = is_file($schemaPath) ? file_get_contents($schemaPath) : false;

    if ($sql === false) {
        throw new RuntimeException('Delight Auth schema file not found: ' . $schemaPath);
    }

    foreach (delight_auth_schema_statements($sql) as $statement) {
        $db->exec($statement);
    }
}

function user_id_by_email(PDO $db, string $email): ?int {
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $id = $stmt->fetchColumn();

    return $id !== false ? (int)$id : null;
}

$options = getopt('', ['email:', 'password::', 'username::', 'admin', 'install-schema', 'help']) ?: [];

if (isset($options['help'])) {
    echo usage();
    exit(0);
}

$email = trim((string)($options['email'] ?? ''));
$password = (string)($options['password'] ?? env_value('AUTH_BOOTSTRAP_PASSWORD', ''));
$username = isset($options['username']) ? trim((string)$options['username']) : null;
$isAdmin = array_key_exists('admin', $options);
$installSchema = array_key_exists('install-schema', $options);

if ($email === '' || $password === '') {
    fail(usage(), 2);
}

try {
    $db = create_database_connection();

    if ($installSchema) {
        install_delight_auth_schema($db);
        echo "Delight Auth schema checked.\n";
    }

    $auth = new \Delight\Auth\Auth($db);
    $administration = $auth->admin();

    try {
        $userId = $administration->createUser($email, $password, $username);
        echo 'User created: #' . $userId . ' ' . $email . "\n";
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        $userId = user_id_by_email($db, $email);
        if ($userId === null) {
            throw $e;
        }

        $administration->changePasswordForUserById($userId, $password);
        echo 'User already existed, password updated: #' . $userId . ' ' . $email . "\n";
    }

    if ($isAdmin) {
        set_admin_role($db, $email, true);
        echo 'Admin access enabled for: ' . $email . "\n";
    }
}
catch (Throwable $e) {
    fail(get_class($e) . ': ' . $e->getMessage());
}
