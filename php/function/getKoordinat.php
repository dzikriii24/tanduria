<?php
session_start();
header('Content-Type: application/json');

include '../db.php'; // path sesuai struktur folder

$user_id = $_SESSION['user_id'] ?? null;

// Cek apakah user sudah login
if (!$user_id) {
    echo json_encode(['error' => 'User belum login']);
    exit;
}

// Ambil lahan terakhir milik user
$query = "SELECT koordinat_lat, koordinat_lng, nama_lahan FROM lahan WHERE user_id = ? AND status = 'aktif' ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

if (!$data) {
    echo json_encode(['error' => 'Tidak ada lahan ditemukan untuk user']);
    exit;
}

echo json_encode($data);