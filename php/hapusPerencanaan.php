<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo "Anda belum login.";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
  $user_id = $_SESSION['user_id'];
  $id = (int) $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM perencanaan WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $id, $user_id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    http_response_code(500);
    echo "Gagal menghapus data: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
} else {
  http_response_code(400);
  echo "Permintaan tidak valid.";
}
?>
