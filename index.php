<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

// Servi il contenuto di index.html direttamente (non redirect)
readfile('index.html');
exit;

