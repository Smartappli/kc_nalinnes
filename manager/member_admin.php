<?php
declare(strict_types=1);

require_once __DIR__ . '/admin_access.php';

function manager_admin_normalize_member_profile_input(array $input): array {
    $email = normalize_email((string)($input['target_email'] ?? $input['email'] ?? ''));
    if ($email === '' || strlen($email) > 249 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Email membre invalide.');
    }

    $username = trim((string)($input['target_username'] ?? $input['username'] ?? ''));
    if (mb_strlen($username) > 100) {
        throw new InvalidArgumentException('Nom membre trop long.');
    }

    return [
        'email' => $email,
        'username' => $username === '' ? null : $username,
    ];
}

function manager_admin_normalize_dependent_input(array $input): array {
    $fullName = trim((string)($input['dependent_name'] ?? $input['full_name'] ?? ''));
    if ($fullName === '' || mb_strlen($fullName) > 255) {
        throw new InvalidArgumentException('Nom du profil lie invalide.');
    }

    $birthdate = trim((string)($input['dependent_birthdate'] ?? $input['birthdate'] ?? ''));
    if ($birthdate !== '') {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $birthdate);
        $errors = DateTimeImmutable::getLastErrors();
        $hasErrors = is_array($errors) && (((int)$errors['warning_count']) > 0 || ((int)$errors['error_count']) > 0);
        if (!$date || $hasErrors || $date->format('Y-m-d') !== $birthdate) {
            throw new InvalidArgumentException('Date de naissance du profil lie invalide.');
        }
    }

    $isMinorRaw = strtolower(trim((string)($input['dependent_is_minor'] ?? $input['is_minor'] ?? '1')));
    $isMinor = in_array($isMinorRaw, ['0', 'false', 'adult', 'adulte'], true) ? 0 : 1;

    return [
        'full_name' => $fullName,
        'birthdate' => $birthdate === '' ? null : $birthdate,
        'is_minor' => $isMinor,
    ];
}

function manager_admin_fetch_user(PDO $db, int $userId): ?array {
    if ($userId <= 0) {
        return null;
    }

    $stmt = $db->prepare('SELECT id, email, username FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return is_array($row) ? $row : null;
}

function manager_admin_update_member_profile(PDO $db, int $userId, array $input): array {
    $existing = manager_admin_fetch_user($db, $userId);
    if ($existing === null) {
        throw new RuntimeException('Membre introuvable.');
    }

    $profile = manager_admin_normalize_member_profile_input($input);

    $duplicateStmt = $db->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
    $duplicateStmt->execute([':email' => $profile['email'], ':id' => $userId]);
    if ($duplicateStmt->fetchColumn() !== false) {
        throw new RuntimeException('Cet email est deja utilise par un autre membre.');
    }

    $stmt = $db->prepare('UPDATE users SET email = :email, username = :username WHERE id = :id');
    $stmt->execute([
        ':email' => $profile['email'],
        ':username' => $profile['username'],
        ':id' => $userId,
    ]);

    return [
        'id' => $userId,
        'old_email' => normalize_email((string)($existing['email'] ?? '')),
        'email' => $profile['email'],
        'username' => $profile['username'],
    ];
}

function manager_admin_assert_user_exists(PDO $db, int $userId): void {
    if (manager_admin_fetch_user($db, $userId) === null) {
        throw new RuntimeException('Membre introuvable.');
    }
}

function manager_admin_add_dependent(PDO $db, int $guardianUserId, array $input): int {
    manager_admin_assert_user_exists($db, $guardianUserId);
    $dependent = manager_admin_normalize_dependent_input($input);

    $stmt = $db->prepare('INSERT INTO member_dependents (guardian_user_id, full_name, birthdate, is_minor) VALUES (:guardian_user_id, :full_name, :birthdate, :is_minor)');
    $stmt->execute([
        ':guardian_user_id' => $guardianUserId,
        ':full_name' => $dependent['full_name'],
        ':birthdate' => $dependent['birthdate'],
        ':is_minor' => $dependent['is_minor'],
    ]);

    return (int)$db->lastInsertId();
}

function manager_admin_update_dependent(PDO $db, int $guardianUserId, int $dependentId, array $input): void {
    if ($guardianUserId <= 0 || $dependentId <= 0) {
        throw new InvalidArgumentException('Profil lie invalide.');
    }

    $dependent = manager_admin_normalize_dependent_input($input);
    $stmt = $db->prepare('UPDATE member_dependents SET full_name = :full_name, birthdate = :birthdate, is_minor = :is_minor WHERE id = :id AND guardian_user_id = :guardian_user_id');
    $stmt->execute([
        ':full_name' => $dependent['full_name'],
        ':birthdate' => $dependent['birthdate'],
        ':is_minor' => $dependent['is_minor'],
        ':id' => $dependentId,
        ':guardian_user_id' => $guardianUserId,
    ]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('Profil lie introuvable.');
    }
}

function manager_admin_delete_dependent(PDO $db, int $guardianUserId, int $dependentId): void {
    if ($guardianUserId <= 0 || $dependentId <= 0) {
        throw new InvalidArgumentException('Profil lie invalide.');
    }

    $stmt = $db->prepare('DELETE FROM member_dependents WHERE id = :id AND guardian_user_id = :guardian_user_id');
    $stmt->execute([
        ':id' => $dependentId,
        ':guardian_user_id' => $guardianUserId,
    ]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('Profil lie introuvable.');
    }
}
