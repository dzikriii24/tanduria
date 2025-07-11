<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil semua input dari form
  $nama_rencana    = $_POST['namaRencana'];
  $jenis_kegiatan  = $_POST['jenisKegiatan'];
  $tanggal_mulai   = $_POST['tanggalMulai'];
  $tanggal_selesai = $_POST['tanggalSelesai'];
  $catatan         = $_POST['catatan'];
  $luas_lahan      = $_POST['luasLahan'];
  $daerah          = $_POST['daerah'];
  $harga_pupuk     = $_POST['hargaPupuk'];
  $harga_bibit     = $_POST['hargaBibit'];
  $modal           = $_POST['modal'];
  $hasil_panen     = $_POST['hasilPanen'];

  // Hitung hasil bersih
  $hasil_bersih = $hasil_panen - $modal;

  // Query simpan ke database
  $query = "INSERT INTO perencanaan (
    nama_rencana, jenis_kegiatan, tanggal_mulai, tanggal_selesai,
    catatan, luas_lahan, daerah, harga_pupuk, harga_bibit,
    modal, hasil_panen, hasil_bersih
  ) VALUES (
    '$nama_rencana', '$jenis_kegiatan', '$tanggal_mulai', '$tanggal_selesai',
    '$catatan', '$luas_lahan', '$daerah', '$harga_pupuk', '$harga_bibit',
    '$modal', '$hasil_panen', '$hasil_bersih'
  )";

  if (mysqli_query($conn, $query)) {
    header("Location: perencanaan.php?success=1");
    exit();
  } else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
  }
}
?>