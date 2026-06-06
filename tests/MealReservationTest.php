<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../member/meal_reservation.php';

final class MealReservationTest extends TestCase {
    private array $tempPaths = [];

    protected function tearDown(): void {
        foreach (array_reverse($this->tempPaths) as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
            elseif (is_dir($path)) {
                @rmdir($path);
            }
        }

        $this->tempPaths = [];
    }

    public function testComputeMealTotalWithDefaultPrices(): void {
        $this->assertSame(58, compute_meal_total(2, 2));
    }

    public function testComputeMealTotalNeverUsesNegativeQuantities(): void {
        $this->assertSame(10, compute_meal_total(-2, 1));
        $this->assertSame(0, compute_meal_total(-1, -1));
    }

    public function testComputeMealTotalWithCustomPrices(): void {
        $this->assertSame(42, compute_meal_total(2, 1, 16, 10));
    }

    public function testMealReservationSubmissionTokenCanOnlyBeConsumedOnce(): void {
        $_SESSION = [];

        $token = meal_reservation_submission_token('public');

        $this->assertTrue(consume_meal_reservation_submission_token('public', $token));
        $this->assertFalse(consume_meal_reservation_submission_token('public', $token));
    }

    public function testMealReservationSubmissionTokenIsScoped(): void {
        $_SESSION = [];

        $publicToken = meal_reservation_submission_token('public');
        $memberToken = meal_reservation_submission_token('member');

        $this->assertFalse(consume_meal_reservation_submission_token('member', $publicToken));
        $this->assertTrue(consume_meal_reservation_submission_token('public', $publicToken));
        $this->assertTrue(consume_meal_reservation_submission_token('member', $memberToken));
    }

    public function testMealReservationsExcelHeadersContainPublicReservationFields(): void {
        $this->assertSame(
            [
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
            ],
            meal_reservations_excel_headers()
        );
    }

    public function testAppendMealReservationToExcelAddsRowsInOrder(): void {
        $dir = $this->makeTempDir();
        $path = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xls';

        append_meal_reservation_to_excel($this->reservationRow([
            'date' => '2026-06-01 12:00:00',
            'profile_name' => 'Premier',
            'contact_email' => 'premier@example.com',
        ]), $path);

        append_meal_reservation_to_excel($this->reservationRow([
            'date' => '2026-06-01 12:05:00',
            'profile_name' => 'Second',
            'contact_email' => 'second@example.com',
            'adult_qty' => '2',
            'child_qty' => '0',
            'total_amount' => '38',
        ]), $path);

        $rows = read_meal_reservations_excel_rows($path);

        $this->assertFileExists($path);
        $this->assertCount(2, $rows);
        $this->assertSame('Premier', $rows[0][2]);
        $this->assertSame('premier@example.com', $rows[0][4]);
        $this->assertSame('Second', $rows[1][2]);
        $this->assertSame('38', $rows[1][8]);
    }

    public function testWriteMealReservationsExcelUsesXlsxWhenZipArchiveIsAvailable(): void {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive is not available in this PHP environment.');
        }

        $dir = $this->makeTempDir();
        $path = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xlsx';

        write_meal_reservations_excel($path, [
            array_values($this->reservationRow([
                'profile_name' => 'Prod XLSX',
                'contact_email' => 'prod@example.com',
            ])),
        ]);

        $rows = read_meal_reservations_excel_rows($path);

        $this->assertFileExists($path);
        $this->assertCount(1, $rows);
        $this->assertSame('Prod XLSX', $rows[0][2]);
        $this->assertSame('prod@example.com', $rows[0][4]);
    }

    public function testSavePublicMealReservationPersistsContactFieldsInDatabase(): void {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite is not available in this PHP environment.');
        }

        $db = new \PDO('sqlite::memory:');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->createSqliteMealReservationsTable($db);

        $id = save_public_meal_reservation($db, [
            'profile_name' => 'Client Public',
            'contact_email' => 'client@example.com',
            'contact_phone' => '+32 499 00 00 00',
            'adult_qty' => 1,
            'child_qty' => 2,
            'total_amount' => 39,
            'notes' => 'Sans sauce',
        ]);

        $row = $db->query('SELECT * FROM meal_reservations WHERE id = ' . $id)->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($row);
        $this->assertSame('0', (string)$row['member_user_id']);
        $this->assertSame('public', $row['profile_type']);
        $this->assertSame('Client Public', $row['profile_name']);
        $this->assertSame('client@example.com', $row['contact_email']);
        $this->assertSame('+32 499 00 00 00', $row['contact_phone']);
        $this->assertSame('Sans sauce', $row['notes']);
        $this->assertSame('1', (string)$row['adult_qty']);
        $this->assertSame('2', (string)$row['child_qty']);
        $this->assertSame('39', (string)$row['total_amount']);
    }

    public function testEnsureMealReservationsTableAddsMissingPublicColumns(): void {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite is not available in this PHP environment.');
        }

        $db = new \PDO('sqlite::memory:');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->exec('CREATE TABLE meal_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            member_user_id INTEGER NOT NULL,
            profile_type TEXT NOT NULL,
            dependent_id INTEGER NULL,
            profile_name TEXT NOT NULL,
            adult_qty INTEGER NOT NULL DEFAULT 0,
            child_qty INTEGER NOT NULL DEFAULT 0,
            total_amount INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )');

        ensure_meal_reservations_table($db);

        $columns = array_map(
            static fn(array $row): string => (string)$row['name'],
            $db->query('PRAGMA table_info(meal_reservations)')->fetchAll(\PDO::FETCH_ASSOC)
        );

        $this->assertContains('contact_email', $columns);
        $this->assertContains('contact_phone', $columns);
        $this->assertContains('notes', $columns);
    }

    private function reservationRow(array $overrides = []): array {
        return array_merge([
            'date' => '2026-06-01 12:00:00',
            'member_user_id' => '0',
            'profile_name' => 'Test Public',
            'profile_type' => 'public',
            'contact_email' => 'test@example.com',
            'contact_phone' => '+320000000',
            'adult_qty' => '1',
            'child_qty' => '2',
            'total_amount' => '39',
            'notes' => 'note',
        ], $overrides);
    }

    private function makeTempDir(): string {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kc-meal-test-' . uniqid('', true);
        mkdir($dir);
        $this->tempPaths[] = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xls';
        $this->tempPaths[] = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xls.lock';
        $this->tempPaths[] = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xlsx';
        $this->tempPaths[] = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xlsx.lock';
        $this->tempPaths[] = $dir;

        return $dir;
    }

    private function createSqliteMealReservationsTable(\PDO $db): void {
        $db->exec('CREATE TABLE meal_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            member_user_id INTEGER NOT NULL,
            profile_type TEXT NOT NULL,
            dependent_id INTEGER NULL,
            profile_name TEXT NOT NULL,
            contact_email TEXT NULL,
            contact_phone TEXT NULL,
            notes TEXT NULL,
            adult_qty INTEGER NOT NULL DEFAULT 0,
            child_qty INTEGER NOT NULL DEFAULT 0,
            total_amount INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )');
    }
}
