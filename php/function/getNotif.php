<?php
session_start();
require '../db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode(['total' => 0]);
    exit;
}

// Hitung respon konsultasi
$resKonsul = $conn->query("SELECT COUNT(*) AS total FROM konsultasi WHERE user_id = $user_id AND status = 'respon' AND sudah_dibaca = 0");
$totalKonsul = $resKonsul->fetch_assoc()['total'] ?? 0;

// Hitung lahan panen
$resLahan = $conn->query("SELECT COUNT(*) AS total FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) >= 120");
$totalLahan = $resLahan->fetch_assoc()['total'] ?? 0;

// Hitung fase pupuk dan pestisida
$resFase = $conn->query("
    SELECT COUNT(*) AS total FROM lahan 
    WHERE user_id = $user_id 
    AND (
        DATEDIFF(NOW(), mulai_tanam) IN (7, 25, 45, 20, 35, 50, 60) 
    )
");
$totalFase = $resFase->fetch_assoc()['total'] ?? 0;

echo json_encode(['total' => $totalKonsul + $totalLahan + $totalFase]);
?>
