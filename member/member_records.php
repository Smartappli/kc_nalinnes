<?php
declare(strict_types=1);

function ensure_member_records_tables(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS member_profiles (
        user_id INT PRIMARY KEY,
        first_name VARCHAR(100) NULL,
        last_name VARCHAR(100) NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');
    $db->exec('CREATE TABLE IF NOT EXISTS member_grade_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        grade VARCHAR(100) NOT NULL,
        obtained_at DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_member_grade_history_user (user_id)
    )');
    $db->exec('CREATE TABLE IF NOT EXISTS member_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        period_type VARCHAR(20) NOT NULL,
        period_year INT NOT NULL,
        period_month TINYINT NULL,
        status VARCHAR(20) NOT NULL DEFAULT \'unpaid\',
        paid_at DATE NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_member_payment_period (user_id, period_type, period_year, period_month),
        INDEX idx_member_payments_user_year (user_id, period_year)
    )');
}

function member_record_normalize_name(mixed $value, string $label): ?string {
    $name = trim((string)$value);
    if ($name === '') {
        return null;
    }

    if (mb_strlen($name) > 100) {
        throw new InvalidArgumentException($label . ' trop long.');
    }

    return $name;
}

function member_record_normalize_date(mixed $value, string $label, bool $required = true): ?string {
    $dateValue = trim((string)$value);
    if ($dateValue === '') {
        if ($required) {
            throw new InvalidArgumentException($label . ' obligatoire.');
        }

        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateValue);
    $errors = DateTimeImmutable::getLastErrors();
    $hasErrors = is_array($errors) && (((int)$errors['warning_count']) > 0 || ((int)$errors['error_count']) > 0);
    if (!$date || $hasErrors || $date->format('Y-m-d') !== $dateValue) {
        throw new InvalidArgumentException($label . ' invalide.');
    }

    return $dateValue;
}

function member_record_normalize_profile_input(array $input): array {
    return [
        'first_name' => member_record_normalize_name($input['first_name'] ?? $input['target_first_name'] ?? '', 'Prenom'),
        'last_name' => member_record_normalize_name($input['last_name'] ?? $input['target_last_name'] ?? '', 'Nom'),
    ];
}

function member_record_display_name(array $userRow, ?array $profile = null): string {
    $firstName = trim((string)($profile['first_name'] ?? ''));
    $lastName = trim((string)($profile['last_name'] ?? ''));
    $fullName = trim($firstName . ' ' . $lastName);
    if ($fullName !== '') {
        return $fullName;
    }

    $username = trim((string)($userRow['username'] ?? ''));
    if ($username !== '') {
        return $username;
    }

    return (string)($userRow['email'] ?? '');
}

function member_record_save_profile(PDO $db, int $userId, array $input): array {
    if ($userId <= 0) {
        throw new InvalidArgumentException('Membre invalide.');
    }

    $profile = member_record_normalize_profile_input($input);
    $stmt = $db->prepare('INSERT INTO member_profiles (user_id, first_name, last_name)
        VALUES (:user_id, :first_name, :last_name)
        ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name)');
    $stmt->execute([
        ':user_id' => $userId,
        ':first_name' => $profile['first_name'],
        ':last_name' => $profile['last_name'],
    ]);

    return $profile;
}

function member_record_profiles_by_user_id(PDO $db): array {
    ensure_member_records_tables($db);
    $stmt = $db->query('SELECT user_id, first_name, last_name FROM member_profiles');
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $profiles = [];
    foreach ($rows as $row) {
        $profiles[(int)$row['user_id']] = [
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
        ];
    }

    return $profiles;
}

function member_record_profile(PDO $db, int $userId): ?array {
    ensure_member_records_tables($db);
    $stmt = $db->prepare('SELECT first_name, last_name FROM member_profiles WHERE user_id = :user_id LIMIT 1');
    $stmt->execute([':user_id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return is_array($row) ? $row : null;
}

function member_record_normalize_grade_input(array $input): array {
    $grade = trim((string)($input['grade'] ?? $input['target_grade'] ?? ''));
    if ($grade === '' || mb_strlen($grade) > 100) {
        throw new InvalidArgumentException('Grade membre invalide.');
    }

    return [
        'grade' => $grade,
        'obtained_at' => member_record_normalize_date($input['obtained_at'] ?? $input['grade_obtained_at'] ?? '', 'Date obtention'),
    ];
}

function member_record_add_grade(PDO $db, int $userId, array $input): int {
    if ($userId <= 0) {
        throw new InvalidArgumentException('Membre invalide.');
    }

    $grade = member_record_normalize_grade_input($input);
    $stmt = $db->prepare('INSERT INTO member_grade_history (user_id, grade, obtained_at) VALUES (:user_id, :grade, :obtained_at)');
    $stmt->execute([
        ':user_id' => $userId,
        ':grade' => $grade['grade'],
        ':obtained_at' => $grade['obtained_at'],
    ]);

    return (int)$db->lastInsertId();
}

function member_record_delete_grade(PDO $db, int $userId, int $gradeId): void {
    if ($userId <= 0 || $gradeId <= 0) {
        throw new InvalidArgumentException('Grade membre invalide.');
    }

    $stmt = $db->prepare('DELETE FROM member_grade_history WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $gradeId, ':user_id' => $userId]);
    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('Grade membre introuvable.');
    }
}

function member_record_grade_history_by_user_id(PDO $db): array {
    ensure_member_records_tables($db);
    $stmt = $db->query('SELECT id, user_id, grade, obtained_at FROM member_grade_history ORDER BY user_id ASC, obtained_at DESC, id DESC');
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $history = [];
    foreach ($rows as $row) {
        $userId = (int)$row['user_id'];
        if (!isset($history[$userId])) {
            $history[$userId] = [];
        }

        $history[$userId][] = $row;
    }

    return $history;
}

function member_record_normalize_payment_status(mixed $value): string {
    $status = strtolower(trim((string)$value));
    if ($status === '') {
        $status = 'unpaid';
    }

    if (!in_array($status, ['unpaid', 'pending', 'paid'], true)) {
        throw new InvalidArgumentException('Etat de paiement invalide.');
    }

    return $status;
}

function member_record_normalize_payment_input(array $input): array {
    $periodType = strtolower(trim((string)($input['period_type'] ?? 'annual')));
    if (!in_array($periodType, ['annual', 'monthly'], true)) {
        throw new InvalidArgumentException('Type de paiement invalide.');
    }

    $year = (int)($input['period_year'] ?? date('Y'));
    if ($year < 2000 || $year > 2100) {
        throw new InvalidArgumentException('Annee de paiement invalide.');
    }

    $month = null;
    if ($periodType === 'monthly') {
        $month = (int)($input['period_month'] ?? 0);
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Mois de paiement invalide.');
        }
    }

    $status = member_record_normalize_payment_status($input['payment_status'] ?? $input['status'] ?? 'unpaid');
    $paidAt = member_record_normalize_date($input['paid_at'] ?? '', 'Date paiement', false);
    if ($status === 'paid' && $paidAt === null) {
        $paidAt = date('Y-m-d');
    }
    if ($status !== 'paid') {
        $paidAt = null;
    }

    return [
        'period_type' => $periodType,
        'period_year' => $year,
        'period_month' => $month,
        'status' => $status,
        'paid_at' => $paidAt,
    ];
}

function member_record_save_payment(PDO $db, int $userId, array $input): array {
    if ($userId <= 0) {
        throw new InvalidArgumentException('Membre invalide.');
    }

    $payment = member_record_normalize_payment_input($input);
    $stmt = $db->prepare('INSERT INTO member_payments (user_id, period_type, period_year, period_month, status, paid_at)
        VALUES (:user_id, :period_type, :period_year, :period_month, :status, :paid_at)
        ON DUPLICATE KEY UPDATE status = VALUES(status), paid_at = VALUES(paid_at)');
    $stmt->execute([
        ':user_id' => $userId,
        ':period_type' => $payment['period_type'],
        ':period_year' => $payment['period_year'],
        ':period_month' => $payment['period_month'],
        ':status' => $payment['status'],
        ':paid_at' => $payment['paid_at'],
    ]);

    return $payment;
}

function member_record_payments_by_user_id(PDO $db, int $year): array {
    ensure_member_records_tables($db);
    $stmt = $db->prepare('SELECT user_id, period_type, period_year, period_month, status, paid_at FROM member_payments WHERE period_year = :period_year ORDER BY user_id ASC, period_type ASC, period_month ASC');
    $stmt->execute([':period_year' => $year]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $payments = [];
    foreach ($rows as $row) {
        $userId = (int)$row['user_id'];
        if (!isset($payments[$userId])) {
            $payments[$userId] = [
                'annual' => null,
                'monthly' => [],
            ];
        }

        if ((string)$row['period_type'] === 'annual') {
            $payments[$userId]['annual'] = $row;
        }
        else {
            $payments[$userId]['monthly'][(int)$row['period_month']] = $row;
        }
    }

    return $payments;
}

function member_record_annual_payment_is_paid(PDO $db, int $userId, int $year): bool {
    ensure_member_records_tables($db);
    $stmt = $db->prepare('SELECT status FROM member_payments WHERE user_id = :user_id AND period_type = \'annual\' AND period_year = :period_year LIMIT 1');
    $stmt->execute([':user_id' => $userId, ':period_year' => $year]);

    return (string)($stmt->fetchColumn() ?: '') === 'paid';
}

function member_record_payment_status_label(?string $status): string {
    return match ($status) {
        'paid' => 'Paye',
        'pending' => 'A verifier',
        default => 'Non paye',
    };
}
