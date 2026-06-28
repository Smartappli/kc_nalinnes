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

function precompleted_mutuelle_filename(string $beneficiaryName): string {
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($beneficiaryName)) ?: 'membre';

    return 'mutuelle-precomplete-' . trim($slug, '-') . '.pdf';
}

function generate_precompleted_mutuelle_pdf(string $templatePath, string $beneficiaryName, string $responsibleName): string {
    $pdf = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdf->setSourceFile($templatePath);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $tpl = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($tpl);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tpl);

        if ($pageNo === 1) {
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(20, 35);
            $pdf->Cell(120, 6, utf8_decode('Beneficiaire: ' . $beneficiaryName));
            $pdf->SetXY(20, 42);
            $pdf->Cell(120, 6, utf8_decode('Membre responsable: ' . $responsibleName));
            $pdf->SetXY(20, 49);
            $pdf->Cell(120, 6, utf8_decode('Date: ' . date('d/m/Y')));
        }
    }

    return $pdf->Output('S');
}
