<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id         = $_SESSION['user_id'];
  $nama_rencana    = $_POST['namaRencana'];
  $jenis_kegiatan  = $_POST['jenisKegiatan'];
  $tanggal_mulai   = $_POST['tanggalMulai'];
  $tanggal_selesai = $_POST['tanggalSelesai'];
  $catatan         = $_POST['catatan'];
  $luas_lahan      = (int)$_POST['luasLahan'];
  $daerah          = $_POST['daerah'];
  $harga_pupuk     = (int)$_POST['hargaPupuk'];
  $jumlah_karung   = (int)$_POST['jumlahKarungPupuk'];
  $harga_bibit     = (int)$_POST['hargaBibit'];

  // Hitung otomatis
  $modal_tanam     = ($harga_pupuk * $jumlah_karung) + ($harga_bibit * $luas_lahan);
  $hasil_panen     = $modal_tanam * 2;
  $hasil_bersih    = $hasil_panen - $modal_tanam;

  $stmt = $conn->prepare("INSERT INTO perencanaan (
    user_id, nama_rencana, jenis_kegiatan, tanggal_mulai, tanggal_selesai, catatan,
    luas_lahan, daerah, harga_pupuk, harga_bibit, jumlah_pupuk, modal_tanam, hasil_panen, hasil_bersih
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param("isssssssisiiii", $user_id, $nama_rencana, $jenis_kegiatan, $tanggal_mulai,
    $tanggal_selesai, $catatan, $luas_lahan, $daerah, $harga_pupuk, $harga_bibit, $jumlah_karung,
    $modal_tanam, $hasil_panen, $hasil_bersih);

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
