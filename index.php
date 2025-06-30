<?php
require_once __DIR__ . '/config.php';

$client = getClient();

if ($client->getAccessToken()) {
    header('Location: dashboard.php');
    exit;
}

$authUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Calendar Integration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            text-align: center;
            padding: 40px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="mb-4">Welcome to Google Calendar Integration</h1>
        <p class="mb-4">Please log in with your Google Account to manage your calendars.</p>
        <a href="<?php echo $authUrl; ?>" class="btn btn-primary btn-lg">Login with Google</a>
    </div>
</body>
</html>