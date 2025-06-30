<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$client = getClient();

if (!$client->getAccessToken() || !isset($_GET['calendar_id'])) {
    echo json_encode([]);
    exit;
}

$service = new Google_Service_Calendar($client);
$calendarId = urldecode($_GET['calendar_id']);

$optParams = [
    'maxResults' => 250,
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => date('c', strtotime('-6 months')),
    'timeMax' => date('c', strtotime('+6 months')),
];
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

$output = [];
foreach ($events as $event) {
    $start = $event->start->dateTime ?? $event->start->date;
    $end = $event->end->dateTime ?? $event->end->date;

    $output[] = [
        'title' => $event->getSummary(),
        'start' => $start,
        'end' => $end,
        'url' => $event->getHtmlLink(),
    ];
}

echo json_encode($output);
