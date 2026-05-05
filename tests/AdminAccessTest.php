<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../manager/admin_access.php';

final class AdminAccessTest extends TestCase {
    public function testParseAdminEmailsNormalizesAndFiltersValues(): void {
        $result = parse_admin_emails(' Admin@Example.com, ,owner@example.com  ,');

        $this->assertSame(['admin@example.com', 'owner@example.com'], $result);
    }

    public function testParseAdminEmailsThrowsWhenEmpty(): void {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Configuration ADMIN_EMAILS manquante ou vide.');

        parse_admin_emails(' ,   ,');
    }


    public function testResolveDashboardPathRoutesAdminToManagerDashboard(): void {
        $path = resolve_dashboard_path('admin@example.com', 'admin@example.com,owner@example.com');
        $this->assertSame('/manager/dashboard.php', $path);
    }

    public function testResolveDashboardPathRoutesMemberToMemberDashboard(): void {
        $path = resolve_dashboard_path('member@example.com', 'admin@example.com');
        $this->assertSame('/member/dashboard.php', $path);
    }

    public function testIsAdminEmailIsCaseInsensitive(): void {
        $admins = ['admin@example.com'];

        $this->assertTrue(is_admin_email('ADMIN@example.com', $admins));
        $this->assertFalse(is_admin_email('member@example.com', $admins));
    }
}
