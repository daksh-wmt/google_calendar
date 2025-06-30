<?php
require_once __DIR__ . '/config.php';

$client = getClient();

if (!$client->getAccessToken()) {
    header('Location: index.php');
    exit;
}

$service = new Google_Service_Calendar($client);

// Fetch calendars
$calendarList = $service->calendarList->listCalendarList();
$calendars = $calendarList->getItems();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Calendar Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Google Calendar Dashboard</h1>
        <a href="logout.php" class="btn btn-danger mb-4">Logout</a>

        <div class="row">
            <div class="col-md-4">
                <h2>Your Calendars</h2>
                <ul class="list-group">
                    <?php foreach ($calendars as $calendar): ?>
                        <li class="list-group-item">
                            <a href="?calendar_id=<?php echo urlencode($calendar->getId()); ?>">
                                <?php echo htmlspecialchars($calendar->getSummary()); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (isset($_GET['calendar_id'])): ?>
                    <hr class="my-4">
                    <h3>Create New Event</h3>
                    <form action="create_event.php" method="post">
                        <input type="hidden" name="calendar_id" value="<?php echo htmlspecialchars($_GET['calendar_id']); ?>">
                        <div class="mb-3">
                            <label for="summary" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="summary" name="summary" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <?php if (isset($_GET['calendar_id'])): ?>
                    <div id='calendar'></div>
                <?php else: ?>
                    <p class="mt-4">Please select a calendar from the list to view its events.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['calendar_id'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: 'get_events.php?calendar_id=<?php echo urlencode($_GET['calendar_id']); ?>',
          eventClick: function(info) {
            info.jsEvent.preventDefault(); // don't let the browser navigate
            if (info.event.url) {
              window.open(info.event.url);
            }
          }
        });
        calendar.render();
      });
    </script>
    <?php endif; ?>
</body>
</html>
