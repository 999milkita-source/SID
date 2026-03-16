<?php
// _protect.php - Middleware proteksi admin
session_start();

// Logout jika session tidak valid
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header('Location: /public/login.php');
    exit;
}

// Cek fingerprint (User-Agent + IP)
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
if(!isset($_SESSION['fingerprint']) || $_SESSION['fingerprint'] !== $fingerprint){
    session_destroy();
    header('Location: /public/login.php');
    exit;
}

// Regenerate session id setiap akses halaman
session_regenerate_id(true);
