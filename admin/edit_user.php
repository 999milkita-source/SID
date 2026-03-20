<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$success = $error = null;

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: kelola_user.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['error'] = 'User tidak ditemukan.';
    header('Location: kelola_user.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? '';

    if ($email === '' || $role === '') {
        $error = 'Email dan role wajib diisi.';
    } else {
        $params = [
            ':email' => $email,
            ':role'  => $role,
            ':id'    => $id,
        ];

        $sql = "UPDATE users SET email = :email, role = :role";

        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql .= ", password = :password";
            $params[':password'] = $password;
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = 'User berhasil diperbarui.';
        header('Location: kelola_user.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="assets/css/kelola_user.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Edit User</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p><strong><?= htmlspecialchars($row['nik']).' - '.htmlspecialchars($row['nama_lengkap']) ?></strong></p>

    <form method="POST">
        <label>Email / Username</label>
        <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>">

        <label>Role</label>
        <select name="role">
            <option value="penduduk" <?= $row['role']==='penduduk' ? 'selected' : '' ?>>Penduduk</option>
            <option value="rt" <?= $row['role']==='rt' ? 'selected' : '' ?>>RT</option>
            <option value="rw" <?= $row['role']==='rw' ? 'selected' : '' ?>>RW</option>
            <option value="admin" <?= $row['role']==='admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <label>Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password">

        <button type="submit">Update</button>
        <a href="kelola_user.php" class="btn">Kembali</a>
    </form>
</main>
</body>
</html>

