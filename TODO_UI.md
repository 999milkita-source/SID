# Admin Info Desa UI Improvement Plan

**Status: Planning → Implementation**

## Goals:
✅ Non-confusing layout with **TABS per tipe**  
✅ **Clear Background tab** with warna/gambar + live preview  
✅ Easy for admin to set halaman depan background  

## Plan (4 Steps):
1. **Replace** `admin/dashboard_public/info_desa.php` with tabbed UI
2. **Extract** background logic from `info_desa_new.php` → dedicated Background tab
3. **Enhance** `admin/assets/css/info_desa.css` for tabs/preview
4. **Update** `admin/assets/js/info_desa.js` for tab switching + preview

## Tab Structure:
```
[Beranda] [Profil] [Tentang] [Kontak] [Background*] [Galeri]
  ↓           ↓        ↓        ↓         ↓*           ↓
table     table    table    table   WARNA/GAMBAR    grid
                    ↑preview ↑upload  PREVIEW LIVE
```

**Approve to proceed with tabbed UI implementation? (Yes/No)
