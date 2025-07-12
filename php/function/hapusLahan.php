<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $id = $_POST['id'] ?? null;

    if ($user_id && $id) {
        $stmt = $conn->prepare("DELETE FROM lahan WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan atau tidak cocok user_id.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid.']);
}
?>
