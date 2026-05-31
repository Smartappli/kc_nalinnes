<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../manager/admin_access.php';

final class AdminAccessTest extends TestCase {
    private FakeAdminPdo $db;

    protected function setUp(): void {
        $this->db = new FakeAdminPdo();
    }

    public function testParseAdminEmailsNormalizesAndFiltersValues(): void {
        $result = parse_admin_emails(' Admin@Example.com, ,owner@example.com  ,');
        $this->assertSame(['admin@example.com', 'owner@example.com'], $result);
    }

    public function testResolveDashboardPathRoutesAdminToManagerDashboard(): void {
        set_admin_role($this->db, 'admin@example.com', true);
        $path = resolve_dashboard_path('admin@example.com', $this->db, '');
        $this->assertSame('/manager/dashboard.php', $path);
    }

    public function testResolveDashboardPathRoutesMemberToMemberDashboard(): void {
        set_admin_role($this->db, 'admin@example.com', true);
        $path = resolve_dashboard_path('member@example.com', $this->db, '');
        $this->assertSame('/member/dashboard.php', $path);
    }

    public function testResolveDashboardPathRoutesMemberWhenNoAdminIsConfigured(): void {
        $path = resolve_dashboard_path('member@example.com', $this->db, '');
        $this->assertSame('/member/dashboard.php', $path);
    }

    public function testSetAdminRoleRemovesAdmin(): void {
        set_admin_role($this->db, 'admin@example.com', true);
        set_admin_role($this->db, 'admin@example.com', false);
        $admins = get_db_admin_emails($this->db);
        $this->assertSame([], $admins);
    }

    public function testResolveDashboardPathUsesEnvironmentAdminsWhenAdminTableIsUnavailable(): void {
        $path = resolve_dashboard_path('admin@example.com', new BrokenAdminPdo(), 'admin@example.com');
        $this->assertSame('/manager/dashboard.php', $path);
    }
}

final class FakeAdminPdo extends \PDO {
    /** @var list<string> */
    public array $adminEmails = [];

    public function __construct() {}

    public function exec(string $statement): int|false {
        return 0;
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false {
        return new FakeAdminStatement($this, 'select');
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false {
        if (stripos($query, 'INSERT INTO admin_users') !== false) {
            return new FakeAdminStatement($this, 'insert');
        }

        if (stripos($query, 'DELETE FROM admin_users') !== false) {
            return new FakeAdminStatement($this, 'delete');
        }

        return new FakeAdminStatement($this, 'noop');
    }
}

final class BrokenAdminPdo extends \PDO {
    public function __construct() {}

    public function exec(string $statement): int|false {
        throw new \RuntimeException('admin table unavailable');
    }
}

final class FakeAdminStatement extends \PDOStatement {
    public function __construct(private FakeAdminPdo $db, private string $operation) {}

    public function execute(?array $params = null): bool {
        $email = normalize_email((string)($params[':email'] ?? ''));

        if ($this->operation === 'insert' && $email !== '' && !in_array($email, $this->db->adminEmails, true)) {
            $this->db->adminEmails[] = $email;
        }

        if ($this->operation === 'delete') {
            $this->db->adminEmails = array_values(array_filter(
                $this->db->adminEmails,
                static fn(string $adminEmail): bool => $adminEmail !== $email
            ));
        }

        return true;
    }

    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array {
        return $this->db->adminEmails;
    }
}
