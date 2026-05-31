<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autenticato']);
    exit;
}

$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore connessione database']);
    exit;
}


$username = $_SESSION['user'];
$plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;

if ($plan_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Piano non valido']);
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare('SELECT user_id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Utente non trovato']);
    $stmt->close();
    $mysqli->close();
    exit;
}
$user = $result->fetch_assoc();
$user_id = $user['user_id'];
$stmt->close();

$stmt = $mysqli->prepare("
    SELECT COUNT(*) as cnt 
    FROM user_subscriptions 
    WHERE user_id = ? AND status = 'active' AND end_date >= CURDATE()
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['cnt'] > 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Hai già un abbonamento attivo']);
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare('SELECT duration_months FROM membership_plans WHERE plan_id = ? LIMIT 1');
$stmt->bind_param('i', $plan_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Piano non trovato']);
    $stmt->close();
    $mysqli->close();
    exit;
}
$plan = $result->fetch_assoc();
$stmt->close();

$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime("+{$plan['duration_months']} months"));

$stmt = $mysqli->prepare('
    INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status)
    VALUES (?, ?, ?, ?, "active")
');
$stmt->bind_param('iiss', $user_id, $plan_id, $start_date, $end_date);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Abbonamento attivato con successo!',
        'subscription_id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Errore durante l\'attivazione dell\'abbonamento']);
}

$stmt->close();
$mysqli->close();
