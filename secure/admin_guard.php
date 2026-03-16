<?php
// admin_guard.php - validasi login admin tanpa DB
session_start();

define('ADMIN_SECRET_HASH', '$2y$12$R6MjyVVPMDwCIfxS8bBor.Ca4CBZ88qVRrsNAH5BQW4N0wqMQPaAe'); 

function admin_login($password){
    if(password_verify($password, ADMIN_SECRET_HASH)){
        $_SESSION['role'] = 'admin';
        $_SESSION['fingerprint'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
        session_regenerate_id(true);
        return true;
    }
    return false;
}

function admin_logout(){
    session_destroy();
}
