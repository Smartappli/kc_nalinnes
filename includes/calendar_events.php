<?php
declare(strict_types=1);

function kc_calendar_audiences(): array {
    return [
        'children' => 'Enfants',
        'teens' => 'Ados',
        'adults' => 'Adultes',
        'all' => 'Tous les groupes',
    ];
}

function kc_calendar_event_types(): array {
    return [
        'single' => 'Ponctuel',
        'recurring' => 'Repete',
    ];
}

function kc_calendar_default_color(string $audience): string {
    return match ($audience) {
        'children' => '#3b82f6',
        'teens' => '#f97316',
        'adults' => '#22c55e',
        default => '#b91c1c',
    };
}

function kc_calendar_text(string $key, string $fallback): string {
    return function_exists('kc_t') ? kc_t($key) : $fallback;
}

function kc_calendar_recurring_row(
    int $sortOrder,
    string $audience,
    string $title,
    array $daysOfWeek,
    string $startTime,
    string $endTime,
    string $startRecur,
    string $endRecur,
    ?string $description = null,
    ?string $color = null
): array {
    return [
        'id' => 'default-' . $sortOrder,
        'audience' => $audience,
        'event_type' => 'recurring',
        'title' => $title,
        'description' => $description ?? '',
        'color' => $color ?? kc_calendar_default_color($audience),
        'start_at' => null,
        'end_at' => null,
        'days_of_week' => $daysOfWeek,
        'start_time' => $startTime,
        'end_time' => $endTime,
        'start_recur' => $startRecur,
        'end_recur' => $endRecur,
        'is_active' => 1,
        'sort_order' => $sortOrder,
    ];
}

function kc_calendar_single_row(
    int $sortOrder,
    string $audience,
    string $title,
    string $start,
    string $end,
    ?string $description = null,
    ?string $color = null
): array {
    return [
        'id' => 'default-' . $sortOrder,
        'audience' => $audience,
        'event_type' => 'single',
        'title' => $title,
        'description' => $description ?? '',
        'color' => $color ?? kc_calendar_default_color($audience),
        'start_at' => str_replace('T', ' ', $start),
        'end_at' => str_replace('T', ' ', $end),
        'days_of_week' => [],
        'start_time' => null,
        'end_time' => null,
        'start_recur' => null,
        'end_recur' => null,
        'is_active' => 1,
        'sort_order' => $sortOrder,
    ];
}

function kc_calendar_default_event_rows(): array {
    $children = kc_calendar_text('home.calendar.event.children', 'Cours enfants');
    $teens = kc_calendar_text('home.calendar.event.teens', 'Cours ados');
    $adults = kc_calendar_text('home.calendar.event.adults', 'Cours adultes');
    $saintNicholas = kc_calendar_text('home.calendar.event.saint_nicholas', 'Saint Nicolas');
    $grading = kc_calendar_text('home.calendar.event.grading', 'Passage de grade');
    $teensAdultsOctober = kc_calendar_text('home.calendar.event.teens_adults_october', 'Cours ados/adultes');
    $teensAdultsFebruary = kc_calendar_text('home.calendar.event.teens_adults_february', 'Cours ados/adultes');
    $teensAdultsEaster = kc_calendar_text('home.calendar.event.teens_adults_easter', 'Cours ados/adultes');

    $rows = [];
    $order = 10;

    foreach ([
        ['2025-09-01', '2025-10-19'],
        ['2025-11-03', '2025-12-20'],
        ['2026-01-05', '2026-02-15'],
        ['2026-03-02', '2026-04-24'],
        ['2026-05-11', '2026-06-26'],
    ] as [$start, $end]) {
        $rows[] = kc_calendar_recurring_row($order++, 'children', $children, [1], '17:00', '18:00', $start, $end);
    }

    foreach ([
        ['2025-09-01', '2025-10-19'],
        ['2025-11-03', '2025-12-22'],
        ['2026-01-05', '2026-01-29'],
        ['2026-01-31', '2026-02-15'],
        ['2026-03-02', '2026-04-24'],
        ['2026-05-11', '2026-06-26'],
    ] as [$start, $end]) {
        $rows[] = kc_calendar_recurring_row($order++, 'teens', $teens, [1, 5], '18:00', '19:00', $start, $end);
        $rows[] = kc_calendar_recurring_row($order++, 'adults', $adults, [1, 5], '19:00', '20:30', $start, $end);
    }

    $rows[] = kc_calendar_single_row($order++, 'all', $saintNicholas, '2025-12-01T17:00:00', '2025-12-01T19:00:00', 'Visite de Saint Nicolas au dojo KC Nalinnes.');
    $rows[] = kc_calendar_single_row($order++, 'all', $grading, '2026-01-30T18:00:00', '2026-01-30T20:00:00', 'Passage de grade - tous niveaux.');
    $rows[] = kc_calendar_single_row($order++, 'all', $grading, '2026-06-26T18:00:00', '2026-06-26T20:00:00', 'Passage de grade - tous niveaux.');

    foreach ([
        [$teensAdultsOctober, '2025-10-27T18:00:00', '2025-10-27T20:00:00'],
        [$teensAdultsOctober, '2025-10-31T18:00:00', '2025-10-31T20:00:00'],
        [$teensAdultsFebruary, '2026-02-16T18:00:00', '2026-02-16T20:00:00'],
        [$teensAdultsFebruary, '2026-02-20T18:00:00', '2026-02-20T20:00:00'],
        [$teensAdultsFebruary, '2026-02-23T18:00:00', '2026-02-23T20:00:00'],
        [$teensAdultsFebruary, '2026-02-27T18:00:00', '2026-02-27T20:00:00'],
        [$teensAdultsEaster, '2026-05-04T18:00:00', '2026-05-04T20:00:00'],
        [$teensAdultsEaster, '2026-05-08T18:00:00', '2026-05-08T20:00:00'],
    ] as [$title, $start, $end]) {
        $rows[] = kc_calendar_single_row($order++, 'teens', $title, $start, $end);
        $rows[] = kc_calendar_single_row($order++, 'adults', $title, $start, $end);
    }

    return $rows;
}

function ensure_calendar_events_table(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS calendar_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        audience VARCHAR(20) NOT NULL,
        event_type VARCHAR(20) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        color VARCHAR(20) NOT NULL DEFAULT \'#0ea5e9\',
        start_at DATETIME NULL,
        end_at DATETIME NULL,
        days_of_week VARCHAR(32) NULL,
        start_time TIME NULL,
        end_time TIME NULL,
        start_recur DATE NULL,
        end_recur DATE NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');
}

function seed_default_calendar_events_if_empty(PDO $db): void {
    ensure_calendar_events_table($db);

    $count = (int)($db->query('SELECT COUNT(*) FROM calendar_events')->fetchColumn() ?: 0);
    if ($count > 0) {
        return;
    }

    $stmt = $db->prepare('INSERT INTO calendar_events
        (audience, event_type, title, description, color, start_at, end_at, days_of_week, start_time, end_time, start_recur, end_recur, is_active, sort_order)
        VALUES
        (:audience, :event_type, :title, :description, :color, :start_at, :end_at, :days_of_week, :start_time, :end_time, :start_recur, :end_recur, :is_active, :sort_order)');

    foreach (kc_calendar_default_event_rows() as $row) {
        $stmt->execute([
            ':audience' => (string)$row['audience'],
            ':event_type' => (string)$row['event_type'],
            ':title' => (string)$row['title'],
            ':description' => (string)$row['description'],
            ':color' => (string)$row['color'],
            ':start_at' => $row['start_at'],
            ':end_at' => $row['end_at'],
            ':days_of_week' => kc_calendar_days_to_storage($row['days_of_week']),
            ':start_time' => $row['start_time'],
            ':end_time' => $row['end_time'],
            ':start_recur' => $row['start_recur'],
            ':end_recur' => $row['end_recur'],
            ':is_active' => (int)$row['is_active'],
            ':sort_order' => (int)$row['sort_order'],
        ]);
    }
}

function kc_calendar_admin_event_rows(PDO $db): array {
    seed_default_calendar_events_if_empty($db);

    $stmt = $db->query('SELECT * FROM calendar_events ORDER BY sort_order ASC, start_at ASC, start_recur ASC, id ASC');
    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

function kc_calendar_public_event_rows(?PDO $db): array {
    if ($db === null) {
        return kc_calendar_default_event_rows();
    }

    try {
        ensure_calendar_events_table($db);
        $stmt = $db->query('SELECT * FROM calendar_events WHERE is_active = 1 ORDER BY sort_order ASC, start_at ASC, start_recur ASC, id ASC');
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        return $rows !== [] ? $rows : kc_calendar_default_event_rows();
    }
    catch (Throwable $e) {
        error_log('Calendar public load failed: ' . $e->getMessage());
        return kc_calendar_default_event_rows();
    }
}

function kc_calendar_days_from_value(mixed $value): array {
    if (is_array($value)) {
        $raw = $value;
    }
    elseif (is_string($value) && trim($value) !== '') {
        $decoded = json_decode($value, true);
        $raw = is_array($decoded) ? $decoded : preg_split('/\s*,\s*/', trim($value));
    }
    else {
        $raw = [];
    }

    $days = [];
    foreach ($raw as $day) {
        $intDay = (int)$day;
        if ($intDay >= 0 && $intDay <= 6) {
            $days[] = $intDay;
        }
    }

    $days = array_values(array_unique($days));
    sort($days);
    return $days;
}

function kc_calendar_days_to_storage(mixed $value): string {
    return json_encode(kc_calendar_days_from_value($value), JSON_THROW_ON_ERROR);
}

function kc_calendar_normalize_audience(string $audience): string {
    $audience = trim($audience);
    if (!array_key_exists($audience, kc_calendar_audiences())) {
        throw new InvalidArgumentException('Groupe calendrier invalide.');
    }

    return $audience;
}

function kc_calendar_normalize_event_type(string $eventType): string {
    $eventType = trim($eventType);
    if (!array_key_exists($eventType, kc_calendar_event_types())) {
        throw new InvalidArgumentException('Type d evenement invalide.');
    }

    return $eventType;
}

function kc_calendar_normalize_color(string $color, string $audience): string {
    $color = trim($color);
    if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        return strtolower($color);
    }

    return kc_calendar_default_color($audience);
}

function kc_calendar_normalize_datetime_local(?string $value): ?string {
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }

    try {
        return (new DateTimeImmutable(str_replace('T', ' ', $value)))->format('Y-m-d H:i:s');
    }
    catch (Throwable $e) {
        throw new InvalidArgumentException('Date/heure invalide.');
    }
}

function kc_calendar_normalize_date(?string $value): ?string {
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) {
        throw new InvalidArgumentException('Date de recurrence invalide.');
    }

    return $value;
}

function kc_calendar_normalize_time(?string $value): ?string {
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }

    if (!preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $value)) {
        throw new InvalidArgumentException('Heure invalide.');
    }

    return strlen($value) === 5 ? $value . ':00' : $value;
}

function kc_calendar_normalize_event_input(array $input): array {
    $audience = kc_calendar_normalize_audience((string)($input['audience'] ?? 'children'));
    $eventType = kc_calendar_normalize_event_type((string)($input['event_type'] ?? 'single'));
    $title = trim((string)($input['title'] ?? ''));

    if ($title === '' || strlen($title) > 255) {
        throw new InvalidArgumentException('Titre calendrier invalide.');
    }

    $row = [
        'audience' => $audience,
        'event_type' => $eventType,
        'title' => $title,
        'description' => trim((string)($input['description'] ?? '')),
        'color' => kc_calendar_normalize_color((string)($input['color'] ?? ''), $audience),
        'start_at' => null,
        'end_at' => null,
        'days_of_week' => [],
        'start_time' => null,
        'end_time' => null,
        'start_recur' => null,
        'end_recur' => null,
        'is_active' => !empty($input['is_active']) ? 1 : 0,
        'sort_order' => max(0, (int)($input['sort_order'] ?? 0)),
    ];

    if ($eventType === 'single') {
        $row['start_at'] = kc_calendar_normalize_datetime_local((string)($input['start_at'] ?? ''));
        $row['end_at'] = kc_calendar_normalize_datetime_local((string)($input['end_at'] ?? ''));

        if ($row['start_at'] === null || $row['end_at'] === null) {
            throw new InvalidArgumentException('Debut et fin requis pour un evenement ponctuel.');
        }

        if (strtotime($row['end_at']) <= strtotime($row['start_at'])) {
            throw new InvalidArgumentException('La fin doit etre posterieure au debut.');
        }

        return $row;
    }

    $row['days_of_week'] = kc_calendar_days_from_value($input['days_of_week'] ?? []);
    $row['start_time'] = kc_calendar_normalize_time((string)($input['start_time'] ?? ''));
    $row['end_time'] = kc_calendar_normalize_time((string)($input['end_time'] ?? ''));
    $row['start_recur'] = kc_calendar_normalize_date((string)($input['start_recur'] ?? ''));
    $row['end_recur'] = kc_calendar_normalize_date((string)($input['end_recur'] ?? ''));

    if ($row['days_of_week'] === [] || $row['start_time'] === null || $row['end_time'] === null || $row['start_recur'] === null || $row['end_recur'] === null) {
        throw new InvalidArgumentException('Jours, heures et periode requis pour un evenement repete.');
    }

    if ($row['end_time'] <= $row['start_time']) {
        throw new InvalidArgumentException('L heure de fin doit etre posterieure au debut.');
    }

    if (strtotime($row['end_recur']) <= strtotime($row['start_recur'])) {
        throw new InvalidArgumentException('La fin de recurrence doit etre posterieure au debut.');
    }

    return $row;
}

function kc_calendar_save_event(PDO $db, array $input): int {
    ensure_calendar_events_table($db);
    $row = kc_calendar_normalize_event_input($input);
    $id = max(0, (int)($input['event_id'] ?? 0));
    $params = [
        ':audience' => $row['audience'],
        ':event_type' => $row['event_type'],
        ':title' => $row['title'],
        ':description' => $row['description'],
        ':color' => $row['color'],
        ':start_at' => $row['start_at'],
        ':end_at' => $row['end_at'],
        ':days_of_week' => kc_calendar_days_to_storage($row['days_of_week']),
        ':start_time' => $row['start_time'],
        ':end_time' => $row['end_time'],
        ':start_recur' => $row['start_recur'],
        ':end_recur' => $row['end_recur'],
        ':is_active' => $row['is_active'],
        ':sort_order' => $row['sort_order'],
    ];

    if ($id > 0) {
        $params[':id'] = $id;
        $stmt = $db->prepare('UPDATE calendar_events SET
            audience = :audience,
            event_type = :event_type,
            title = :title,
            description = :description,
            color = :color,
            start_at = :start_at,
            end_at = :end_at,
            days_of_week = :days_of_week,
            start_time = :start_time,
            end_time = :end_time,
            start_recur = :start_recur,
            end_recur = :end_recur,
            is_active = :is_active,
            sort_order = :sort_order
            WHERE id = :id');
        $stmt->execute($params);
        return $id;
    }

    $stmt = $db->prepare('INSERT INTO calendar_events
        (audience, event_type, title, description, color, start_at, end_at, days_of_week, start_time, end_time, start_recur, end_recur, is_active, sort_order)
        VALUES
        (:audience, :event_type, :title, :description, :color, :start_at, :end_at, :days_of_week, :start_time, :end_time, :start_recur, :end_recur, :is_active, :sort_order)');
    $stmt->execute($params);

    return (int)$db->lastInsertId();
}

function kc_calendar_delete_event(PDO $db, int $id): void {
    ensure_calendar_events_table($db);

    if ($id <= 0) {
        throw new InvalidArgumentException('Evenement calendrier invalide.');
    }

    $stmt = $db->prepare('DELETE FROM calendar_events WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

function kc_calendar_datetime_for_json(?string $value): ?string {
    if ($value === null || trim($value) === '') {
        return null;
    }

    return str_replace(' ', 'T', substr($value, 0, 19));
}

function kc_calendar_time_for_json(?string $value): ?string {
    if ($value === null || trim($value) === '') {
        return null;
    }

    return substr($value, 0, 5);
}

function kc_calendar_row_to_fullcalendar(array $row): array {
    $audience = kc_calendar_normalize_audience((string)($row['audience'] ?? 'children'));
    $eventType = kc_calendar_normalize_event_type((string)($row['event_type'] ?? 'single'));
    $daysOfWeek = kc_calendar_days_from_value($row['days_of_week'] ?? []);

    $event = [
        'id' => (string)($row['id'] ?? ('event-' . ($row['sort_order'] ?? '0'))),
        'title' => (string)($row['title'] ?? ''),
        'color' => kc_calendar_normalize_color((string)($row['color'] ?? ''), $audience),
        'extendedProps' => [
            'audience' => $audience,
            'eventType' => $eventType,
            'description' => (string)($row['description'] ?? ''),
            'daysOfWeek' => $daysOfWeek,
            'startTime' => kc_calendar_time_for_json($row['start_time'] ?? null),
            'endTime' => kc_calendar_time_for_json($row['end_time'] ?? null),
            'startRecur' => $row['start_recur'] ?? null,
            'endRecur' => $row['end_recur'] ?? null,
            'sortOrder' => (int)($row['sort_order'] ?? 0),
            'isActive' => (int)($row['is_active'] ?? 1) === 1,
        ],
    ];

    if ($eventType === 'recurring') {
        $event['daysOfWeek'] = $daysOfWeek;
        $event['startTime'] = kc_calendar_time_for_json($row['start_time'] ?? null);
        $event['endTime'] = kc_calendar_time_for_json($row['end_time'] ?? null);
        $event['startRecur'] = (string)($row['start_recur'] ?? '');
        $event['endRecur'] = (string)($row['end_recur'] ?? '');
        return $event;
    }

    $event['start'] = kc_calendar_datetime_for_json($row['start_at'] ?? null);
    $event['end'] = kc_calendar_datetime_for_json($row['end_at'] ?? null);
    return $event;
}

function kc_calendar_expand_row_for_ics(array $row): array {
    $eventType = (string)($row['event_type'] ?? 'single');
    $title = (string)($row['title'] ?? '');
    $description = (string)($row['description'] ?? '');

    if ($eventType === 'single') {
        $start = kc_calendar_datetime_for_json($row['start_at'] ?? null);
        $end = kc_calendar_datetime_for_json($row['end_at'] ?? null);

        return ($start !== null && $end !== null) ? [[
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'description' => $description,
        ]] : [];
    }

    $days = kc_calendar_days_from_value($row['days_of_week'] ?? []);
    $startRecur = kc_calendar_normalize_date((string)($row['start_recur'] ?? ''));
    $endRecur = kc_calendar_normalize_date((string)($row['end_recur'] ?? ''));
    $startTime = kc_calendar_time_for_json($row['start_time'] ?? null);
    $endTime = kc_calendar_time_for_json($row['end_time'] ?? null);

    if ($days === [] || $startRecur === null || $endRecur === null || $startTime === null || $endTime === null) {
        return [];
    }

    $events = [];
    $cursor = new DateTimeImmutable($startRecur);
    $endDate = new DateTimeImmutable($endRecur);
    $daySet = array_flip($days);

    while ($cursor < $endDate) {
        if (isset($daySet[(int)$cursor->format('w')])) {
            $date = $cursor->format('Y-m-d');
            $events[] = [
                'title' => $title,
                'start' => $date . 'T' . $startTime,
                'end' => $date . 'T' . $endTime,
                'description' => $description,
            ];
        }

        $cursor = $cursor->modify('+1 day');
    }

    return $events;
}

function kc_calendar_events_payload(array $rows): array {
    $fullcalendar = [];
    $ics = [
        'children' => [],
        'teens' => [],
        'adults' => [],
        'club' => [],
    ];

    foreach ($rows as $row) {
        if ((int)($row['is_active'] ?? 1) !== 1) {
            continue;
        }

        $audience = (string)($row['audience'] ?? 'children');
        $fullcalendar[] = kc_calendar_row_to_fullcalendar($row);
        $expanded = kc_calendar_expand_row_for_ics($row);
        $targets = $audience === 'all' ? ['children', 'teens', 'adults'] : [$audience];

        foreach ($targets as $target) {
            if (isset($ics[$target])) {
                array_push($ics[$target], ...$expanded);
            }
        }

        array_push($ics['club'], ...$expanded);
    }

    foreach ($ics as $key => $events) {
        usort($events, static fn(array $a, array $b): int => strcmp((string)$a['start'], (string)$b['start']));
        $ics[$key] = kc_calendar_dedupe_ics_events($events);
    }

    return [
        'fullcalendar' => $fullcalendar,
        'ics' => $ics,
    ];
}

function kc_calendar_dedupe_ics_events(array $events): array {
    $seen = [];
    $out = [];

    foreach ($events as $event) {
        $key = (string)$event['title'] . '|' . (string)$event['start'] . '|' . (string)$event['end'];
        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $out[] = $event;
    }

    return $out;
}
