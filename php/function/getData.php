<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "tanduria");

$result = $conn->query("SELECT id, nama_lahan, koordinat_lat, koordinat_lng, mulai_tanam FROM lahan");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
