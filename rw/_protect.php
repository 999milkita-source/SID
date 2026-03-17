<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'], $_SESSION['fingerprint']) || ($_SESSION['role'] ?? '') !== 'rw') {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$fingerprint = hash(
    'sha256',
    $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']
);

if ($_SESSION['fingerprint'] !== $fingerprint) {
    session_destroy();
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}
