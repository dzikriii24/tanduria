<?php
require '../db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$id_konsultasi = (int) ($_POST['id_konsultasi'] ?? 0);

if ($user_id && $id_konsultasi) {
    // Hapus response
    $conn->query("DELETE FROM response WHERE id_konsultasi = $id_konsultasi");

    // Update status konsultasi
    $conn->query("UPDATE konsultasi SET status = 'belum', sudah_dibaca = 1 WHERE id = $id_konsultasi");

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
