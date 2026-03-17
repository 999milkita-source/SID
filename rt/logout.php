<?php
session_start();
require_once '../config/config.php';

/* Hapus semua data session */
$_SESSION = [];

/* Hapus cookie session (penting!) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Hancurkan session */
session_destroy();

/* Redirect ke login */
header('Location: ' . BASE_URL . 'public/login.php');
exit;
