<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

header('Location: index.html?user=' . urlencode($_SESSION['user']));
exit;
