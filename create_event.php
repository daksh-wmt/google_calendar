<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

try {
    $client = getClient();

    if (!$client->getAccessToken()) {
        throw new Exception('User not authenticated.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    if (empty($_POST['calendar_id']) || empty($_POST['summary']) || empty($_POST['start_time']) || empty($_POST['end_time'])) {
        throw new Exception('Missing required fields.');
    }

    $calendarId = $_POST['calendar_id'];
    $summary = $_POST['summary'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event([
        'summary' => $summary,
        'start' => ['dateTime' => (new DateTime($startTime))->format(DateTime::RFC3339)],
        'end' => ['dateTime' => (new DateTime($endTime))->format(DateTime::RFC3339)],
    ]);

    $service->events->insert($calendarId, $event);

    $response['status'] = 'success';
    $response['message'] = 'Event created successfully!';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
