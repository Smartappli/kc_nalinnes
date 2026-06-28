<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../manager/member_admin.php';

final class ManagerMemberAdminTest extends TestCase {
    public function testMemberProfileInputIsNormalized(): void {
        $profile = manager_admin_normalize_member_profile_input([
            'target_email' => '  Member@Example.COM ',
            'target_username' => '  Alice Dupont  ',
        ]);

        $this->assertSame('member@example.com', $profile['email']);
        $this->assertSame('Alice Dupont', $profile['username']);

        $profile = manager_admin_normalize_member_profile_input([
            'target_email' => 'member@example.com',
            'target_username' => '   ',
        ]);

        $this->assertNull($profile['username']);
    }

    public function testMemberProfileInputRejectsInvalidEmail(): void {
        $this->expectException(InvalidArgumentException::class);

        manager_admin_normalize_member_profile_input([
            'target_email' => 'not-an-email',
            'target_username' => 'Alice',
        ]);
    }

    public function testDependentInputIsNormalized(): void {
        $dependent = manager_admin_normalize_dependent_input([
            'dependent_name' => '  Enfant Test  ',
            'dependent_birthdate' => '2015-04-12',
            'dependent_is_minor' => '0',
        ]);

        $this->assertSame('Enfant Test', $dependent['full_name']);
        $this->assertSame('2015-04-12', $dependent['birthdate']);
        $this->assertSame(0, $dependent['is_minor']);
    }

    public function testDependentInputRejectsInvalidBirthdate(): void {
        $this->expectException(InvalidArgumentException::class);

        manager_admin_normalize_dependent_input([
            'dependent_name' => 'Enfant Test',
            'dependent_birthdate' => '2015-15-99',
            'dependent_is_minor' => '1',
        ]);
    }

    public function testMemberCreationInputIsNormalized(): void {
        $member = manager_admin_normalize_member_creation_input([
            'new_member_email' => ' New@Example.COM ',
            'new_member_username' => ' New Member ',
            'new_member_password' => ' Secret123! ',
            'new_member_role' => 'admin',
            'new_member_grade' => ' 8e kyu ',
        ]);

        $this->assertSame('new@example.com', $member['email']);
        $this->assertSame('New Member', $member['username']);
        $this->assertSame('Secret123!', $member['password']);
        $this->assertSame('admin', $member['role']);
        $this->assertSame('8e kyu', $member['grade']);
    }

    public function testMemberCreationInputRejectsShortPassword(): void {
        $this->expectException(InvalidArgumentException::class);

        manager_admin_normalize_member_creation_input([
            'new_member_email' => 'new@example.com',
            'new_member_password' => 'short',
            'new_member_role' => 'member',
        ]);
    }

    public function testMemberCreationInputRejectsInvalidRole(): void {
        $this->expectException(InvalidArgumentException::class);

        manager_admin_normalize_member_creation_input([
            'new_member_email' => 'new@example.com',
            'new_member_password' => 'Secret123!',
            'new_member_role' => 'owner',
        ]);
    }

    public function testMemberGradeRejectsOverlongValue(): void {
        $this->expectException(InvalidArgumentException::class);

        manager_admin_normalize_member_grade(str_repeat('a', 101));
    }

    public function testUpdateMemberProfilePersistsNormalizedData(): void {
        $db = new FakeMemberAdminPdo();

        $updated = manager_admin_update_member_profile($db, 1, [
            'target_email' => ' NEW@Example.com ',
            'target_username' => ' Nouveau Nom ',
        ]);

        $this->assertSame('member@example.com', $updated['old_email']);
        $this->assertSame('new@example.com', $updated['email']);
        $this->assertSame('new@example.com', $db->users[1]['email']);
        $this->assertSame('Nouveau Nom', $db->users[1]['username']);
    }

    public function testUpdateMemberProfileRejectsDuplicateEmail(): void {
        $db = new FakeMemberAdminPdo();

        $this->expectException(RuntimeException::class);

        manager_admin_update_member_profile($db, 1, [
            'target_email' => 'other@example.com',
            'target_username' => 'Member',
        ]);
    }

    public function testCreateMemberPersistsAccountGradeAndAdminRole(): void {
        $db = new FakeMemberAdminPdo();
        $auth = new FakeMemberAdminAuth($db);

        $created = manager_admin_create_member($db, $auth, [
            'new_member_email' => ' Created@Example.com ',
            'new_member_username' => ' Created User ',
            'new_member_password' => 'Secret123!',
            'new_member_role' => 'admin',
            'new_member_grade' => '7e kyu',
        ]);

        $this->assertSame(3, $created['id']);
        $this->assertSame('created@example.com', $created['email']);
        $this->assertSame('created@example.com', $db->users[3]['email']);
        $this->assertSame('Created User', $db->users[3]['username']);
        $this->assertSame('Secret123!', $db->passwords[3]);
        $this->assertSame('7e kyu', $db->grades[3]);
        $this->assertContains('created@example.com', $db->adminEmails);
    }

    public function testCreateMemberWithMemberRoleDoesNotGrantAdminAccess(): void {
        $db = new FakeMemberAdminPdo();
        $auth = new FakeMemberAdminAuth($db);

        $created = manager_admin_create_member($db, $auth, [
            'new_member_email' => 'member-created@example.com',
            'new_member_username' => '',
            'new_member_password' => 'Secret123!',
            'new_member_role' => 'member',
            'new_member_grade' => '',
        ]);

        $this->assertSame(3, $created['id']);
        $this->assertNull($created['username']);
        $this->assertNull($created['grade']);
        $this->assertSame([], $db->adminEmails);
        $this->assertArrayNotHasKey(3, $db->grades);
    }

    public function testCreateMemberRejectsDuplicateEmail(): void {
        $db = new FakeMemberAdminPdo();
        $auth = new FakeMemberAdminAuth($db);

        $this->expectException(\Delight\Auth\UserAlreadyExistsException::class);

        manager_admin_create_member($db, $auth, [
            'new_member_email' => 'member@example.com',
            'new_member_password' => 'Secret123!',
            'new_member_role' => 'member',
        ]);
    }

    public function testResetMemberPasswordUsesAuthAdministration(): void {
        $db = new FakeMemberAdminPdo();
        $auth = new FakeMemberAdminAuth($db);

        manager_admin_reset_member_password($db, $auth, 1, 'Reset123!');

        $this->assertSame('Reset123!', $db->passwords[1]);
    }

    public function testResetMemberPasswordRejectsUnknownMember(): void {
        $db = new FakeMemberAdminPdo();
        $auth = new FakeMemberAdminAuth($db);

        $this->expectException(RuntimeException::class);

        manager_admin_reset_member_password($db, $auth, 999, 'Reset123!');
    }

    public function testSaveGradeCanClearExistingGrade(): void {
        $db = new FakeMemberAdminPdo();
        $db->grades[1] = '9e kyu';

        manager_admin_save_grade($db, 1, null);

        $this->assertArrayNotHasKey(1, $db->grades);
    }

    public function testAuthExceptionMessagesAreReadable(): void {
        $this->assertSame(
            'Cet email est deja utilise par un autre membre.',
            manager_admin_auth_exception_message(new \Delight\Auth\UserAlreadyExistsException())
        );
        $this->assertSame(
            'Membre introuvable.',
            manager_admin_auth_exception_message(new \Delight\Auth\UnknownIdException())
        );
        $this->assertSame(
            'Operation membre impossible.',
            manager_admin_auth_exception_message(new RuntimeException())
        );
    }

    public function testDependentLifecycleUsesGuardianScope(): void {
        $db = new FakeMemberAdminPdo();

        $dependentId = manager_admin_add_dependent($db, 1, [
            'dependent_name' => ' Karate Kid ',
            'dependent_birthdate' => '2016-06-01',
            'dependent_is_minor' => '1',
        ]);

        $this->assertSame(10, $dependentId);
        $this->assertSame('Karate Kid', $db->dependents[10]['full_name']);

        manager_admin_update_dependent($db, 1, 10, [
            'dependent_name' => 'Karate Adulte',
            'dependent_birthdate' => '',
            'dependent_is_minor' => '0',
        ]);

        $this->assertSame('Karate Adulte', $db->dependents[10]['full_name']);
        $this->assertNull($db->dependents[10]['birthdate']);
        $this->assertSame(0, $db->dependents[10]['is_minor']);

        manager_admin_delete_dependent($db, 1, 10);
        $this->assertArrayNotHasKey(10, $db->dependents);
    }
}

final class FakeMemberAdminPdo extends PDO {
    /** @var array<int, array{id:int,email:string,username:?string}> */
    public array $users = [
        1 => ['id' => 1, 'email' => 'member@example.com', 'username' => 'Member'],
        2 => ['id' => 2, 'email' => 'other@example.com', 'username' => 'Other'],
    ];

    /** @var array<int, array{id:int,guardian_user_id:int,full_name:string,birthdate:?string,is_minor:int}> */
    public array $dependents = [];

    /** @var array<int, string> */
    public array $passwords = [
        1 => 'OldPass123!',
        2 => 'OtherPass123!',
    ];

    /** @var array<int, string> */
    public array $grades = [];

    /** @var list<string> */
    public array $adminEmails = [];

    public int $nextUserId = 3;

    public int $nextDependentId = 10;

    public function __construct() {}

    public function exec(string $statement): int|false {
        return 0;
    }

    public function prepare(string $query, array $options = []): PDOStatement|false {
        return new FakeMemberAdminStatement($this, $query);
    }

    public function lastInsertId(?string $name = null): string|false {
        return (string)($this->nextDependentId - 1);
    }
}

final class FakeMemberAdminAuth {
    public function __construct(private FakeMemberAdminPdo $db) {}

    public function admin(): FakeMemberAdminAdministration {
        return new FakeMemberAdminAdministration($this->db);
    }
}

final class FakeMemberAdminAdministration {
    public function __construct(private FakeMemberAdminPdo $db) {}

    public function createUser(string $email, string $password, ?string $username = null): int {
        $email = normalize_email($email);
        foreach ($this->db->users as $user) {
            if (normalize_email((string)$user['email']) === $email) {
                throw new \Delight\Auth\UserAlreadyExistsException();
            }
        }

        $id = $this->db->nextUserId++;
        $this->db->users[$id] = [
            'id' => $id,
            'email' => $email,
            'username' => $username,
        ];
        $this->db->passwords[$id] = $password;

        return $id;
    }

    public function changePasswordForUserById(int $userId, string $password): void {
        if (!isset($this->db->users[$userId])) {
            throw new \Delight\Auth\UnknownIdException();
        }

        $this->db->passwords[$userId] = $password;
    }
}

final class FakeMemberAdminStatement extends PDOStatement {
    private ?array $row = null;

    private mixed $column = false;

    private int $affectedRows = 0;

    public function __construct(private FakeMemberAdminPdo $db, private string $query) {}

    public function execute(?array $params = null): bool {
        $params ??= [];
        $this->row = null;
        $this->column = false;
        $this->affectedRows = 0;

        if (stripos($this->query, 'SELECT id, email, username FROM users') !== false) {
            $id = (int)($params[':id'] ?? 0);
            $this->row = $this->db->users[$id] ?? null;
            return true;
        }

        if (stripos($this->query, 'SELECT id FROM users WHERE email') !== false) {
            $email = normalize_email((string)($params[':email'] ?? ''));
            $currentId = (int)($params[':id'] ?? 0);
            foreach ($this->db->users as $id => $user) {
                if ($id !== $currentId && normalize_email((string)$user['email']) === $email) {
                    $this->column = $id;
                    break;
                }
            }
            return true;
        }

        if (stripos($this->query, 'UPDATE users SET email') !== false) {
            $id = (int)($params[':id'] ?? 0);
            if (isset($this->db->users[$id])) {
                $this->db->users[$id]['email'] = (string)$params[':email'];
                $this->db->users[$id]['username'] = $params[':username'] === null ? null : (string)$params[':username'];
                $this->affectedRows = 1;
            }
            return true;
        }

        if (stripos($this->query, 'INSERT INTO member_dependents') !== false) {
            $id = $this->db->nextDependentId++;
            $this->db->dependents[$id] = [
                'id' => $id,
                'guardian_user_id' => (int)$params[':guardian_user_id'],
                'full_name' => (string)$params[':full_name'],
                'birthdate' => $params[':birthdate'] === null ? null : (string)$params[':birthdate'],
                'is_minor' => (int)$params[':is_minor'],
            ];
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'INSERT INTO member_grades') !== false) {
            $this->db->grades[(int)$params[':user_id']] = (string)$params[':grade'];
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'DELETE FROM member_grades') !== false) {
            unset($this->db->grades[(int)$params[':user_id']]);
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'INSERT INTO admin_users') !== false) {
            $email = normalize_email((string)$params[':email']);
            if ($email !== '' && !in_array($email, $this->db->adminEmails, true)) {
                $this->db->adminEmails[] = $email;
            }
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'DELETE FROM admin_users') !== false) {
            $email = normalize_email((string)$params[':email']);
            $this->db->adminEmails = array_values(array_filter(
                $this->db->adminEmails,
                static fn(string $adminEmail): bool => $adminEmail !== $email
            ));
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'SELECT id FROM member_dependents') !== false) {
            $id = (int)($params[':id'] ?? 0);
            $guardianId = (int)($params[':guardian_user_id'] ?? 0);
            if (isset($this->db->dependents[$id]) && $this->db->dependents[$id]['guardian_user_id'] === $guardianId) {
                $this->column = $id;
            }
            return true;
        }

        if (stripos($this->query, 'UPDATE member_dependents SET') !== false) {
            $id = (int)($params[':id'] ?? 0);
            $guardianId = (int)($params[':guardian_user_id'] ?? 0);
            if (isset($this->db->dependents[$id]) && $this->db->dependents[$id]['guardian_user_id'] === $guardianId) {
                $this->db->dependents[$id]['full_name'] = (string)$params[':full_name'];
                $this->db->dependents[$id]['birthdate'] = $params[':birthdate'] === null ? null : (string)$params[':birthdate'];
                $this->db->dependents[$id]['is_minor'] = (int)$params[':is_minor'];
                $this->affectedRows = 1;
            }
            return true;
        }

        if (stripos($this->query, 'DELETE FROM member_dependents') !== false) {
            $id = (int)($params[':id'] ?? 0);
            $guardianId = (int)($params[':guardian_user_id'] ?? 0);
            if (isset($this->db->dependents[$id]) && $this->db->dependents[$id]['guardian_user_id'] === $guardianId) {
                unset($this->db->dependents[$id]);
                $this->affectedRows = 1;
            }
            return true;
        }

        return true;
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed {
        return $this->row ?? false;
    }

    public function fetchColumn(int $column = 0): mixed {
        return $this->column;
    }

    public function rowCount(): int {
        return $this->affectedRows;
    }
}
