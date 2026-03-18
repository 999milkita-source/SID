<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

check_login();
require_role('admin');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$data = $pdo->query(
  "SELECT * FROM info_desa ORDER BY tipe, urutan ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];
foreach($data as $d){
  $grouped[$d['tipe']][] = $d;
}

$label = [
  'beranda' => 'Beranda',
  'profil'  => 'Profil Desa',
  'tentang' => 'Tentang Desa',
  'kontak'  => 'Kontak'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin - Info Desa</title>
<link rel="stylesheet" href="../assets/css/info_desa.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">

<section class="card header-card">
  <h2>Kelola Informasi Desa</h2>
  <button class="btn-primary" onclick="openAdd()">+ Tambah Konten</button>
</section>

<?php foreach($grouped as $tipe => $rows): ?>
<section class="card section-card">
  <h3><?= $label[$tipe] ?? strtoupper($tipe) ?></h3>

  <table class="table">
    <thead>
      <tr>
        <th>Judul</th>
        <th>Urutan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($rows as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['judul']) ?></td>
        <td><?= (int)$d['urutan'] ?></td>
        <td>
          <button class="btn-edit"
            onclick='openEdit(<?= json_encode($d, JSON_HEX_TAG) ?>)'>
            Edit
          </button>
          <a class="btn-delete"
             href="hapus_info.php?id=<?= $d['id'] ?>">
            Hapus
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

</section>
<?php endforeach; ?>

</main>

<!-- MODAL -->
<div id="modal" class="modal">
  <div class="modal-box">
    <h3>Form Informasi Desa</h3>

    <form method="post" action="simpan_info.php">
      <input type="hidden" name="id" id="id">

      <label>Judul</label>
      <input type="text" name="judul" id="judul" required>

      <label>Tipe Halaman</label>
      <select name="tipe" id="tipe" required>
        <option value="beranda">Beranda</option>
        <option value="profil">Profil Desa</option>
        <option value="tentang">Tentang Desa</option>
        <option value="kontak">Kontak</option>
      </select>

      <label>Urutan</label>
      <input type="number" name="urutan" id="urutan" value="0">

      <label>Konten</label>
      <textarea name="konten" id="konten" rows="6" required></textarea>

      <div class="modal-action">
        <button type="submit" class="btn-primary">Simpan</button>
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="../assets/js/info_desa.js"></script>
</body>
</html>