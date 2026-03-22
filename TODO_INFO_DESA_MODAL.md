# MODAL UX IMPROVEMENT PLAN - INFO DESA
STATUS: READY TO IMPLEMENT

## 1. admin/assets/js/info_desa.js patches
**Add tabMap object at top:**
```
const tabMap = {
  'beranda': 'Beranda',
  'profil': 'Profil', 
  'tentang': 'Tentang',
  'kontak': 'Kontak'
};
```

**Patch openModal(type, tab):**
```
document.getElementById("modal-title").textContent = `Tambah Konten - ${tabMap[tab] || tab}`;
```

**Patch editRow(data, type):**
```
const tabLabel = tabMap[data.tipe] || data.tipe;
document.getElementById("modal-title").textContent = `Edit Konten - ${tabLabel}`;
```

## 2. admin/dashboard_public/info_desa.php HTML patch
**After <h3 id="modal-title"> add:**
```
<div id="tab-info" style="background:#f3f4f6;padding:8px;border-radius:4px;margin:10px 0;font-size:14px;color:#4b5563;">
<i>📄 Data ini akan ditampilkan di halaman <strong id="tab-label"></strong></i>
</div>
```

**JS set:**
```
document.getElementById("tab-label").textContent = tabMap[tab] || tab;
```

## Followup Steps:
- Test all tabs: openModal → clear title/label
- Edit existing → shows correct tab
- No backend changes

Proceed to edit files?

