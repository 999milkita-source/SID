<?php
function log_activity($user_id, $aksi){
    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO logs (user_id, aksi, ip_address) VALUES (?,?,?)"
    );
    $stmt->execute([$user_id, $aksi, $_SERVER['REMOTE_ADDR']]);
}
