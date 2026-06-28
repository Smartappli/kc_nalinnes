<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../manager/member_admin.php';

final class ManagerMemberAdminTest extends TestCase {
    public function testMemberProfileInputIsNormalized(): void {
        $profile = manager_admin_normalize_member_profile_input([
            'target_email' => '  Member@Example.COM ',
            'target_username' => '  Alice Dupont  ',
            'target_first_name' => ' Alice ',
            'target_last_name' => ' Dupont ',
        ]);

        $this->assertSame('member@example.com', $profile['email']);
        $this->assertSame('Alice Dupont', $profile['username']);
        $this->assertSame('Alice', $profile['first_name']);
        $this->assertSame('Dupont', $profile['last_name']);

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
            'new_member_first_name' => ' New ',
            'new_member_last_name' => ' Member ',
            'new_member_password' => ' Secret123! ',
            'new_member_role' => 'admin',
            'new_member_grade' => ' 8e kyu ',
        ]);

        $this->assertSame('new@example.com', $member['email']);
        $this->assertSame('New Member', $member['username']);
        $this->assertSame('New', $member['first_name']);
        $this->assertSame('Member', $member['last_name']);
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
            'target_first_name' => 'Nouveau',
            'target_last_name' => 'Nom',
        ]);

        $this->assertSame('member@example.com', $updated['old_email']);
        $this->assertSame('new@example.com', $updated['email']);
        $this->assertSame('Nouveau', $updated['first_name']);
        $this->assertSame('Nom', $updated['last_name']);
        $this->assertSame('new@example.com', $db->users[1]['email']);
        $this->assertSame('Nouveau Nom', $db->users[1]['username']);
        $this->assertSame(['first_name' => 'Nouveau', 'last_name' => 'Nom'], $db->profiles[1]);
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
            'new_member_first_name' => 'Created',
            'new_member_last_name' => 'User',
            'new_member_password' => 'Secret123!',
            'new_member_role' => 'admin',
            'new_member_grade' => '7e kyu',
        ]);

        $this->assertSame(3, $created['id']);
        $this->assertSame('created@example.com', $created['email']);
        $this->assertSame('Created', $created['first_name']);
        $this->assertSame('User', $created['last_name']);
        $this->assertSame('created@example.com', $db->users[3]['email']);
        $this->assertSame('Created User', $db->users[3]['username']);
        $this->assertSame(['first_name' => 'Created', 'last_name' => 'User'], $db->profiles[3]);
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

    public function testGradeHistoryLifecycleStoresDates(): void {
        $db = new FakeMemberAdminPdo();

        $gradeId = member_record_add_grade($db, 1, [
            'grade' => ' 6e kyu ',
            'obtained_at' => '2026-03-14',
        ]);

        $this->assertSame(20, $gradeId);
        $this->assertSame([
            'id' => 20,
            'user_id' => 1,
            'grade' => '6e kyu',
            'obtained_at' => '2026-03-14',
        ], $db->gradeHistory[20]);

        member_record_delete_grade($db, 1, $gradeId);

        $this->assertArrayNotHasKey(20, $db->gradeHistory);
    }

    public function testAnnualAndMonthlyPaymentsArePersistedAndGrouped(): void {
        $db = new FakeMemberAdminPdo();

        $annualPayment = member_record_save_payment($db, 1, [
            'period_type' => 'annual',
            'period_year' => '2026',
            'payment_status' => 'paid',
            'paid_at' => '2026-01-05',
        ]);
        member_record_save_payment($db, 1, [
            'period_type' => 'monthly',
            'period_year' => '2026',
            'period_month' => '9',
            'payment_status' => 'pending',
        ]);

        $this->assertSame(0, $annualPayment['period_month']);
        $this->assertTrue(member_record_annual_payment_is_paid($db, 1, 2026));
        $grouped = member_record_payments_by_user_id($db, 2026);
        $this->assertSame('paid', $grouped[1]['annual']['status']);
        $this->assertSame('pending', $grouped[1]['monthly'][9]['status']);

        member_record_save_payment($db, 1, [
            'period_type' => 'annual',
            'period_year' => '2026',
            'payment_status' => 'unpaid',
        ]);

        $this->assertFalse(member_record_annual_payment_is_paid($db, 1, 2026));
    }

    public function testGradeHistoryAndPaymentInputsAreNormalized(): void {
        $grade = member_record_normalize_grade_input([
            'grade' => ' 6e kyu ',
            'obtained_at' => '2026-03-14',
        ]);
        $payment = member_record_normalize_payment_input([
            'period_type' => 'monthly',
            'period_year' => '2026',
            'period_month' => '9',
            'payment_status' => 'paid',
            'paid_at' => '2026-09-02',
        ]);
        $annualPayment = member_record_normalize_payment_input([
            'period_type' => 'annual',
            'period_year' => '2026',
            'payment_status' => 'pending',
        ]);

        $this->assertSame(['grade' => '6e kyu', 'obtained_at' => '2026-03-14'], $grade);
        $this->assertSame('monthly', $payment['period_type']);
        $this->assertSame(2026, $payment['period_year']);
        $this->assertSame(9, $payment['period_month']);
        $this->assertSame('paid', $payment['status']);
        $this->assertSame('2026-09-02', $payment['paid_at']);
        $this->assertSame(0, $annualPayment['period_month']);
    }

    public function testMemberDisplayNameUsesProfileBeforeUsername(): void {
        $this->assertSame(
            'Alice Dupont',
            member_record_display_name(['email' => 'alice@example.com', 'username' => 'Fallback'], ['first_name' => 'Alice', 'last_name' => 'Dupont'])
        );
        $this->assertSame(
            'Fallback',
            member_record_display_name(['email' => 'alice@example.com', 'username' => 'Fallback'], ['first_name' => null, 'last_name' => null])
        );
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

    /** @var array<int, array{first_name:?string,last_name:?string}> */
    public array $profiles = [];

    /** @var array<int, array{id:int,user_id:int,grade:string,obtained_at:string}> */
    public array $gradeHistory = [];

    /** @var array<string, array{user_id:int,period_type:string,period_year:int,period_month:int,status:string,paid_at:?string}> */
    public array $payments = [];

    /** @var list<string> */
    public array $adminEmails = [];

    public int $nextUserId = 3;

    public int $nextDependentId = 10;

    public int $nextGradeId = 20;

    public string $lastInsertIdValue = '0';

    public function __construct() {}

    public function exec(string $statement): int|false {
        return 0;
    }

    public function prepare(string $query, array $options = []): PDOStatement|false {
        return new FakeMemberAdminStatement($this, $query);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false {
        $statement = new FakeMemberAdminStatement($this, $query);
        $statement->execute();

        return $statement;
    }

    public function lastInsertId(?string $name = null): string|false {
        return $this->lastInsertIdValue;
    }

    public function paymentKey(int $userId, string $periodType, int $periodYear, int $periodMonth): string {
        return $userId . '|' . $periodType . '|' . $periodYear . '|' . $periodMonth;
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

    /** @var list<array> */
    private array $rows = [];

    private mixed $column = false;

    private int $affectedRows = 0;

    public function __construct(private FakeMemberAdminPdo $db, private string $query) {}

    public function execute(?array $params = null): bool {
        $params ??= [];
        $this->row = null;
        $this->rows = [];
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

        if (stripos($this->query, 'INSERT INTO member_profiles') !== false) {
            $this->db->profiles[(int)$params[':user_id']] = [
                'first_name' => $params[':first_name'] === null ? null : (string)$params[':first_name'],
                'last_name' => $params[':last_name'] === null ? null : (string)$params[':last_name'],
            ];
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'SELECT user_id, first_name, last_name FROM member_profiles') !== false) {
            foreach ($this->db->profiles as $userId => $profile) {
                $this->rows[] = [
                    'user_id' => $userId,
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                ];
            }
            return true;
        }

        if (stripos($this->query, 'SELECT first_name, last_name FROM member_profiles') !== false) {
            $id = (int)($params[':user_id'] ?? 0);
            $this->row = $this->db->profiles[$id] ?? null;
            return true;
        }

        if (stripos($this->query, 'INSERT INTO member_dependents') !== false) {
            $id = $this->db->nextDependentId++;
            $this->db->lastInsertIdValue = (string)$id;
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

        if (stripos($this->query, 'INSERT INTO member_grade_history') !== false) {
            $id = $this->db->nextGradeId++;
            $this->db->lastInsertIdValue = (string)$id;
            $this->db->gradeHistory[$id] = [
                'id' => $id,
                'user_id' => (int)$params[':user_id'],
                'grade' => (string)$params[':grade'],
                'obtained_at' => (string)$params[':obtained_at'],
            ];
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'DELETE FROM member_grade_history') !== false) {
            $id = (int)($params[':id'] ?? 0);
            $userId = (int)($params[':user_id'] ?? 0);
            if (isset($this->db->gradeHistory[$id]) && $this->db->gradeHistory[$id]['user_id'] === $userId) {
                unset($this->db->gradeHistory[$id]);
                $this->affectedRows = 1;
            }
            return true;
        }

        if (stripos($this->query, 'SELECT id, user_id, grade, obtained_at FROM member_grade_history') !== false) {
            $this->rows = array_values($this->db->gradeHistory);
            usort($this->rows, static function (array $a, array $b): int {
                $byUser = $a['user_id'] <=> $b['user_id'];
                if ($byUser !== 0) {
                    return $byUser;
                }

                $byDate = strcmp((string)$b['obtained_at'], (string)$a['obtained_at']);
                if ($byDate !== 0) {
                    return $byDate;
                }

                return $b['id'] <=> $a['id'];
            });
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

        if (stripos($this->query, 'INSERT INTO member_payments') !== false) {
            $userId = (int)$params[':user_id'];
            $periodType = (string)$params[':period_type'];
            $periodYear = (int)$params[':period_year'];
            $periodMonth = (int)$params[':period_month'];
            $this->db->payments[$this->db->paymentKey($userId, $periodType, $periodYear, $periodMonth)] = [
                'user_id' => $userId,
                'period_type' => $periodType,
                'period_year' => $periodYear,
                'period_month' => $periodMonth,
                'status' => (string)$params[':status'],
                'paid_at' => $params[':paid_at'] === null ? null : (string)$params[':paid_at'],
            ];
            $this->affectedRows = 1;
            return true;
        }

        if (stripos($this->query, 'SELECT user_id, period_type, period_year, period_month, status, paid_at FROM member_payments') !== false) {
            $year = (int)($params[':period_year'] ?? 0);
            foreach ($this->db->payments as $payment) {
                if ($payment['period_year'] === $year) {
                    $this->rows[] = $payment;
                }
            }
            usort($this->rows, static function (array $a, array $b): int {
                return [$a['user_id'], $a['period_type'], $a['period_month']] <=> [$b['user_id'], $b['period_type'], $b['period_month']];
            });
            return true;
        }

        if (stripos($this->query, 'SELECT status FROM member_payments') !== false) {
            $userId = (int)($params[':user_id'] ?? 0);
            $year = (int)($params[':period_year'] ?? 0);
            $payment = $this->db->payments[$this->db->paymentKey($userId, 'annual', $year, 0)] ?? null;
            $this->column = is_array($payment) ? $payment['status'] : false;
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

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array {
        return $this->rows;
    }

    public function fetchColumn(int $column = 0): mixed {
        return $this->column;
    }

    public function rowCount(): int {
        return $this->affectedRows;
    }
}
