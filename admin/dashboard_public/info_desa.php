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
// Grouping debug removed - data normalized
        $info_grouped = [];
        foreach($info_data as $d) {
          $tipe_key = trim(strtolower($d['tipe'] ?? ''));
          if ($tipe_key) {
            $info_grouped[$tipe_key][] = $d;
          } else {
            // fallback to beranda
            $info_grouped['beranda'][] = $d;
          }
        }

$galeri_query = $pdo->query("SELECT * FROM galeri ORDER BY id DESC");
$galeri_data = $galeri_query->fetchAll(PDO::FETCH_ASSOC);

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Konten - Admin</title>
<link rel="stylesheet" href="../assets/css/info_desa.css">
  <link rel="stylesheet" href="../../admin/assets/css/style.css">

</head>
<body>
<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">
  <div class="header-card">
    <h2>Kelola Konten Publik</h2>
    <button class="btn-primary" onclick="openModal()">+ Tambah Baru</button>
  </div>

  <div class="tabs-container">
    <ul class="tab-nav">
      <?php foreach($tabs as $id => $tab): ?>
        <li><button class="tab-btn <?= $id === 'beranda' ? 'active' : '' ?>" data-tab="<?= $id ?>">
          <?= htmlspecialchars($tab['label']) ?>
        </button></li>
      <?php endforeach; ?>
    </ul>

    <?php foreach($tabs as $id => $tab): 
      $rows = $tab['data'];
      $isText = $tab['type'] === 'text';
    ?>
      <div class="tab-panel <?= $id === 'beranda' ? 'active' : '' ?>" data-tab="<?= $id ?>">
        <div class="section-card">
          <h3><?= htmlspecialchars($tab['label']) ?></h3>
          
          <?php if (empty($rows)): ?>
            <div class="empty-state">
              <p>Tidak ada data untuk <strong><?= $tab['label'] ?></strong></p>
              <button class="btn-primary" onclick="openModal('<?= $tab['type'] ?>', '<?= $id ?>')">
                Tambah Data Pertama
              </button>
            </div>
          <?php else: ?>
            <table class="table">
              <thead>
                <tr>
                  <th><?= $isText ? 'Judul' : 'Preview' ?></th>
                  <th><?= $isText ? 'Urutan' : 'Judul' ?></th>
                  <?php if (!$isText): ?><th>Gambar</th><?php endif; ?>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($rows as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row[$isText ? 'judul' : 'file']) ?></td>
                    <?php if ($isText): ?>
                      <td><?= $row['urutan'] ?></td>
                    <?php endif; ?>
                    <?php if (!$isText): ?>
                      <td><img src="uploads/<?= htmlspecialchars($row['file']) ?>" class="thumb" alt=""></td>
                    <?php endif; ?>
                    <td>
                      <button class="btn-edit" onclick="editRow(<?= json_encode($row) ?>, '<?= $tab['type'] ?>')">
                        ✏️ Edit
                      </button>
                      <button class="btn-delete" onclick="deleteRow(<?= $row['id'] ?>, '<?= $tab['type'] ?>')">
                        🗑️ Hapus
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Modal Text -->
<div id="modal" class="modal">
  <div class="modal-box">
    <h3 id="modal-title">Form Konten</h3>
    <form id="text-form" method="post" action="simpan_info.php">
      <input type="hidden" id="text-id" name="id">
      <input type="hidden" id="text-type" name="tipe">
      
      <label>Judul</label>
      <input type="text" id="text-judul" name="judul" required>
      
      <label>Urutan</label>
      <input type="number" id="text-urutan" name="urutan" min="0" value="0">
      
      <label>Konten</label>
      <textarea id="text-konten" name="konten" rows="6" required></textarea>
      
      <div class="modal-action">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
    
    <!-- Galeri Form -->
    <form id="galeri-form" method="post" action="upload_galeri.php" enctype="multipart/form-data" style="display:none">
      <input type="hidden" id="galeri-id" name="id">
      
      <label>Judul Foto</label>
      <input type="text" id="galeri-judul" name="judul" required>
      
      <label>Foto</label>
      <input type="file" id="galeri-foto" name="foto" accept="image/*" required>
      
      <div class="modal-action">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>

<script>
// Tab Switching
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    
    btn.classList.add('active');
    document.querySelector(`[data-tab="${btn.dataset.tab}"]`).classList.add('active');
  });
});

function openModal(type = 'text', tabId = '') {
  const modal = document.getElementById('modal');
  const textForm = document.getElementById('text-form');
  const galeriForm = document.getElementById('galeri-form');
  
  if (type === 'text') {
    document.getElementById('text-type').value = tabId;
    textForm.style.display = 'block';
    galeriForm.style.display = 'none';
    document.getElementById('modal-title').textContent = 'Tambah Konten';
    textForm.reset();
  } else {
    textForm.style.display = 'none';
    galeriForm.style.display = 'block';
    document.getElementById('modal-title').textContent = 'Tambah Galeri';
    galeriForm.reset();
  }
  
  modal.style.display = 'flex';
}

function editRow(data, type) {
  openModal(type, data.tipe || '');
  setTimeout(() => {
    if (type === 'text') {
      document.getElementById('text-id').value = data.id;
      document.getElementById('text-type').value = data.tipe;
      document.getElementById('text-judul').value = data.judul;
      document.getElementById('text-urutan').value = data.urutan || 0;
      document.getElementById('text-konten').value = data.konten || '';
      document.getElementById('modal-title').textContent = 'Edit Konten';
    } else {
      document.getElementById('galeri-id').value = data.id;
      document.getElementById('galeri-judul').value = data.judul;
      document.getElementById('galeri-foto').removeAttribute('required');
      document.getElementById('modal-title').textContent = 'Edit Foto';
    }
  }, 100);
}

function deleteRow(id, type) {
  if (confirm(`Yakin hapus data ini?\nType: ${type}`)) {
    const url = type === 'text' ? `hapus_info.php?id=${id}` : `hapus_galeri.php?id=${id}`;
    window.location.href = url;
  }
}

function closeModal() {
  document.getElementById('modal').style.display = 'none';
}

// Status
const params = new URLSearchParams(location.search);
if (params.get('status') === 'sukses_simpan' || params.get('status') === 'sukses_upload') {
  const toast = document.createElement('div');
  toast.className = 'toast show';
  toast.textContent = 'Data berhasil disimpan!';
  document.body.appendChild(toast);
  
  setTimeout(() => toast.remove(), 3000);
}
</script>
  <script src="../../admin/assets/js/sidebar.js"></script>
</body>

</html>

