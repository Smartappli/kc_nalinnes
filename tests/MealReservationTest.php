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

    public function testComputeMealTotalWithDecimalPrices(): void {
        $this->assertSame(49.5, compute_meal_total(2, 1, 19.5, 10.5));
    }

    public function testMealSettingsInputIsNormalized(): void {
        $settings = normalize_meal_settings_input([
            'adult_menu' => '  Brochette + saucisse  ',
            'child_menu' => '  Saucisse enfant  ',
            'adult_price' => '19,50',
            'child_price' => '10.25',
            'reservation_deadline_at' => '2026-06-22T12:00',
            'meal_at' => '2026-06-26T20:00',
        ]);

        $this->assertSame('Brochette + saucisse', $settings['adult_menu']);
        $this->assertSame('Saucisse enfant', $settings['child_menu']);
        $this->assertSame(19.5, $settings['adult_price']);
        $this->assertSame(10.25, $settings['child_price']);
        $this->assertSame('2026-06-22 12:00:00', $settings['reservation_deadline_at']);
        $this->assertSame('2026-06-26 20:00:00', $settings['meal_at']);
        $this->assertSame('19,50', meal_price_label((float)$settings['adult_price']));
        $this->assertSame('22/06/2026 12:00', meal_datetime_label((string)$settings['reservation_deadline_at']));
    }

    public function testMealSettingsRejectDeadlineAfterMealDate(): void {
        $this->expectException(\InvalidArgumentException::class);

        normalize_meal_settings_input([
            'reservation_deadline_at' => '2026-06-27T12:00',
            'meal_at' => '2026-06-26T20:00',
        ]);
    }

    public function testMealSettingsRejectInvalidPrice(): void {
        $this->expectException(\InvalidArgumentException::class);

        normalize_meal_settings_input([
            'adult_price' => 'prix',
        ]);
    }

    public function testMealSettingsRejectOutOfRangePrice(): void {
        $this->expectException(\InvalidArgumentException::class);

        normalize_meal_settings_input([
            'child_price' => '10000',
        ]);
    }

    public function testMealSettingsAllowBlankDatesAndKeepReservationsOpen(): void {
        $settings = normalize_meal_settings_input([
            'reservation_deadline_at' => '',
            'meal_at' => '',
        ]);

        $this->assertNull($settings['reservation_deadline_at']);
        $this->assertNull($settings['meal_at']);
        $this->assertSame('-', meal_datetime_label($settings['meal_at']));
        $this->assertSame('', meal_datetime_input_value($settings['reservation_deadline_at']));
        $this->assertTrue(meal_reservations_are_open($settings, new \DateTimeImmutable('2035-01-01 12:00:00')));
    }

    public function testMealSettingsFallbackToDefaultsWhenNoDatabaseRowExists(): void {
        $db = new FakeMealSettingsPdo();

        $settings = meal_settings($db);

        $this->assertSame(meal_default_settings(), $settings);
        $this->assertSame(1, $db->execCount);
    }

    public function testSaveMealSettingsPersistsAndCanBeReadBack(): void {
        $db = new FakeMealSettingsPdo();

        $saved = save_meal_settings($db, [
            'adult_menu' => '  Menu adulte test  ',
            'child_menu' => 'Menu enfant test',
            'adult_price' => '22,75',
            'child_price' => '11.50',
            'reservation_deadline_at' => '2030-06-20T12:30',
            'meal_at' => '2030-06-25T19:45',
        ]);
        $readBack = meal_settings($db);

        $this->assertSame('Menu adulte test', $saved['adult_menu']);
        $this->assertSame('Menu adulte test', $readBack['adult_menu']);
        $this->assertSame(22.75, $readBack['adult_price']);
        $this->assertSame(11.5, $readBack['child_price']);
        $this->assertSame('2030-06-20 12:30:00', $readBack['reservation_deadline_at']);
        $this->assertSame('2030-06-25 19:45:00', $readBack['meal_at']);
    }

    public function testMealReservationOpenStateUsesDeadline(): void {
        $settings = meal_default_settings();

        $this->assertTrue(meal_reservations_are_open($settings, new \DateTimeImmutable('2026-06-22 11:59:00')));
        $this->assertFalse(meal_reservations_are_open($settings, new \DateTimeImmutable('2026-06-22 12:01:00')));
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
                'status',
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
        $this->assertSame('confirmed', $rows[0][4]);
        $this->assertSame('premier@example.com', $rows[0][5]);
        $this->assertSame('Second', $rows[1][2]);
        $this->assertSame('38', $rows[1][9]);
    }

    public function testReadLegacyMealReservationExcelRowsMapsMissingStatusColumn(): void {
        $dir = $this->makeTempDir();
        $path = $dir . DIRECTORY_SEPARATOR . 'reservations-repas.xls';
        $legacyHeaders = meal_reservations_legacy_excel_headers();
        $legacyRow = [
            '2026-06-01 12:00:00',
            '0',
            'Legacy Public',
            'public',
            'legacy@example.com',
            '+320000000',
            '1',
            '2',
            '39',
            'ancienne note',
        ];
        $htmlRows = [
            '<tr>' . implode('', array_map(static fn(string $value): string => '<th>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</th>', $legacyHeaders)) . '</tr>',
            '<tr>' . implode('', array_map(static fn(string $value): string => '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>', $legacyRow)) . '</tr>',
        ];
        file_put_contents($path, '<!doctype html><html><body><table>' . implode('', $htmlRows) . '</table></body></html>');

        $rows = read_meal_reservations_excel_rows($path);

        $this->assertCount(1, $rows);
        $this->assertSame('Legacy Public', $rows[0][2]);
        $this->assertSame('confirmed', $rows[0][4]);
        $this->assertSame('legacy@example.com', $rows[0][5]);
        $this->assertSame('ancienne note', $rows[0][10]);
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
        $this->assertSame('prod@example.com', $rows[0][5]);
    }

    public function testMealReservationStatusesAreValidated(): void {
        $this->assertSame(
            [
                'confirmed' => 'Confirmee',
                'pending' => 'A verifier',
                'paid' => 'Payee',
                'cancelled' => 'Annulee',
            ],
            meal_reservation_statuses()
        );
        $this->assertSame('confirmed', normalize_meal_reservation_status(''));
        $this->assertSame('paid', normalize_meal_reservation_status('paid'));
        $this->assertSame('Annulee', meal_reservation_status_label('cancelled'));
    }

    public function testInvalidMealReservationStatusIsRejected(): void {
        $this->expectException(\InvalidArgumentException::class);

        normalize_meal_reservation_status('unknown');
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
            'status' => 'pending',
        ]);

        $row = $db->query('SELECT * FROM meal_reservations WHERE id = ' . $id)->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($row);
        $this->assertSame('0', (string)$row['member_user_id']);
        $this->assertSame('public', $row['profile_type']);
        $this->assertSame('pending', $row['status']);
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
        $this->assertContains('status', $columns);
    }

    private function reservationRow(array $overrides = []): array {
        return array_merge([
            'date' => '2026-06-01 12:00:00',
            'member_user_id' => '0',
            'profile_name' => 'Test Public',
            'profile_type' => 'public',
            'status' => 'confirmed',
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
            status TEXT NOT NULL DEFAULT \'confirmed\',
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

final class FakeMealSettingsPdo extends \PDO {
    /** @var array<string, mixed>|null */
    public ?array $settingsRow = null;

    public int $execCount = 0;

    public function __construct() {}

    public function exec(string $statement): int|false {
        $this->execCount++;

        return 0;
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false {
        return new FakeMealSettingsStatement($this, 'select');
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false {
        return new FakeMealSettingsStatement($this, 'save');
    }
}

final class FakeMealSettingsStatement extends \PDOStatement {
    public function __construct(private FakeMealSettingsPdo $db, private string $operation) {}

    public function execute(?array $params = null): bool {
        if ($this->operation === 'save') {
            $params ??= [];
            $this->db->settingsRow = [
                'adult_menu' => $params[':adult_menu'] ?? '',
                'child_menu' => $params[':child_menu'] ?? '',
                'adult_price' => $params[':adult_price'] ?? 0,
                'child_price' => $params[':child_price'] ?? 0,
                'reservation_deadline_at' => $params[':reservation_deadline_at'] ?? null,
                'meal_at' => $params[':meal_at'] ?? null,
            ];
        }

        return true;
    }

    public function fetch(int $mode = \PDO::FETCH_DEFAULT, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed {
        return $this->db->settingsRow ?? false;
    }
}
