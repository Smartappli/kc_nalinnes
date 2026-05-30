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
    $configuredPath = getenv('MEAL_RESERVATIONS_EXCEL_PATH') ?: getenv('MEAL_RESERVATIONS_XLSX_PATH');
    if (is_string($configuredPath) && trim($configuredPath) !== '') {
        return $configuredPath;
    }

    return __DIR__ . '/../storage/reservations-repas.xls';
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

function read_meal_reservations_excel_rows(string $path): array {
    if (!is_file($path)) {
        return [];
    }

    $dom = new DOMDocument();
    $html = file_get_contents($path);
    if (!is_string($html) || @$dom->loadHTML('<?xml encoding="UTF-8">' . $html) === false) {
        return [];
    }

    $headers = meal_reservations_excel_headers();
    $rows = [];
    foreach ($dom->getElementsByTagName('tr') as $rowNode) {
        $values = [];
        foreach ($rowNode->childNodes as $cellNode) {
            if ($cellNode instanceof DOMElement && in_array(strtolower($cellNode->tagName), ['th', 'td'], true)) {
                $values[] = trim((string)$cellNode->textContent);
            }
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
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create meal reservations storage directory.');
    }

    $headers = meal_reservations_excel_headers();
    $tableRows = '<tr>';
    foreach ($headers as $header) {
        $tableRows .= '<th>' . htmlspecialchars($header, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</th>';
    }
    $tableRows .= '</tr>';

    foreach (array_values($rows) as $i => $row) {
        $tableRows .= '<tr>';
        foreach (array_pad(array_slice(array_values($row), 0, count($headers)), count($headers), '') as $value) {
            $tableRows .= '<td style="mso-number-format:\'@\';">' . htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        }
        $tableRows .= '</tr>';
    }

    $excelHtml = '<!doctype html><html><head><meta charset="UTF-8">'
        . '<style>table{border-collapse:collapse}th,td{border:1px solid #999;padding:4px}</style>'
        . '</head><body><table>' . $tableRows . '</table></body></html>';

    $tmp = tempnam($dir, 'reservations_excel_');
    if ($tmp === false) {
        throw new RuntimeException('Unable to create temporary Excel file.');
    }

    file_put_contents($tmp, $excelHtml, LOCK_EX);

    if (!rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException('Unable to move Excel file into place.');
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
            throw new RuntimeException('Unable to lock meal reservations Excel file.');
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
