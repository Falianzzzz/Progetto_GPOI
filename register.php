<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html');
    exit;
}

$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    header('Location: register.html?error=db');
    exit;
}


$username   = isset($_POST['username'])   ? trim($_POST['username'])   : '';
$email      = isset($_POST['email'])      ? trim($_POST['email'])      : '';
$password   = isset($_POST['password'])   ? $_POST['password']         : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name  = isset($_POST['last_name'])  ? trim($_POST['last_name'])  : '';

if ($username === '' || $email === '' || $password === '' || $first_name === '' || $last_name === '') {
    $mysqli->close();
    header('Location: register.html?error=missing');
    exit;
}

// Check for existing username
$stmt = $mysqli->prepare('SELECT user_id FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $mysqli->close();
    header('Location: register.html?error=username_taken');
    exit;
}
$stmt->close();

// Check for existing email
$stmt = $mysqli->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $mysqli->close();
    header('Location: register.html?error=email_taken');
    exit;
}
$stmt->close();

$stmt = $mysqli->prepare('INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $username, $email, $password, $first_name, $last_name);

if ($stmt->execute()) {
    $stmt->close();
    $mysqli->close();
    header('Location: login.html?registered=1');
    exit;
} else {
    $stmt->close();
    $mysqli->close();
    header('Location: register.html?error=db');
    exit;
}
