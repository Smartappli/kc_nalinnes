<?php
declare(strict_types=1);

function parse_admin_emails(string $raw): array {
    $emails = array_values(array_filter(array_map(
        static fn(string $value): string => strtolower(trim($value)),
        explode(',', $raw)
    )));

    if ($emails === []) {
        throw new \RuntimeException('Configuration ADMIN_EMAILS manquante ou vide.');
    }

    return $emails;
}

function is_admin_email(string $email, array $adminEmails): bool {
    return in_array(strtolower($email), $adminEmails, true);
}

function resolve_dashboard_path(string $email, string $adminEmailsRaw): string {
    $adminEmails = parse_admin_emails($adminEmailsRaw);
    return is_admin_email($email, $adminEmails) ? '/manager/dashboard.php' : '/member/dashboard.php';
}
