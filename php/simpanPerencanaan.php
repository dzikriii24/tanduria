<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo "Anda belum login.";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id            = $_SESSION['user_id'];
  $nama_rencana       = $_POST['namaRencana'];
  $jenis_kegiatan     = $_POST['jenisKegiatan'];
  $tanggal_mulai      = $_POST['tanggalMulai'];
  $tanggal_selesai    = $_POST['tanggalSelesai'];
  $catatan            = $_POST['catatan'];
  $luas_lahan         = (int)$_POST['luasLahan'];
  $daerah             = $_POST['daerah'];

  // Input dari user
  $hargaPerKarung     = (int)$_POST['hargaPerKarung'];
  $hargaBibitPer100m  = (int)$_POST['hargaBibitPer100m'];

  // Parameter
  $fasePemupukan = 4;
  $dosisPer100m  = 2;

  // Hitung otomatis
  $jumlah_pupuk       = ceil(($luas_lahan / 100) * $dosisPer100m * $fasePemupukan);
  $total_harga_pupuk  = $jumlah_pupuk * $hargaPerKarung;
  $total_harga_bibit  = ceil(($luas_lahan / 100) * $hargaBibitPer100m);
  $modal_tanam        = $total_harga_pupuk + $total_harga_bibit;
  $hasil_panen        = $modal_tanam * 2;
  $hasil_bersih       = $hasil_panen - $modal_tanam;

  $stmt = $conn->prepare("INSERT INTO perencanaan (
    user_id, nama_rencana, jenis_kegiatan, tanggal_mulai, tanggal_selesai, catatan,
    luas_lahan, daerah,
    harga_pupuk, harga_bibit, jumlah_pupuk,
    modal_tanam, hasil_panen, hasil_bersih,
    total_harga_pupuk, total_harga_bibit
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$stmt) {
    http_response_code(500);
    echo "Gagal menyiapkan statement: " . $conn->error;
    exit;
  }

  $stmt->bind_param(
    "isssssssisiiiiii",
    $user_id,
    $nama_rencana,
    $jenis_kegiatan,
    $tanggal_mulai,
    $tanggal_selesai,
    $catatan,
    $luas_lahan,
    $daerah,
    $hargaPerKarung,
    $hargaBibitPer100m,
    $jumlah_pupuk,
    $modal_tanam,
    $hasil_panen,
    $hasil_bersih,
    $total_harga_pupuk,
    $total_harga_bibit
  );

  if ($stmt->execute()) {
    echo "success";
  } else {
    http_response_code(500);
    echo "Gagal menyimpan ke database: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>
