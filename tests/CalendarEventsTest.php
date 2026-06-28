<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/calendar_events.php';

final class CalendarEventsTest extends TestCase {
    public function testDefaultCalendarPayloadContainsSeparatedAudiences(): void {
        $payload = kc_calendar_events_payload(kc_calendar_default_event_rows());

        $this->assertNotEmpty($payload['fullcalendar']);
        $this->assertNotEmpty($payload['ics']['children']);
        $this->assertNotEmpty($payload['ics']['teens']);
        $this->assertNotEmpty($payload['ics']['adults']);
        $this->assertNotEmpty($payload['ics']['club']);

        $audiences = array_unique(array_map(
            static fn(array $event): string => (string)$event['extendedProps']['audience'],
            $payload['fullcalendar']
        ));
        $eventTypes = array_unique(array_map(
            static fn(array $event): string => (string)$event['extendedProps']['eventType'],
            $payload['fullcalendar']
        ));

        $this->assertContains('children', $audiences);
        $this->assertContains('teens', $audiences);
        $this->assertContains('adults', $audiences);
        $this->assertContains('all', $audiences);
        $this->assertContains('single', $eventTypes);
        $this->assertContains('recurring', $eventTypes);
    }

    public function testSingleEventInputIsNormalizedForStorageAndFullCalendar(): void {
        $row = kc_calendar_normalize_event_input([
            'audience' => 'children',
            'event_type' => 'single',
            'title' => 'Stage enfants',
            'description' => 'Dojo principal',
            'color' => '#3366AA',
            'start_at' => '2026-09-12T10:00',
            'end_at' => '2026-09-12T12:30',
            'is_active' => '1',
            'sort_order' => '25',
        ]);

        $this->assertSame('children', $row['audience']);
        $this->assertSame('single', $row['event_type']);
        $this->assertSame('#3366aa', $row['color']);
        $this->assertSame('2026-09-12 10:00:00', $row['start_at']);
        $this->assertSame('2026-09-12 12:30:00', $row['end_at']);

        $event = kc_calendar_row_to_fullcalendar(array_merge($row, ['id' => 12]));

        $this->assertSame('12', $event['id']);
        $this->assertSame('Stage enfants', $event['title']);
        $this->assertSame('2026-09-12T10:00:00', $event['start']);
        $this->assertSame('children', $event['extendedProps']['audience']);
        $this->assertSame('single', $event['extendedProps']['eventType']);
    }

    public function testRecurringEventInputExpandsForIcs(): void {
        $row = kc_calendar_normalize_event_input([
            'audience' => 'teens',
            'event_type' => 'recurring',
            'title' => 'Cours ados test',
            'days_of_week' => ['5', '1', '5'],
            'start_time' => '18:00',
            'end_time' => '19:00',
            'start_recur' => '2026-09-01',
            'end_recur' => '2026-09-08',
            'is_active' => '1',
            'sort_order' => '30',
        ]);

        $this->assertSame([1, 5], $row['days_of_week']);
        $this->assertSame('18:00:00', $row['start_time']);
        $this->assertSame('19:00:00', $row['end_time']);

        $event = kc_calendar_row_to_fullcalendar(array_merge($row, ['id' => 31]));
        $this->assertSame('recurring', $event['extendedProps']['eventType']);
        $this->assertSame([1, 5], $event['daysOfWeek']);
        $this->assertSame('18:00', $event['startTime']);
        $this->assertSame('2026-09-01', $event['startRecur']);
        $this->assertSame('2026-09-09', $event['endRecur']);

        $expanded = kc_calendar_expand_row_for_ics($row);

        $this->assertSame(
            ['2026-09-04T18:00', '2026-09-07T18:00'],
            array_column($expanded, 'start')
        );
    }

    public function testRecurringIcsIncludesTheConfiguredEndDate(): void {
        $row = kc_calendar_normalize_event_input([
            'audience' => 'children',
            'event_type' => 'recurring',
            'title' => 'Cours unique inclusif',
            'days_of_week' => ['2'],
            'start_time' => '17:00',
            'end_time' => '18:00',
            'start_recur' => '2026-09-01',
            'end_recur' => '2026-09-01',
            'is_active' => '1',
        ]);

        $expanded = kc_calendar_expand_row_for_ics($row);
        $event = kc_calendar_row_to_fullcalendar(array_merge($row, ['id' => 41]));

        $this->assertSame(['2026-09-01T17:00'], array_column($expanded, 'start'));
        $this->assertSame('2026-09-02', $event['endRecur']);
    }

    public function testPayloadRoutesAllAudienceEventsToEveryCalendar(): void {
        $rows = [
            kc_calendar_single_row(1, 'children', 'Enfants seulement', '2026-09-10T17:00:00', '2026-09-10T18:00:00'),
            kc_calendar_single_row(2, 'teens', 'Ados seulement', '2026-09-10T18:00:00', '2026-09-10T19:00:00'),
            kc_calendar_single_row(3, 'adults', 'Adultes seulement', '2026-09-10T19:00:00', '2026-09-10T20:30:00'),
            kc_calendar_single_row(4, 'all', 'Tout le club', '2026-09-11T18:00:00', '2026-09-11T20:00:00'),
        ];

        $payload = kc_calendar_events_payload($rows);

        $childrenTitles = array_column($payload['ics']['children'], 'title');
        $teensTitles = array_column($payload['ics']['teens'], 'title');
        $adultsTitles = array_column($payload['ics']['adults'], 'title');

        $this->assertContains('Enfants seulement', $childrenTitles);
        $this->assertContains('Tout le club', $childrenTitles);
        $this->assertNotContains('Ados seulement', $childrenTitles);

        $this->assertContains('Ados seulement', $teensTitles);
        $this->assertContains('Tout le club', $teensTitles);
        $this->assertNotContains('Adultes seulement', $teensTitles);

        $this->assertContains('Adultes seulement', $adultsTitles);
        $this->assertContains('Tout le club', $adultsTitles);
        $this->assertNotContains('Enfants seulement', $adultsTitles);
    }

    public function testAdminPayloadCanIncludeInactiveEventsWithoutPublishingThem(): void {
        $active = kc_calendar_single_row(1, 'children', 'Actif', '2026-09-10T17:00:00', '2026-09-10T18:00:00');
        $inactive = array_merge(
            kc_calendar_single_row(2, 'children', 'Brouillon', '2026-09-11T17:00:00', '2026-09-11T18:00:00'),
            ['is_active' => 0]
        );

        $publicPayload = kc_calendar_events_payload([$active, $inactive]);
        $adminPayload = kc_calendar_events_payload([$active, $inactive], true);
        $counts = kc_calendar_admin_counts([$active, $inactive]);

        $this->assertSame(['Actif'], array_column($publicPayload['fullcalendar'], 'title'));
        $this->assertSame(['Actif', 'Brouillon'], array_column($adminPayload['fullcalendar'], 'title'));
        $this->assertSame(['kc-calendar-inactive'], $adminPayload['fullcalendar'][1]['classNames']);
        $this->assertSame(['Actif'], array_column($adminPayload['ics']['children'], 'title'));
        $this->assertSame(2, $counts['total']);
        $this->assertSame(1, $counts['active']);
        $this->assertSame(1, $counts['inactive']);
    }

    public function testAdminConflictsDetectSameAudienceAndAllAudienceOverlap(): void {
        $rows = [
            array_merge(kc_calendar_single_row(1, 'children', 'Cours enfants', '2026-09-10T17:00:00', '2026-09-10T18:00:00'), ['id' => 1]),
            array_merge(kc_calendar_single_row(2, 'all', 'Evenement club', '2026-09-10T17:30:00', '2026-09-10T19:00:00'), ['id' => 2]),
            array_merge(kc_calendar_single_row(3, 'teens', 'Cours ados', '2026-09-10T20:00:00', '2026-09-10T21:00:00'), ['id' => 3]),
            array_merge(kc_calendar_single_row(4, 'adults', 'Cours adultes', '2026-09-10T20:00:00', '2026-09-10T21:00:00'), ['id' => 4]),
            array_merge(kc_calendar_single_row(5, 'children', 'Brouillon chevauchant', '2026-09-10T17:15:00', '2026-09-10T17:45:00'), ['id' => 5, 'is_active' => 0]),
        ];

        $conflicts = kc_calendar_admin_conflicts($rows);

        $this->assertCount(1, $conflicts);
        $this->assertSame('children', $conflicts[0]['audience']);
        $this->assertSame('Cours enfants', $conflicts[0]['first_title']);
        $this->assertSame('Evenement club', $conflicts[0]['second_title']);
    }

    public function testCalendarInputRejectsInvalidValues(): void {
        $this->expectException(InvalidArgumentException::class);

        kc_calendar_normalize_event_input([
            'audience' => 'parents',
            'event_type' => 'single',
            'title' => 'Invalide',
            'start_at' => '2026-09-12T10:00',
            'end_at' => '2026-09-12T12:00',
        ]);
    }

    public function testRecurringEventRejectsPeriodWithoutSelectedDay(): void {
        $this->expectException(InvalidArgumentException::class);

        kc_calendar_normalize_event_input([
            'audience' => 'children',
            'event_type' => 'recurring',
            'title' => 'Recurrence vide',
            'days_of_week' => ['3'],
            'start_time' => '17:00',
            'end_time' => '18:00',
            'start_recur' => '2026-09-01',
            'end_recur' => '2026-09-01',
        ]);
    }

    public function testDefaultDraftImportCreatesInactiveModelRows(): void {
        $db = new FakeCalendarImportPdo();

        $imported = kc_calendar_import_default_drafts($db);

        $this->assertSame(count(kc_calendar_default_event_rows()), $imported);
        $this->assertCount($imported, $db->insertedRows);
        $this->assertStringContainsString('is_active, sort_order)', $db->preparedSql);
        $this->assertStringContainsString('0, :sort_order', $db->preparedSql);

        foreach ($db->insertedRows as $row) {
            $this->assertStringStartsWith('[modele] ', (string)$row[':title']);
            $this->assertGreaterThanOrEqual(1000, (int)$row[':sort_order']);
        }
    }
}

final class FakeCalendarImportPdo extends PDO {
    public string $preparedSql = '';

    /** @var list<array<string, mixed>> */
    public array $insertedRows = [];

    public function __construct() {}

    public function exec(string $statement): int|false {
        return 0;
    }

    public function prepare(string $query, array $options = []): PDOStatement|false {
        $this->preparedSql = $query;
        return new FakeCalendarImportStatement($this);
    }
}

final class FakeCalendarImportStatement extends PDOStatement {
    public function __construct(private FakeCalendarImportPdo $db) {}

    public function execute(?array $params = null): bool {
        $this->db->insertedRows[] = $params ?? [];
        return true;
    }
}
