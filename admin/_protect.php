<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

$isAdminRole = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isAdminFlag = !empty($_SESSION['is_admin']);

if (!$isAdminRole && !$isAdminFlag) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
if (!isset($_SESSION['fingerprint']) || $_SESSION['fingerprint'] !== $fingerprint) {
    session_destroy();
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}
