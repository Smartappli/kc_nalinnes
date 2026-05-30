<?php
declare(strict_types=1);

function compute_meal_total(int $adultQty, int $childQty, int $adultPrice = 19, int $childPrice = 10): int {
    $adultQty = max(0, $adultQty);
    $childQty = max(0, $childQty);
    return ($adultQty * $adultPrice) + ($childQty * $childPrice);
}

function ensure_meal_reservations_table(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS meal_reservations (id INT AUTO_INCREMENT PRIMARY KEY, member_user_id INT NOT NULL, profile_type VARCHAR(20) NOT NULL, dependent_id INT NULL, profile_name VARCHAR(255) NOT NULL, adult_qty INT NOT NULL DEFAULT 0, child_qty INT NOT NULL DEFAULT 0, total_amount DECIMAL(10,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
}

function ensure_meal_public_contact_columns(PDO $db): void {
    $columns = [
        'contact_email' => 'VARCHAR(255) NULL',
        'contact_phone' => 'VARCHAR(50) NULL',
        'notes' => 'TEXT NULL',
    ];

    foreach ($columns as $name => $definition) {
        try {
            $db->exec(sprintf('ALTER TABLE meal_reservations ADD COLUMN %s %s', $name, $definition));
        }
        catch (Throwable $e) {
            // Column already exists, or the database user cannot alter the table.
        }
    }
}

function meal_reservations_excel_path(): string {
    $configuredPath = getenv('MEAL_RESERVATIONS_XLSX_PATH');
    if (is_string($configuredPath) && trim($configuredPath) !== '') {
        return $configuredPath;
    }

    return __DIR__ . '/../storage/reservations-repas.xlsx';
}

function meal_reservations_excel_headers(): array {
    return [
        'date',
        'member_user_id',
        'profile_name',
        'profile_type',
        'contact_email',
        'contact_phone',
        'adult_qty',
        'child_qty',
        'total_amount',
        'notes',
    ];
}

function meal_reservation_excel_col_name(int $index): string {
    $name = '';
    $index++;
    while ($index > 0) {
        $mod = ($index - 1) % 26;
        $name = chr(65 + $mod) . $name;
        $index = intdiv($index - 1, 26);
    }

    return $name;
}

function meal_reservation_excel_row_xml(int $rowNum, array $values): string {
    $cells = '';
    foreach (array_values($values) as $i => $value) {
        $ref = meal_reservation_excel_col_name($i) . $rowNum;
        $cells .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></is></c>';
    }

    return '<row r="' . $rowNum . '">' . $cells . '</row>';
}

function read_meal_reservations_excel_rows(string $path): array {
    if (!is_file($path) || !class_exists('ZipArchive')) {
        return [];
    }

    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return [];
    }

    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();

    if (!is_string($sheetXml) || $sheetXml === '') {
        return [];
    }

    $dom = new DOMDocument();
    if (@$dom->loadXML($sheetXml) === false) {
        return [];
    }

    $headers = meal_reservations_excel_headers();
    $rows = [];
    foreach ($dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'row') as $rowNode) {
        $values = [];
        foreach ($rowNode->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'c') as $cellNode) {
            $textNodes = $cellNode->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 't');
            $values[] = $textNodes->length > 0 ? (string)$textNodes->item(0)->textContent : '';
        }

        if ($values === $headers) {
            continue;
        }

        if ($values !== []) {
            $rows[] = array_pad(array_slice($values, 0, count($headers)), count($headers), '');
        }
    }

    return $rows;
}

function write_meal_reservations_excel(string $path, array $rows): void {
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive is required to write XLSX files.');
    }

    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create meal reservations storage directory.');
    }

    $headers = meal_reservations_excel_headers();
    $sheetRows = meal_reservation_excel_row_xml(1, $headers);
    foreach (array_values($rows) as $i => $row) {
        $sheetRows .= meal_reservation_excel_row_xml($i + 2, array_pad(array_slice(array_values($row), 0, count($headers)), count($headers), ''));
    }

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
        . $sheetRows
        . '</sheetData></worksheet>';

    $tmp = tempnam($dir, 'reservations_xlsx_');
    if ($tmp === false) {
        throw new RuntimeException('Unable to create temporary XLSX file.');
    }

    $zip = new ZipArchive();
    if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
        @unlink($tmp);
        throw new RuntimeException('Unable to open temporary XLSX archive.');
    }

    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
    $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
    $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Reservations" sheetId="1" r:id="rId1"/></sheets></workbook>');
    $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    if (!rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException('Unable to move XLSX file into place.');
    }
}

function append_meal_reservation_to_excel(array $reservation, ?string $path = null): void {
    $path = $path ?? meal_reservations_excel_path();
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create meal reservations storage directory.');
    }

    $lockPath = $path . '.lock';
    $lock = fopen($lockPath, 'c');
    if ($lock === false) {
        throw new RuntimeException('Unable to open meal reservations lock file.');
    }

    try {
        if (!flock($lock, LOCK_EX)) {
            throw new RuntimeException('Unable to lock meal reservations XLSX file.');
        }

        $headers = meal_reservations_excel_headers();
        $row = [];
        foreach ($headers as $header) {
            $row[] = (string)($reservation[$header] ?? '');
        }

        $rows = read_meal_reservations_excel_rows($path);
        $rows[] = $row;
        write_meal_reservations_excel($path, $rows);
        flock($lock, LOCK_UN);
    }
    finally {
        fclose($lock);
    }
}
