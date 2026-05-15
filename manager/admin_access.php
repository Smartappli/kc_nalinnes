<?php
declare(strict_types=1);

function normalize_email(string $email): string {
    return strtolower(trim($email));
}

function parse_admin_emails(string $raw): array {
    return array_values(array_filter(array_map('normalize_email', explode(',', $raw))));
}

function ensure_admin_users_table(\PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS admin_users (
        email VARCHAR(255) PRIMARY KEY,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
}

function get_db_admin_emails(\PDO $db): array {
    ensure_admin_users_table($db);
    $stmt = $db->query('SELECT email FROM admin_users');
    $emails = $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN) : [];
    return array_values(array_filter(array_map('normalize_email', $emails ?: [])));
}

function get_effective_admin_emails(\PDO $db, string $raw): array {
    $fromEnv = parse_admin_emails($raw);
    $fromDb = get_db_admin_emails($db);
    $all = array_values(array_unique(array_merge($fromEnv, $fromDb)));

    if ($all === []) {
        throw new \RuntimeException('Aucun admin configuré (ADMIN_EMAILS et admin_users vides).');
    }

    return $all;
}

function is_admin_email(string $email, array $adminEmails): bool {
    return in_array(normalize_email($email), $adminEmails, true);
}

function resolve_dashboard_path(string $email, \PDO $db, string $adminEmailsRaw): string {
    $adminEmails = get_effective_admin_emails($db, $adminEmailsRaw);
    return is_admin_email($email, $adminEmails) ? '/manager/dashboard.php' : '/member/dashboard.php';
}


function is_temp_bypass_login_enabled(): bool {
    $raw = getenv('TEMP_BYPASS_LOGIN');

    if ($raw === false || $raw === '') {
        $raw = $_ENV['TEMP_BYPASS_LOGIN'] ?? $_SERVER['TEMP_BYPASS_LOGIN'] ?? '';
    }

    $value = strtolower(trim((string)$raw));
    return in_array($value, ['1', 'true', 'yes', 'on'], true);
}

function set_admin_role(\PDO $db, string $email, bool $isAdmin): void {
    ensure_admin_users_table($db);
    $email = normalize_email($email);

    if ($isAdmin) {
        $stmt = $db->prepare('INSERT IGNORE INTO admin_users (email) VALUES (:email)');
        $stmt->execute([':email' => $email]);
        return;
    }

    $stmt = $db->prepare('DELETE FROM admin_users WHERE email = :email');
    $stmt->execute([':email' => $email]);
}
