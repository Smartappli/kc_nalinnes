<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    require_once __DIR__ . '/../includes/i18n.php';

    header('Location: ' . kc_redirect_url_with_locale('/reservation-repas.php'), true, 303);
    exit;
}

function compute_meal_total(int $adultQty, int $childQty, int $adultPrice = 19, int $childPrice = 10): int {
    $adultQty = max(0, $adultQty);
    $childQty = max(0, $childQty);
    return ($adultQty * $adultPrice) + ($childQty * $childPrice);
}

function meal_reservation_submission_token(string $scope): string {
    if (!isset($_SESSION['meal_reservation_submission_tokens']) || !is_array($_SESSION['meal_reservation_submission_tokens'])) {
        $_SESSION['meal_reservation_submission_tokens'] = [];
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['meal_reservation_submission_tokens'][$scope] = $token;

    return $token;
}

function consume_meal_reservation_submission_token(string $scope, string $token): bool {
    $tokens = $_SESSION['meal_reservation_submission_tokens'] ?? [];
    if (!is_array($tokens) || !isset($tokens[$scope]) || !is_string($tokens[$scope])) {
        return false;
    }

    $expectedToken = $tokens[$scope];
    if (!hash_equals($expectedToken, $token)) {
        return false;
    }

    unset($_SESSION['meal_reservation_submission_tokens'][$scope]);

    return true;
}

function ensure_meal_reservations_table(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS meal_reservations (id INT AUTO_INCREMENT PRIMARY KEY, member_user_id INT NOT NULL, profile_type VARCHAR(20) NOT NULL, dependent_id INT NULL, profile_name VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL DEFAULT \'confirmed\', adult_qty INT NOT NULL DEFAULT 0, child_qty INT NOT NULL DEFAULT 0, total_amount DECIMAL(10,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');

    ensure_meal_reservations_columns($db);
}

function meal_reservations_schema_columns(): array {
    return [
        'member_user_id' => 'INT NOT NULL DEFAULT 0',
        'profile_type' => 'VARCHAR(20) NOT NULL DEFAULT \'public\'',
        'dependent_id' => 'INT NULL',
        'profile_name' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
        'status' => 'VARCHAR(20) NOT NULL DEFAULT \'confirmed\'',
        'contact_email' => 'VARCHAR(255) NULL',
        'contact_phone' => 'VARCHAR(50) NULL',
        'notes' => 'TEXT NULL',
        'adult_qty' => 'INT NOT NULL DEFAULT 0',
        'child_qty' => 'INT NOT NULL DEFAULT 0',
        'total_amount' => 'DECIMAL(10,2) NOT NULL DEFAULT 0',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    ];
}

function meal_reservation_statuses(): array {
    return [
        'confirmed' => 'Confirmee',
        'pending' => 'A verifier',
        'paid' => 'Payee',
        'cancelled' => 'Annulee',
    ];
}

function normalize_meal_reservation_status(string $status): string {
    $status = trim($status);
    if ($status === '') {
        $status = 'confirmed';
    }

    if (!array_key_exists($status, meal_reservation_statuses())) {
        throw new InvalidArgumentException('Statut de reservation repas invalide.');
    }

    return $status;
}

function meal_reservation_status_label(string $status): string {
    $statuses = meal_reservation_statuses();
    return $statuses[$status] ?? $statuses['confirmed'];
}

function ensure_meal_reservations_columns(PDO $db): void {
    foreach (meal_reservations_schema_columns() as $name => $definition) {
        add_meal_reservation_column_if_missing($db, $name, $definition);
    }
}

function add_meal_reservation_column_if_missing(PDO $db, string $name, string $definition): void {
    try {
        $db->exec(sprintf('ALTER TABLE meal_reservations ADD COLUMN %s %s', $name, $definition));
    }
    catch (Throwable $e) {
        // Column already exists, or the database user cannot alter the table.
    }
}

function save_public_meal_reservation(PDO $db, array $reservation): int {
    ensure_meal_reservations_table($db);

    $stmt = $db->prepare('INSERT INTO meal_reservations (member_user_id, profile_type, dependent_id, profile_name, status, contact_email, contact_phone, notes, adult_qty, child_qty, total_amount) VALUES (:uid, :ptype, :did, :pname, :status, :email, :phone, :notes, :adult, :child, :total)');
    $stmt->execute([
        ':uid' => 0,
        ':ptype' => (string)($reservation['profile_type'] ?? 'public'),
        ':did' => null,
        ':pname' => (string)$reservation['profile_name'],
        ':status' => normalize_meal_reservation_status((string)($reservation['status'] ?? 'confirmed')),
        ':email' => (string)$reservation['contact_email'],
        ':phone' => (string)($reservation['contact_phone'] ?? ''),
        ':notes' => (string)($reservation['notes'] ?? ''),
        ':adult' => (int)$reservation['adult_qty'],
        ':child' => (int)$reservation['child_qty'],
        ':total' => (int)$reservation['total_amount'],
    ]);

    return (int)$db->lastInsertId();
}

function update_meal_reservation_status(PDO $db, int $reservationId, string $status): void {
    if ($reservationId <= 0) {
        throw new InvalidArgumentException('Reservation repas invalide.');
    }

    ensure_meal_reservations_table($db);

    $normalizedStatus = normalize_meal_reservation_status($status);
    $stmt = $db->prepare('UPDATE meal_reservations SET status = :status WHERE id = :id');
    $stmt->execute([
        ':status' => $normalizedStatus,
        ':id' => $reservationId,
    ]);

    if ($stmt->rowCount() > 0) {
        return;
    }

    $exists = $db->prepare('SELECT COUNT(*) FROM meal_reservations WHERE id = :id');
    $exists->execute([':id' => $reservationId]);
    if ((int)$exists->fetchColumn() === 0) {
        throw new RuntimeException('Reservation repas introuvable.');
    }
}

function delete_meal_reservation(PDO $db, int $reservationId): void {
    if ($reservationId <= 0) {
        throw new InvalidArgumentException('Reservation repas invalide.');
    }

    ensure_meal_reservations_table($db);

    $stmt = $db->prepare('DELETE FROM meal_reservations WHERE id = :id');
    $stmt->execute([':id' => $reservationId]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('Reservation repas introuvable.');
    }
}

function send_meal_reservation_mail(string $to, string $subject, string $message, string $headers): bool {
    $sent = @mail($to, $subject, $message, $headers);
    if (!$sent) {
        error_log('Meal reservation mail failed: ' . $subject . ' -> ' . $to);
    }

    return $sent;
}

function meal_reservations_excel_path(): string {
    $configuredPath = env_value('MEAL_RESERVATIONS_EXCEL_PATH') ?? env_value('MEAL_RESERVATIONS_XLSX_PATH');
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
        'status',
        'contact_email',
        'contact_phone',
        'adult_qty',
        'child_qty',
        'total_amount',
        'notes',
    ];
}

function meal_reservations_legacy_excel_headers(): array {
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

function normalize_meal_reservation_excel_row_values(array $values): ?array {
    $headers = meal_reservations_excel_headers();
    $legacyHeaders = meal_reservations_legacy_excel_headers();

    if ($values === $headers || $values === $legacyHeaders) {
        return null;
    }

    if (count($values) === count($legacyHeaders)) {
        $values = array_merge(
            array_slice($values, 0, 4),
            ['confirmed'],
            array_slice($values, 4)
        );
    }

    return array_pad(array_slice($values, 0, count($headers)), count($headers), '');
}

function meal_reservations_should_use_xlsx(string $path): bool {
    return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'xlsx' && class_exists('ZipArchive');
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

function meal_reservation_xlsx_row_xml(int $rowNum, array $values): string {
    $cells = '';
    foreach (array_values($values) as $i => $value) {
        $ref = meal_reservation_excel_col_name($i) . $rowNum;
        $cells .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></is></c>';
    }

    return '<row r="' . $rowNum . '">' . $cells . '</row>';
}

function read_meal_reservations_xlsx_rows(string $path): array {
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

    $rows = [];
    foreach ($dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'row') as $rowNode) {
        $values = [];
        foreach ($rowNode->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'c') as $cellNode) {
            $textNodes = $cellNode->getElementsByTagNameNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 't');
            $values[] = $textNodes->length > 0 ? (string)$textNodes->item(0)->textContent : '';
        }

        if ($values !== []) {
            $normalizedValues = normalize_meal_reservation_excel_row_values($values);
            if ($normalizedValues !== null) {
                $rows[] = $normalizedValues;
            }
        }
    }

    return $rows;
}

function read_meal_reservations_html_excel_rows(string $path): array {
    if (!is_file($path)) {
        return [];
    }

    $dom = new DOMDocument();
    $html = file_get_contents($path);
    if (!is_string($html) || @$dom->loadHTML('<?xml encoding="UTF-8">' . $html) === false) {
        return [];
    }

    $rows = [];
    foreach ($dom->getElementsByTagName('tr') as $rowNode) {
        $values = [];
        foreach ($rowNode->childNodes as $cellNode) {
            if ($cellNode instanceof DOMElement && in_array(strtolower($cellNode->tagName), ['th', 'td'], true)) {
                $values[] = trim((string)$cellNode->textContent);
            }
        }

        if ($values !== []) {
            $normalizedValues = normalize_meal_reservation_excel_row_values($values);
            if ($normalizedValues !== null) {
                $rows[] = $normalizedValues;
            }
        }
    }

    return $rows;
}

function read_meal_reservations_excel_rows(string $path): array {
    return meal_reservations_should_use_xlsx($path)
        ? read_meal_reservations_xlsx_rows($path)
        : read_meal_reservations_html_excel_rows($path);
}

function write_meal_reservations_xlsx(string $path, array $rows): void {
    $headers = meal_reservations_excel_headers();
    $sheetRows = meal_reservation_xlsx_row_xml(1, $headers);
    foreach (array_values($rows) as $i => $row) {
        $sheetRows .= meal_reservation_xlsx_row_xml($i + 2, array_pad(array_slice(array_values($row), 0, count($headers)), count($headers), ''));
    }

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
        . $sheetRows
        . '</sheetData></worksheet>';

    $tmp = tempnam(dirname($path), 'reservations_xlsx_');
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

function write_meal_reservations_html_excel(string $path, array $rows): void {
    $dir = dirname($path);
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

function write_meal_reservations_excel(string $path, array $rows): void {
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create meal reservations storage directory.');
    }

    if (meal_reservations_should_use_xlsx($path)) {
        write_meal_reservations_xlsx($path, $rows);
        return;
    }

    write_meal_reservations_html_excel($path, $rows);
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
