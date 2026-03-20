<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$success = $error = null;

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
            INSERT INTO users (nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, no_hp)
            VALUES (:nik,:nama,:tmp,:tgl,:jk,:alamat,:rt,:rw,:hp)
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
        ]);

        $_SESSION['success'] = 'Penduduk berhasil ditambahkan.';
        header('Location: kelola_user.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penduduk</title>
    <link rel="stylesheet" href="assets/css/kelola_user.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Tambah Penduduk</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>NIK</label>
        <input type="text" name="nik" required>

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir">

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir">

        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin">
            <option value="">-- Pilih --</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>

        <label>Alamat</label>
        <textarea name="alamat"></textarea>

        <label>RT</label>
        <input type="number" name="rt">

        <label>RW</label>
        <input type="number" name="rw">

        <label>No HP</label>
        <input type="text" name="no_hp">

        <button type="submit">Simpan</button>
        <a href="kelola_user.php" class="btn">Kembali</a>
    </form>
</main>
</body>
</html>

