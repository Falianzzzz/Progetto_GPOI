<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['has_subscription' => false]);
    exit;
}

$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    echo json_encode(['has_subscription' => false]);
    exit;
}

$username = $_SESSION['user'];

$stmt = $mysqli->prepare('SELECT user_id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['has_subscription' => false]);
    $stmt->close();
    $mysqli->close();
    exit;
}
$user = $result->fetch_assoc();
$stmt->close();

$stmt = $mysqli->prepare("
    SELECT COUNT(*) as cnt 
    FROM user_subscriptions 
    WHERE user_id = ? AND status = 'active' AND end_date >= CURDATE()
");
$stmt->bind_param('i', $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['has_subscription' => $row['cnt'] > 0]);

$stmt->close();
$mysqli->close();
