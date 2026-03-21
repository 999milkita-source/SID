<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

try {
    $stmt = $pdo->prepare("SELECT konten FROM info_desa WHERE tipe='background' ORDER BY urutan ASC LIMIT 1");
    $stmt->execute();
    $konten = $stmt->fetchColumn();
    
    echo json_encode(['konten' => $konten ?: null]);
} catch (Exception $e) {
    echo json_encode(['error' => 'DB error']);
}
?>
