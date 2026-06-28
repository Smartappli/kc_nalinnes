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

function default_mutuelle_pdf_templates(): array {
    return array_keys(mutuelle_pdf_template_definitions());
}

function precompleted_mutuelle_filename(string $beneficiaryName): string {
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($beneficiaryName)) ?: 'membre';

    return 'mutuelle-precomplete-' . trim($slug, '-') . '.pdf';
}

function mutuelle_pdf_env_value(string $key, string $default): string {
    if (function_exists('env_value')) {
        return (string)env_value($key, $default);
    }

    return $default;
}

function mutuelle_pdf_context(string $beneficiaryName, string $responsibleName, array $extra = []): array {
    $clubAddress = mutuelle_pdf_env_value('MUTUELLE_CLUB_ADDRESS', 'Rue des Monts 18');
    $clubLocality = mutuelle_pdf_env_value('MUTUELLE_CLUB_LOCALITY', '6120 Nalinnes');

    return array_merge([
        'beneficiary_name' => $beneficiaryName,
        'beneficiary_birthdate' => '',
        'responsible_name' => $responsibleName,
        'responsible_email' => '',
        'club_name' => mutuelle_pdf_env_value('MUTUELLE_CLUB_NAME', 'KC Nalinnes'),
        'club_address' => trim($clubAddress . ', ' . $clubLocality, ' ,'),
        'club_email' => mutuelle_pdf_env_value('MUTUELLE_CLUB_EMAIL', 'info@kc-nalinnes.be'),
        'membership_year' => date('Y'),
        'generated_date' => date('d/m/Y'),
    ], $extra);
}

function mutuelle_pdf_template_definitions(): array {
    return [
        'mutualia-ac-sport-fr.pdf' => [
            'fields' => [
                ['key' => 'beneficiary_name', 'x' => 34, 'y' => 68, 'w' => 96],
                ['key' => 'beneficiary_birthdate', 'x' => 145, 'y' => 68, 'w' => 38],
                ['key' => 'responsible_name', 'x' => 42, 'y' => 82, 'w' => 90],
                ['key' => 'club_name', 'x' => 40, 'y' => 128, 'w' => 110],
                ['key' => 'club_address', 'x' => 40, 'y' => 136, 'w' => 130],
                ['key' => 'membership_year', 'x' => 155, 'y' => 128, 'w' => 25],
                ['key' => 'generated_date', 'x' => 143, 'y' => 253, 'w' => 35],
            ],
        ],
        'mc_formulaire_AC_SPORT_A4_FR_2024_V2.pdf' => [
            'fields' => [
                ['key' => 'beneficiary_name', 'x' => 45, 'y' => 72, 'w' => 100],
                ['key' => 'beneficiary_birthdate', 'x' => 153, 'y' => 72, 'w' => 35],
                ['key' => 'responsible_name', 'x' => 45, 'y' => 83, 'w' => 100],
                ['key' => 'club_name', 'x' => 40, 'y' => 142, 'w' => 115],
                ['key' => 'club_address', 'x' => 40, 'y' => 151, 'w' => 125],
                ['key' => 'membership_year', 'x' => 158, 'y' => 142, 'w' => 25],
                ['key' => 'generated_date', 'x' => 150, 'y' => 259, 'w' => 35],
            ],
        ],
        'Formulaire-de-demande-dintervention-Sports-2025.pdf' => [
            'fields' => [
                ['key' => 'beneficiary_name', 'x' => 48, 'y' => 78, 'w' => 105],
                ['key' => 'beneficiary_birthdate', 'x' => 151, 'y' => 88, 'w' => 35],
                ['key' => 'responsible_name', 'x' => 48, 'y' => 98, 'w' => 105],
                ['key' => 'club_name', 'x' => 45, 'y' => 158, 'w' => 115],
                ['key' => 'club_address', 'x' => 45, 'y' => 167, 'w' => 125],
                ['key' => 'membership_year', 'x' => 160, 'y' => 158, 'w' => 25],
                ['key' => 'generated_date', 'x' => 146, 'y' => 250, 'w' => 35],
            ],
        ],
        'avantage-inscription club sportif.pdf' => [
            'fields' => [
                ['key' => 'beneficiary_name', 'x' => 52, 'y' => 79, 'w' => 100],
                ['key' => 'beneficiary_birthdate', 'x' => 155, 'y' => 79, 'w' => 35],
                ['key' => 'responsible_name', 'x' => 52, 'y' => 91, 'w' => 100],
                ['key' => 'club_name', 'x' => 44, 'y' => 151, 'w' => 115],
                ['key' => 'club_address', 'x' => 44, 'y' => 160, 'w' => 125],
                ['key' => 'membership_year', 'x' => 160, 'y' => 151, 'w' => 25],
                ['key' => 'generated_date', 'x' => 148, 'y' => 252, 'w' => 35],
            ],
        ],
    ];
}

function mutuelle_pdf_template_definition(string $templateName): array {
    $definitions = mutuelle_pdf_template_definitions();

    return $definitions[basename($templateName)] ?? [
        'fields' => [
            ['label' => 'Beneficiaire', 'key' => 'beneficiary_name', 'x' => 20, 'y' => 35, 'w' => 120],
            ['label' => 'Membre responsable', 'key' => 'responsible_name', 'x' => 20, 'y' => 42, 'w' => 120],
            ['label' => 'Date', 'key' => 'generated_date', 'x' => 20, 'y' => 49, 'w' => 120],
        ],
    ];
}

function mutuelle_pdf_text_for_fpdf(string $value): string {
    $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $value);

    return is_string($converted) ? $converted : $value;
}

function mutuelle_pdf_field_text(array $field, array $context): string {
    $value = trim((string)($context[(string)($field['key'] ?? '')] ?? ''));
    if ($value === '') {
        return '';
    }

    $label = trim((string)($field['label'] ?? ''));
    return $label !== '' ? $label . ': ' . $value : $value;
}

function generate_precompleted_mutuelle_pdf(string $templatePath, string $beneficiaryName, string $responsibleName, array $context = []): string {
    $context = mutuelle_pdf_context($beneficiaryName, $responsibleName, $context);
    $definition = mutuelle_pdf_template_definition(basename($templatePath));
    $pdf = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdf->setSourceFile($templatePath);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $tpl = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tpl);

        foreach (($definition['fields'] ?? []) as $field) {
            if ((int)($field['page'] ?? 1) !== $pageNo) {
                continue;
            }

            $text = mutuelle_pdf_field_text($field, $context);
            if ($text === '') {
                continue;
            }

            $pdf->SetFont('Helvetica', '', (float)($field['size'] ?? 9));
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY((float)$field['x'], (float)$field['y']);
            $pdf->Cell((float)($field['w'] ?? 80), (float)($field['h'] ?? 5), mutuelle_pdf_text_for_fpdf($text), 0, 0);
        }
    }

    return $pdf->Output('S');
}
