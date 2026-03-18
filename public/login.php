<?php
require_once '../config/config.php';
require_once '../config/auth.php';

ensure_session_started();
set_security_headers();

$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = 'Token CSRF tidak valid.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } elseif ($password === '') {
        $error = 'Password tidak boleh kosong.';
    } else {
        $stmt = $pdo->prepare('SELECT id, email, password, role, nama_lengkap, rt, rw FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['rt'] = $user['rt'] ?? null;
            $_SESSION['rw'] = $user['rw'] ?? null;
            $_SESSION['fingerprint'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');

            switch ($user['role']) {
                case 'admin': header('Location: ../admin/index.php'); break;
                case 'penduduk': header('Location: ../penduduk/index.php'); break;
                case 'rt': header('Location: ../rt/index.php'); break;
                case 'rw': header('Location: ../rw/index.php'); break;
                case 'kades': header('Location: ../kades/index.php'); break;
                default:
                    session_unset();
                    session_destroy();
                    $error = 'Role pengguna tidak valid.';
                    break;
            }
            exit;
        }

        $error = 'Email atau password salah!';
    }
} else {
    // Selalu tampilkan form login untuk login user lain. Login akan menimpa session lama.
    // Tidak auto-redirect untuk tetap bisa ganti akun.
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login SID Wolokota</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
<div class="publiclogin">
    <div class="formlogin">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="email" name="email" placeholder="Email" required>
            <div class="password-field">
                <input id="password" type="password" name="password" placeholder="Password" required>
                <button type="button" class="toggle-password" aria-label="Tampilkan password">
                    <i data-feather="eye"></i>
                </button>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p style="margin-top:12px; font-size:0.9rem; color:#666;">Sedang login sebagai <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>. Isi lagi untuk login akun lain.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
    const toggleBtn = document.querySelector('.toggle-password');
    const pwd = document.getElementById('password');
    if (toggleBtn && pwd) {
        toggleBtn.addEventListener('click', () => {
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);
            const icon = toggleBtn.querySelector('i');
            if (icon) { icon.dataset.feather = type === 'text' ? 'eye-off' : 'eye'; }
            feather.replace();
        });
    }
</script>
</body>
</html>
