<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {



$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host,$user, $pass, $db);

if ($mysqli->connect_error) {
    header('Location: login.html?error=db');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    $mysqli->close();
    header('Location: login.html?error=empty');
    exit;
}

$stmt = $mysqli->prepare('SELECT password FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    $mysqli->close();
    header('Location: login.html?error=invalid');
    exit;
}

$stmt->bind_result($pass_db);
$stmt->fetch();

if ($password === $pass_db) {
    $_SESSION['user'] = $username;
    $_SESSION['logged_in'] = true;
    $redirect = ($username === 'admin') ? 'admin.php' : 'index.php';
    header('Location: ' . $redirect);
    exit;
} else {
    $stmt->close();
    $mysqli->close();
    header('Location: login.html?error=invalid');
    exit;
}
} else {
    header('Location: login.html');
    exit;
}