<?php
header('Content-Type: application/json');
include 'db.php'; 
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT nama_lahan, mulai_tanam FROM lahan WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $tanggalTanam = new DateTime($row['mulai_tanam']);
        $hariIni = new DateTime();
        $selisih = $tanggalTanam->diff($hariIni)->days;

        $data[] = [
            'nama_lahan' => $row['nama_lahan'],
            'tanggalTanam' => $tanggalTanam->format('d/m/Y'),
            'hariKe' => $selisih
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode(["error" => "User tidak ditemukan."]);
}
?>
