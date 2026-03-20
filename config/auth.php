<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/sidwolokota');
}

function get_login_url(): string {
    return rtrim(BASE_URL, '/') . '/public/login.php';
}

function get_role_home_url(?string $role = null): string {
    $baseUrl = rtrim(BASE_URL, '/');
    $role = $role ?? ($_SESSION['role'] ?? null);

    $roleHomes = [
        'admin' => $baseUrl . '/admin/index.php',
        'penduduk' => $baseUrl . '/penduduk/index.php',
        'rt' => $baseUrl . '/rt/index.php',
        'rw' => $baseUrl . '/rw/index.php',
        'kades' => $baseUrl . '/kades/index.php',
    ];

    return $roleHomes[$role] ?? get_login_url();
}

function redirect_to_login(): void {
    if (!headers_sent()) {
        header('Location: ' . get_login_url());
    }
    exit;
}

function redirect_to_role_home(?string $role = null): void {
    if (!headers_sent()) {
        header('Location: ' . get_role_home_url($role));
    }
    exit;
}

function ensure_session_started(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function generate_csrf_token(): string {
    ensure_session_started();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    ensure_session_started();
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function set_security_headers(): void {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header("Referrer-Policy: no-referrer-when-downgrade");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    }
}

function check_login(): bool {
    set_security_headers();
    ensure_session_started();

    if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['fingerprint'])) {
        redirect_to_login();
    }

    $fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
    if (!hash_equals($_SESSION['fingerprint'], $fingerprint)) {
        session_unset();
        session_destroy();
        redirect_to_login();
    }

    $validRoles = ['admin', 'penduduk', 'rt', 'rw', 'kades'];
    if (!in_array($_SESSION['role'], $validRoles, true)) {
        session_unset();
        session_destroy();
        redirect_to_login();
    }

    return true;
}

function require_role(string $role): void {
    check_login();
    if (!isset($_SESSION['role'])) {
        redirect_to_login();
    }

    if ($_SESSION['role'] !== $role) {
        redirect_to_role_home($_SESSION['role']);
    }
}

function require_roles(array $roles): void {
    check_login();
    if (!isset($_SESSION['role'])) {
        redirect_to_login();
    }

    if (!in_array($_SESSION['role'], $roles, true)) {
        redirect_to_role_home($_SESSION['role']);
    }
}

function logout(): void {
    ensure_session_started();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'] ?? '/',
            $params['domain'] ?? '',
            isset($params['secure']) ? (bool)$params['secure'] : false,
            isset($params['httponly']) ? (bool)$params['httponly'] : true
        );
    }

    $_SESSION = [];
    session_unset();
    session_destroy();

    if (!headers_sent()) {
        header('Location: ' . get_login_url());
    }
    exit;
}
