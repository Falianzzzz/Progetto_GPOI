<?php
session_start();
header('Content-Type: application/json');

// ─── VERIFICA SESSIONE ───
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

// ─── RECUPERA DATI UTENTE ───
$stmt = $mysqli->prepare('
    SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, u.created_at
    FROM users u
    WHERE u.username = ? 
    LIMIT 1
');
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

$user_data = $result->fetch_assoc();
$user_id = $user_data['user_id'];
$stmt->close();

// ─── RECUPERA ABBONAMENTO ATTIVO ───
$stmt = $mysqli->prepare('
    SELECT 
        us.subscription_id,
        us.start_date,
        us.end_date,
        us.status,
        mp.name,
        mp.duration_months,
        mp.price_per_month,
        mp.has_pt_session,
        mp.pt_sessions_count,
        mp.has_nutritional_plan
    FROM user_subscriptions us
    JOIN membership_plans mp ON us.plan_id = mp.plan_id
    WHERE us.user_id = ? AND us.status = "active"
    ORDER BY us.start_date DESC
    LIMIT 1
');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subscription_data = null;
if ($result->num_rows > 0) {
    $subscription_data = $result->fetch_assoc();
}
$stmt->close();

// ─── PREPARA RISPOSTA JSON ───
$response = [
    'user' => $user_data,
    'subscription' => $subscription_data,
    'message' => 'Dati account caricati con successo'
];

echo json_encode($response);

$mysqli->close();
exit;
