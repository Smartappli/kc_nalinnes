<?php
declare(strict_types=1);

function build_download_token(string $userId, string $csrfToken): string {
    return hash_hmac('sha256', $userId, $csrfToken);
}

function is_valid_download_token(string $providedToken, string $userId, string $csrfToken): bool {
    return hash_equals(build_download_token($userId, $csrfToken), $providedToken);
}

function list_pdf_templates(string $dir): array {
    if (!is_dir($dir)) {
        return [];
    }

    return array_values(array_filter(scandir($dir) ?: [], static fn(string $f): bool => str_ends_with(strtolower($f), '.pdf')));
}

function is_allowed_template(string $requestedTemplate, array $templateFiles): bool {
    return in_array(basename($requestedTemplate), $templateFiles, true);
}
