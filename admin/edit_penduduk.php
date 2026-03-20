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
    $_SESSION['error'] = 'Data penduduk tidak ditemukan.';
    header('Location: kelola_user.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik          = trim($_POST['nik'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $alamat       = trim($_POST['alamat'] ?? '');
    $rt           = (int)($_POST['rt'] ?? 0);
    $rw           = (int)($_POST['rw'] ?? 0);
    $no_hp        = trim($_POST['no_hp'] ?? '');

    if ($nik === '' || $nama_lengkap === '') {
        $error = 'NIK dan Nama wajib diisi.';
    } else {
        $stmt = $pdo->prepare("
            UPDATE users
            SET nik = :nik,
                nama_lengkap = :nama,
                tempat_lahir = :tmp,
                tanggal_lahir = :tgl,
                jenis_kelamin = :jk,
                alamat = :alamat,
                rt = :rt,
                rw = :rw,
                no_hp = :hp
            WHERE id = :id
        ");
        $stmt->execute([
            ':nik'    => $nik,
            ':nama'   => $nama_lengkap,
            ':tmp'    => $tempat_lahir ?: null,
            ':tgl'    => $tanggal_lahir ?: null,
            ':jk'     => $jenis_kelamin ?: null,
            ':alamat' => $alamat ?: null,
            ':rt'     => $rt ?: null,
            ':rw'     => $rw ?: null,
            ':hp'     => $no_hp ?: null,
            ':id'     => $id,
        ]);

        $_SESSION['success'] = 'Data penduduk berhasil diperbarui.';
        header('Location: kelola_user.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penduduk</title>
    <link rel="stylesheet" href="assets/css/kelola_user.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Edit Penduduk</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>NIK</label>
        <input type="text" name="nik" value="<?= htmlspecialchars($row['nik']) ?>" required>

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($row['tempat_lahir']) ?>">

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($row['tanggal_lahir']) ?>">

        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin">
            <option value="">-- Pilih --</option>
            <option value="L" <?= $row['jenis_kelamin']==='L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $row['jenis_kelamin']==='P' ? 'selected' : '' ?>>Perempuan</option>
        </select>

        <label>Alamat</label>
        <textarea name="alamat"><?= htmlspecialchars($row['alamat']) ?></textarea>

        <label>RT</label>
        <input type="number" name="rt" value="<?= htmlspecialchars($row['rt']) ?>">

        <label>RW</label>
        <input type="number" name="rw" value="<?= htmlspecialchars($row['rw']) ?>">

        <label>No HP</label>
        <input type="text" name="no_hp" value="<?= htmlspecialchars($row['no_hp']) ?>">

        <button type="submit">Update</button>
        <a href="kelola_user.php" class="btn">Kembali</a>
    </form>
</main>
</body>
</html>

