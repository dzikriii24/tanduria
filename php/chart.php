<?php
header('Content-Type: application/json');
include 'db.php'; // koneksi ke database

$result = $conn->query("SELECT nama_lahan, mulai_tanam FROM lahan");
$data = [];

while ($row = $result->fetch_assoc()) {
    $tanggalTanam = new DateTime($row['mulai_tanam']);
    $hariIni = new DateTime(); // sekarang
    $selisih = $tanggalTanam->diff($hariIni)->days; // hitung selisih hari

    $data[] = [
        'nama_lahan' => $row['nama_lahan'],
        'tanggalTanam' => $tanggalTanam->format('d/m/Y'),
        'hariKe' => $selisih
    ];
}

echo json_encode($data);
?>
