<?php
require_once __DIR__ . '/config.php';

$client = getClient();

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $_SESSION['access_token'] = $token;
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error during token exchange.";
        print_r($token);
    }
} else {
    echo "No code received.";
}
