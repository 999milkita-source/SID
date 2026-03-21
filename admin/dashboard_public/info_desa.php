<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

// Get all data
$data = $pdo->query("SELECT * FROM info_desa ORDER BY tipe, urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
$galeri_data = $pdo->query("SELECT * FROM galeri ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Group data by tipe
$grouped = [];
foreach($data as $d){
  $grouped[$d['tipe']][] = $d;
}

$tabs = ['beranda'=>'Beranda', 'profil'=>'Profil Desa', 'tentang'=>'Tentang Desa', 'kontak'=>'Kontak', 'background'=>'Background Halaman', 'galeri'=>'Galeri'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin - Kelola Konten Desa</title>
<link rel="stylesheet" href="../assets/css/info_desa.css?v=2">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* Tab Styles */
.tab-container { margin-top: 20px; }
.tab-nav {
  display: flex;
  background: white;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  overflow: hidden;
}
.tab-btn {
  flex: 1;
  padding: 16px 20px;
  border: none;
  background: none;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
  border-bottom: 3px solid transparent;
}
.tab-btn.active {
  background: #f8fafc;
  color: #0ea5e9;
  border-bottom-color: #0ea5e9;
}
.tab-content {
  display: none;
  background: white;
  padding: 30px;
  border-radius: 0 12px 12px 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.tab-content.active { display: block; }

/* Background Tab Special */
.bg-preview {
  width: 100%; height: 200px;
  border-radius: 12px;
  border: 2px dashed #cbd5e1;
  margin: 20px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: #64748b;
  position: relative;
  overflow: hidden;
}
.bg-preview.applied {
  border-color: #10b981;
  color: #059669;
}
.bg-preview img { width: 100%; height: 100%; object-fit: cover; }
.bg-current {
  background: var(--current-bg, #f8fafc);
  min-height: 120px;
  border-radius: 8px;
  margin: 10px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
}
.radio-group { display: flex; gap: 20px; margin: 20px 0; }
.radio-option { 
  flex: 1; 
  padding: 20px; 
  border: 2px solid #e2e8f0; 
  border-radius: 12px; 
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
}
.radio-option:hover, .radio-option.selected { 
  border-color: #0ea5e9; 
  background: #f0f9ff;
}
.color-section { display: none; }
.file-section { display: none; }
.color-section.active, .file-section.active { display: block; }
#bgLivePreview { 
  width: 100%; 
  height: 180px; 
  border-radius: 12px; 
  border: 1px solid #e2e8f0;
  margin-top: 15px;
}
</style>
</head>
<body>

<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">
  <section class="card header-card">
    <h2><i data-feather="edit-3"></i> Kelola Konten Desa</h2>
    <div>
      <select onchange="switchTab(this.value)" id="quickTab">
        <?php foreach($tabs as $key => $name): ?>
        <option value="<?= $key ?>"><?= $name ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn-primary" onclick="openAdd()">+ Tambah Baru</button>
    </div>
  </section>

  <div class="tab-container">
    <!-- Tab Navigation -->
    <nav class="tab-nav">
      <?php foreach($tabs as $key => $name): ?>
      <button class="tab-btn active" onclick="switchTab('<?= $key ?>')" data-tab="<?= $key ?>">
        <?= $name ?>
      </button>
      <?php endforeach; ?>
    </nav>

    <!-- Tab Contents -->
    <?php foreach($tabs as $key => $name): ?>
    <div id="tab-<?= $key ?>" class="tab-content active">
      
      <?php if($key === 'background'): ?>
        <!-- === BACKGROUND SPECIAL TAB === -->
        <div style="background:#f0fdfa;padding:25px;border-radius:12px;border-left:5px solid #10b981;margin-bottom:25px;">
          <h3 style="margin:0 0 15px 0;color:#065f46;">🎨 Set Background Halaman Depan</h3>
          <p style="color:#047857;margin:0 0 20px 0;">Pilih warna atau upload gambar. Langsung terlihat di <strong>index.php, profil.php, dll</strong></p>
          
          <!-- Current Background Preview -->
          <div>
            <strong>Background Saat Ini:</strong>
            <div class="bg-current" id="currentBgPreview" style="background: linear-gradient(135deg, #ecfeff, #f8fafc);">
              Preview real-time
            </div>
            <small style="color:#6b7280;">(Ubah di bawah → preview update otomatis)</small>
          </div>

          <!-- Radio Choice -->
          <div class="radio-group">
            <div class="radio-option selected" onclick="selectBgType('warna')">
              <div style="font-size:24px;margin-bottom:8px;">🎨</div>
              <strong>Pilih Warna</strong><br>
              <small>Klik untuk solid/gradient color</small>
            </div>
            <div class="radio-option" onclick="selectBgType('gambar')">
              <div style="font-size:24px;margin-bottom:8px;">🖼️</div>
              <strong>Upload Gambar</strong><br>
              <small>Full page background</small>
            </div>
          </div>

          <!-- Warna Section -->
          <div id="warnaSection" class="color-section">
            <div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
              <div>
                <label>Warna 1:</label>
                <input type="color" id="color1" value="#ecfeff" onchange="updateLivePreview()">
              </div>
              <div>
                <label>Warna 2 (gradient):</label>
                <input type="color" id="color2" value="#f8fafc" onchange="updateLivePreview()">
              </div>
            </div>
            <div id="bgLivePreview" style="background: linear-gradient(135deg, #ecfeff, #f8fafc);"></div>
            <small style="color:#6b7280;">Kosongkan warna 2 = solid color</small>
          </div>

          <!-- Gambar Section -->
          <div id="gambarSection" class="file-section">
            <input type="file" id="bgFile" accept="image/*" onchange="previewImage()">
            <div id="imagePreview" class="bg-preview">Upload gambar untuk preview</div>
            <small>Pilih file JPG/PNG → preview langsung</small>
          </div>

          <!-- Quick Save -->
          <div style="margin-top:30px;padding-top:20px;border-top:1px solid #e2e8f0;">
            <input type="hidden" id="bgKonten" value="">
            <button class="btn-primary" onclick="saveBackground()" style="font-size:16px;padding:12px 24px;">
              💾 Simpan Background
            </button>
          </div>

        </div>

      <?php elseif($key === 'galeri'): ?>
        <!-- Galeri Upload -->
        <div style="display:grid;gap:20px;">
          <div style="background:#fef3c7;padding:20px;border-radius:12px;">
            <h4>📸 Upload Foto Galeri</h4>
            <form method="post" action="upload_galeri.php" enctype="multipart/form-data">
              <input name="judul" placeholder="Judul foto" required style="width:250px;margin-right:10px;">
              <input type="file" name="foto" accept="image/*" required>
              <button type="submit" class="btn-primary">Upload</button>
            </form>
          </div>
          
          <?php if(empty($galeri_data)): ?>
            <p style="text-align:center;color:#9ca3af;padding:60px;">📭 Belum ada foto galeri. Upload di atas!</p>
          <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;">
              <?php foreach($galeri_data as $g): ?>
              <div class="galeri-preview" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                <img src="../../uploads/<?= htmlspecialchars($g['file']) ?>" style="width:100%;height:160px;object-fit:cover;">
                <div style="padding:15px;">
                  <p style="font-weight:500;margin:0 0 10px 0;"><?= htmlspecialchars($g['judul']) ?></p>
                  <a href="hapus_galeri.php?id=<?= $g['id'] ?>" class="btn-delete" onclick="return confirm('Hapus foto?')" style="padding:6px 12px;font-size:12px;">Hapus</a>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

      <?php else: ?>
        <!-- Regular Content Tab -->
        <?php $rows = $grouped[$key] ?? []; ?>
        <?php if(empty($rows)): ?>
          <div style="text-align:center;padding:80px 20px;color:#9ca3af;">
            <div style="font-size:48px;margin-bottom:20px;">📄</div>
            <h3>Belum ada konten</h3>
            <p>Klik "Tambah Baru" untuk membuat konten <?= strtolower($tabs[$key]) ?></p>
            <button class="btn-primary" onclick="openAddWithType('<?= $key ?>')" style="margin-top:20px;">+ Buat Pertama</button>
          </div>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Urutan</th>
                <th>Preview</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($rows as $d): ?>
              <tr>
                <td><strong><?= htmlspecialchars($d['judul']) ?></strong></td>
                <td><?= $d['urutan'] ?></td>
                <td style="max-width:200px;"><small><?= substr(strip_tags($d['konten']), 0, 80) ?>...</small></td>
                <td>
                  <button class="btn-edit" onclick='openEdit(<?= json_encode($d, JSON_HEX_TAG) ?>)' title="Edit">
                    ✏️ Edit
                  </button>
                  <a class="btn-delete" href="hapus_info.php?id=<?= $d['id'] ?>" onclick="return confirm('Hapus?')" title="Hapus">🗑️</a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Universal Edit Modal -->
<div id="modal" class="modal">
  <div class="modal-box" style="width:500px;">
    <h3 id="modalTitle">Edit Konten</h3>
    <form method="post" action="simpan_info.php" enctype="multipart/form-data" id="editForm">
      <input type="hidden" name="id" id="editId">
      
      <label>Judul</label>
      <input type="text" name="judul" id="editJudul" required>
      
      <label>Tipe</label>
      <select name="tipe" id="editTipe" required disabled>
        <option value="beranda">Beranda</option>
        <option value="profil">Profil Desa</option>
        <option value="tentang">Tentang Desa</option>
        <option value="kontak">Kontak</option>
      </select>
      
      <label>Urutan</label>
      <input type="number" name="urutan" id="editUrutan" min="0">
      
      <label>Konten</label>
      <textarea name="konten" id="editKonten" rows="8" placeholder="Tulis konten HTML/teks..."></textarea>
      
      <div class="modal-action">
        <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
        <button type="button" class="btn-cancel" onclick="closeModal()">❌ Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="../assets/js/info_desa.js"></script>
<script>
// === TAB SYSTEM ===
let currentTab = 'beranda';
function switchTab(tab) {
  // Update nav
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
  
  // Update content
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
  document.getElementById(`tab-${tab}`).classList.add('active');
  
  currentTab = tab;
  document.getElementById('quickTab').value = tab;
  
  // Special: load current background
  if (tab === 'background') loadCurrentBackground();
}

// Quick tab switcher
document.getElementById('quickTab').onchange = function() {
  switchTab(this.value);
};

// Background Logic
function selectBgType(type) {
  document.querySelectorAll('.radio-option').forEach(opt => opt.classList.remove('selected'));
  event.target.closest('.radio-option').classList.add('selected');
  
  document.querySelectorAll('.color-section, .file-section').forEach(sec => sec.classList.remove('active'));
  document.getElementById(type + 'Section').classList.add('active');
}

function updateLivePreview() {
  const c1 = document.getElementById('color1').value;
  const c2 = document.getElementById('color2').value;
  const preview = document.getElementById('bgLivePreview');
  const isGradient = c2 !== '#f8fafc';
  
  preview.style.background = isGradient ? `linear-gradient(135deg, ${c1}, ${c2})` : c1;
  document.getElementById('currentBgPreview').style.background = preview.style.background;
  document.getElementById('bgKonten').value = preview.style.background;
}

function previewImage() {
  const file = document.getElementById('bgFile').files[0];
  const preview = document.getElementById('imagePreview');
  
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = `<img src="${e.target.result}">`;
      preview.classList.add('applied');
      preview.style.backgroundImage = `url(${e.target.result})`;
      document.getElementById('currentBgPreview').style.backgroundImage = `url(${e.target.result})`;
      document.getElementById('bgKonten').value = `url('../uploads/${file.name}') center / cover no-repeat fixed`;
    };
    reader.readAsDataURL(file);
  }
}

function saveBackground() {
  const konten = document.getElementById('bgKonten').value;
  const judul = 'Background Halaman Utama';
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'simpan_info.php';
  form.style.display = 'none';
  
  const inputs = [
    {name: 'judul', value: judul},
    {name: 'tipe', value: 'background'},
    {name: 'urutan', value: 1},
    {name: 'konten', value: konten},
    {name: 'background_foto', type: 'file', files: document.getElementById('bgFile').files}
  ];
  
  inputs.forEach(input => {
    const el = document.createElement(input.type || 'input');
    el.name = input.name;
    el.value = input.value;
    if (input.files) el.files = input.files;
    form.appendChild(el);
  });
  
  document.body.appendChild(form);
  form.submit();
}

function loadCurrentBackground() {
  // Load from DB LIMIT 1
  fetch('get_current_bg.php')
    .then(r => r.json())
    .then(data => {
      if (data.konten) {
        document.getElementById('currentBgPreview').style.cssText = `background: ${data.konten} !important;`;
        document.getElementById('bgKonten').value = data.konten;
      }
    })
    .catch(() => {});
}

// Init
switchTab('background'); // Start with most important tab
feather.replace();
</script>
</body>
</html>
