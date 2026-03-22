<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

// Data
$info_query = $pdo->query("SELECT * FROM info_desa ORDER BY tipe, urutan ASC");
$info_data = $info_query->fetchAll(PDO::FETCH_ASSOC);

// Grouping
$info_grouped = [];
foreach($info_data as $d) {
  $tipe_key = trim(strtolower($d['tipe'] ?? ''));
  if ($tipe_key) {
    $info_grouped[$tipe_key][] = $d;
  } else {
    $info_grouped['beranda'][] = $d;
  }
}

// Galeri
$galeri_query = $pdo->query("SELECT * FROM galeri ORDER BY id DESC");
$galeri_data = $galeri_query->fetchAll(PDO::FETCH_ASSOC);

// Tabs
$tabs = [
  'beranda' => ['label' => 'Beranda', 'type' => 'text', 'data' => $info_grouped['beranda'] ?? []],
  'profil' => ['label' => 'Profil', 'type' => 'text', 'data' => $info_grouped['profil'] ?? []],
  'tentang' => ['label' => 'Tentang', 'type' => 'text', 'data' => $info_grouped['tentang'] ?? []],
  'kontak' => ['label' => 'Kontak', 'type' => 'text', 'data' => $info_grouped['kontak'] ?? []],
  'galeri' => ['label' => 'Galeri', 'type' => 'galeri', 'data' => $galeri_data]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Konten</title>

<link rel="stylesheet" href="../assets/css/info_desa.css">
<link rel="stylesheet" href="../../admin/assets/css/style.css">

</head>
<body>

<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">

<div class="header-card">
  <h2>Kelola Konten Publik</h2>
  <button class="btn-primary" onclick="openModal('text','beranda')">
    + Tambah Baru
  </button>
</div>

<!-- TAB NAV -->
<ul class="tab-nav">
<?php foreach($tabs as $id => $tab): ?>
  <li>
    <button type="button"
      class="tab-btn <?= $id==='beranda'?'active':'' ?>"
      data-tab="<?= $id ?>">
      <?= $tab['label'] ?>
    </button>
  </li>
<?php endforeach; ?>
</ul>

<!-- TAB CONTENT -->
<?php foreach($tabs as $id => $tab): 
  $rows = $tab['data'];
  $isText = $tab['type'] === 'text';
?>
<div class="tab-panel <?= $id==='beranda'?'active':'' ?>" id="tab-<?= $id ?>">

<h3><?= $tab['label'] ?></h3>

<?php if(empty($rows)): ?>
  <p style="color:#888;">Belum ada data</p>
  <button class="btn-primary"
    onclick="openModal('<?= $tab['type'] ?>','<?= $id ?>')">
    Tambah Data
  </button>

<?php else: ?>

<table class="table">
<thead>
<tr>
<th><?= $isText ? 'Judul' : 'Preview' ?></th>
<th><?= $isText ? 'Urutan' : 'Judul' ?></th>
<?php if(!$isText): ?><th>Gambar</th><?php endif; ?>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php foreach($rows as $row): ?>
<tr>
<td><?= htmlspecialchars($row[$isText ? 'judul':'file']) ?></td>

<?php if($isText): ?>
<td><?= $row['urutan'] ?></td>
<?php endif; ?>

<?php if(!$isText): ?>
<td><img src="uploads/<?= $row['file'] ?>" width="80"></td>
<?php endif; ?>

<td>
<button type="button" class="btn-edit"
onclick='editRow(<?= json_encode($row) ?>,"<?= $tab['type'] ?>")'>
Edit
</button>

<button type="button" class="btn-delete"
onclick='deleteRow(<?= $row['id'] ?>,"<?= $tab['type'] ?>")'>
Hapus
</button>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>

</div>
<?php endforeach; ?>

</main>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-box">

<h3 id="modal-title">Form</h3>
<div id="tab-info" style="background:#f8fafc;padding:8px;border-left:3px solid #3b82f6;border-radius:4px;margin:10px 0;font-size:14px;color:#374151;">
  📄 Data ini akan ditampilkan di halaman <strong id="tab-label"></strong>
</div>

<form id="text-form" method="post" action="simpan_info.php">
<input type="hidden" id="text-id" name="id">
<input type="hidden" id="text-type" name="tipe">

<input type="text" id="text-judul" name="judul" placeholder="Judul" required>
<input type="number" id="text-urutan" name="urutan" value="0">
<textarea id="text-konten" name="konten" required></textarea>

<button type="submit" class="btn-primary">Simpan</button>
<button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
</form>

<form id="galeri-form" method="post" action="upload_galeri.php"
enctype="multipart/form-data" style="display:none">
<input type="hidden" id="galeri-id" name="id">
<input type="text" id="galeri-judul" name="judul" required>
<input type="file" id="galeri-foto" name="foto">

<button type="submit" class="btn-primary">Upload</button>
<button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
</form>

</div>
</div>

<!-- JS DIPISAH -->
<script src="../assets/js/info_desa.js"></script>

</body>
</html>