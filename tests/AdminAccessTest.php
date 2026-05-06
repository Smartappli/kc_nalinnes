<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../manager/admin_access.php';

final class AdminAccessTest extends TestCase {
    private \PDO $db;

    protected function setUp(): void {
        $this->db = new \PDO('sqlite::memory:');
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

    public function testSetAdminRoleRemovesAdmin(): void {
        set_admin_role($this->db, 'admin@example.com', true);
        set_admin_role($this->db, 'admin@example.com', false);
        $admins = get_db_admin_emails($this->db);
        $this->assertSame([], $admins);
    }
}
