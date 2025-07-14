<?php
session_start();
include 'db.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo "Anda belum login.";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id            = $_SESSION['user_id'];
  $nama_rencana       = $_POST['namaRencana'];
  $jenis_kegiatan     = $_POST['jenisKegiatan']; // otomatis "Penanaman"
  $tanggal_mulai      = $_POST['tanggalMulai'];
  $tanggal_selesai    = $_POST['tanggalSelesai'];
  $catatan            = $_POST['catatan'];
  $luas_lahan         = (int)$_POST['luasLahan'];
  $daerah             = $_POST['daerah'];

  // Dapat dari form (manual input user)
  $hargaPerKarung     = (int)$_POST['hargaPerKarung'];
  $hargaBibitPer100m  = (int)$_POST['hargaBibitPer100m'];

  // Parameter tetap
  $fasePemupukan      = 4;
  $dosisPer100m       = 2;

  // Hitung otomatis
  $jumlah_karung   = ceil(($luas_lahan / 100) * $dosisPer100m * $fasePemupukan);
  $harga_pupuk     = $jumlah_karung * $hargaPerKarung;
  $harga_bibit     = ceil(($luas_lahan / 100) * $hargaBibitPer100m);
  $modal_tanam     = $harga_pupuk + ($harga_bibit * $luas_lahan);
  $hasil_panen     = $modal_tanam * 2;
  $hasil_bersih    = $hasil_panen - $modal_tanam;

  // Simpan ke database
  $stmt = $conn->prepare("INSERT INTO perencanaan (
    user_id, nama_rencana, jenis_kegiatan, tanggal_mulai, tanggal_selesai, catatan,
    luas_lahan, daerah, harga_pupuk, harga_bibit, jumlah_pupuk,
    modal_tanam, hasil_panen, hasil_bersih
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$stmt) {
    http_response_code(500);
    echo "Gagal menyiapkan statement: " . $conn->error;
    exit;
  }

  $stmt->bind_param(
    "isssssssisiiii",
    $user_id,
    $nama_rencana,
    $jenis_kegiatan,
    $tanggal_mulai,
    $tanggal_selesai,
    $catatan,
    $luas_lahan,
    $daerah,
    $harga_pupuk,
    $harga_bibit,
    $jumlah_karung,
    $modal_tanam,
    $hasil_panen,
    $hasil_bersih
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
