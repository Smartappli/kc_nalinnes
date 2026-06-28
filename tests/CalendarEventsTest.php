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

        $expanded = kc_calendar_expand_row_for_ics($row);

        $this->assertSame(
            ['2026-09-04T18:00', '2026-09-07T18:00'],
            array_column($expanded, 'start')
        );
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
}
