<?php
if(!isset($_SESSION['user_id'], $_SESSION['fingerprint'])){
    header('Location: ../public/login.php');
    exit;
}

$fingerprint = hash(
    'sha256',
    $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']
);

if($_SESSION['fingerprint'] !== $fingerprint){
    session_destroy();
    header('Location: ../public/login.php');
    exit;
}
