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
<html lang="en" data-bs-theme="light">
<head>
    <title>Google Calendar Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: var(--bs-light-bg-subtle);
            transition: background-color 0.3s ease;
        }
        .sidebar {
            background-color: var(--bs-body-bg);
            padding: 2rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        .fc-header-toolbar {
            padding: 1rem;
            background-color: var(--bs-body-bg);
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .fc {
            background-color: var(--bs-body-bg);
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 1rem;
        }
        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            display: none;
        }
        .theme-switch {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Notification Placeholder -->
    <div id="notification" class="alert"></div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="m-0">Google Calendar</h1>
            <div>
                <i class="bi bi-sun-fill fs-4 theme-switch" id="theme-toggle-icon"></i>
                <a href="logout.php" class="btn btn-outline-danger ms-3"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h4>Your Calendars</h4>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($calendars as $calendar): ?>
                            <li class="list-group-item">
                                <a href="?calendar_id=<?php echo urlencode($calendar->getId()); ?>" class="text-decoration-none d-flex align-items-center">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <?php echo htmlspecialchars($calendar->getSummary()); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <?php if (isset($_GET['calendar_id'])): ?>
                    <div class="alert alert-primary d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i>Click on a date or drag across multiple dates to create a new event.</div>
                    <div id='calendar'></div>
                <?php else: ?>
                    <div class="d-flex justify-content-center align-items-center h-100 rounded bg-body-tertiary">
                        <p class="text-muted">Please select a calendar from the list to get started.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createEventForm">
                        <input type="hidden" name="calendar_id" id="calendar_id" value="<?php echo htmlspecialchars($_GET['calendar_id'] ?? ''); ?>">
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEventBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Create Event</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <?php if (isset($_GET['calendar_id'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const createEventModal = new bootstrap.Modal(document.getElementById('createEventModal'));
        const createEventForm = document.getElementById('createEventForm');
        const saveEventBtn = document.getElementById('saveEventBtn');
        const calendarId = '<?php echo urlencode($_GET['calendar_id']); ?>';

        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          events: `get_events.php?calendar_id=${calendarId}`,
          selectable: true,
          select: function(info) {
            const startTime = new Date(info.startStr).toISOString().slice(0, 16);
            let endTime = info.end ? new Date(info.endStr) : new Date(info.startStr);
            if (!info.end) {
                endTime.setHours(endTime.getHours() + 1);
            }
            const endTimeStr = endTime.toISOString().slice(0, 16);

            createEventForm.reset();
            document.getElementById('start_time').value = startTime;
            document.getElementById('end_time').value = endTimeStr;
            createEventModal.show();
          },
          eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
              window.open(info.event.url, '_blank');
            }
          }
        });
        calendar.render();

        saveEventBtn.addEventListener('click', function() {
            const spinner = saveEventBtn.querySelector('.spinner-border');
            const buttonText = saveEventBtn.querySelector('.button-text');
            
            spinner.classList.remove('d-none');
            buttonText.textContent = 'Creating...';
            saveEventBtn.disabled = true;

            const formData = new FormData(createEventForm);
            
            fetch('create_event.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    createEventModal.hide();
                    calendar.refetchEvents();
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An unexpected error occurred.', 'danger');
            })
            .finally(() => {
                spinner.classList.add('d-none');
                buttonText.textContent = 'Create Event';
                saveEventBtn.disabled = false;
            });
        });

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.className = `alert alert-${type}`;
            notification.textContent = message;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Theme switcher logic
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const htmlEl = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlEl.setAttribute('data-bs-theme', savedTheme);
        themeToggleIcon.className = savedTheme === 'dark' ? 'bi bi-moon-fill fs-4 theme-switch' : 'bi bi-sun-fill fs-4 theme-switch';


        themeToggleIcon.addEventListener('click', () => {
            const currentTheme = htmlEl.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlEl.setAttribute('data-bs-theme', newTheme);
            themeToggleIcon.className = newTheme === 'dark' ? 'bi bi-moon-fill fs-4 theme-switch' : 'bi bi-sun-fill fs-4 theme-switch';
            localStorage.setItem('theme', newTheme);
        });
      });
    </script>
    <?php endif; ?>
</body>
</html>
