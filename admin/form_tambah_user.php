<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$success = $error = null;

// daftar penduduk (users) untuk dipilih
$penduduk_list = $pdo->query("SELECT id, nik, nama_lengkap FROM users ORDER BY nama_lengkap ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $email   = trim($_POST['email'] ?? '');
    $role    = $_POST['role'] ?? '';
    $password_raw = $_POST['password'] ?? '';

    if ($user_id <= 0 || $email === '' || $role === '' || $password_raw === '') {
        $error = 'Penduduk, email, role, dan password wajib diisi.';
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET email = :email,
                password = :password,
                role = :role
            WHERE id = :id
        ");
        $stmt->execute([
            ':email'    => $email,
            ':password' => $password,
            ':role'     => $role,
            ':id'       => $user_id,
        ]);

        $_SESSION['success'] = 'User berhasil dibuat/diupdate.';
        header('Location: kelola_user.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link rel="stylesheet" href="assets/css/kelola_user.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Tambah User</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Pilih Penduduk (NIK - Nama)</label>
        <select name="user_id" required>
            <option value="">-- Pilih Penduduk --</option>
            <?php foreach ($penduduk_list as $p): ?>
                <option value="<?= (int)$p['id'] ?>">
                    <?= htmlspecialchars($p['nik']).' - '.htmlspecialchars($p['nama_lengkap']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Email / Username</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="penduduk">Penduduk</option>
            <option value="rt">RT</option>
            <option value="rw">RW</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Simpan</button>
        <a href="kelola_user.php" class="btn">Kembali</a>
    </form>
</main>
</body>
</html>

