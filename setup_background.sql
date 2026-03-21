-- Jalankan di phpMyAdmin atau mysql CLI: c:/xampp/mysql/bin/mysql.exe -uroot sid_wolokota < setup_background.sql

USE sid_wolokota;

-- Hapus record lama jika ada
DELETE FROM info_desa WHERE tipe = 'background';

-- Tambah default background (gradient)
INSERT INTO info_desa (judul, tipe, urutan, konten) VALUES 
('Background Halaman Default', 'background', 1, '{"type":"gradient","color1":"#ecfeff","color2":"#f8fafc"}');

-- Test query
SELECT * FROM info_desa WHERE tipe='background';

PRINT "✅ Background default siap! Test: http://localhost/sidwolokota/public/index.php"
