<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "tanduria");

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'User belum login']);
    exit;
}

$stmt = $conn->prepare("SELECT id, nama_lahan, koordinat_lat, koordinat_lng, mulai_tanam, foto_lahan, link_maps FROM lahan WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
