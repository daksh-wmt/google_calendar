<?php
require_once __DIR__ . '/config.php';

$client = getClient();

if (!$client->getAccessToken()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $calendarId = $_POST['calendar_id'];
    $summary = $_POST['summary'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event([
        'summary' => $summary,
        'start' => [
            'dateTime' => (new DateTime($startTime))->format(DateTime::RFC3339),
        ],
        'end' => [
            'dateTime' => (new DateTime($endTime))->format(DateTime::RFC3339),
        ],
    ]);

    $service->events->insert($calendarId, $event);

    header('Location: dashboard.php?calendar_id=' . urlencode($calendarId));
    exit;
}
