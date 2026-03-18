<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'], $_SESSION['fingerprint'])){
    header('Location: ../public/login.php');
    exit;
}

$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
if($_SESSION['fingerprint'] !== $fingerprint){
    session_destroy();
    header('Location: ../public/login.php');
    exit;
}

