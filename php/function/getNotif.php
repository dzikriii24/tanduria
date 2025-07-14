<?php
session_start();
require '../db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode(['total' => 0]);
    exit;
}

// Respon Konsultasi
$resKonsul = $conn->query("SELECT COUNT(*) AS total FROM konsultasi WHERE user_id = $user_id AND status = 'respon' AND sudah_dibaca = 0");
$totalKonsul = $resKonsul->fetch_assoc()['total'] ?? 0;

// Panen (>= 120 hari)
$resPanen = $conn->query("SELECT COUNT(*) AS total FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) >= 120");
$totalPanen = $resPanen->fetch_assoc()['total'] ?? 0;

// Pemupukan: 7, 25, 45 HST
$resPupuk = $conn->query("SELECT COUNT(*) AS total FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) IN (7, 25, 45)");
$totalPupuk = $resPupuk->fetch_assoc()['total'] ?? 0;

// Penyemprotan Pestisida: 20, 35, 60 HST
$resPestisida = $conn->query("SELECT COUNT(*) AS total FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) IN (20, 35, 60)");
$totalPestisida = $resPestisida->fetch_assoc()['total'] ?? 0;

// Penyiraman: range hari 0–80
$resSiram = $conn->query("SELECT COUNT(*) AS total FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) BETWEEN 0 AND 80");
$totalSiram = $resSiram->fetch_assoc()['total'] ?? 0;

// Total (respon konsultasi + panen + pemupukan + pestisida + penyiraman)
$totalNotif = $totalKonsul + $totalPanen + $totalPupuk + $totalPestisida + $totalSiram;

echo json_encode([
    'total' => $totalNotif,
    'detail' => [
        'respon_konsultasi' => $totalKonsul,
        'panen' => $totalPanen,
        'pemupukan' => $totalPupuk,
        'penyemprotan' => $totalPestisida,
        'penyiraman' => $totalSiram
    ]
]);
