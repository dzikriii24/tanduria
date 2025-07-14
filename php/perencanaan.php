<?php
session_start();
include 'db.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data perencanaan
$query = $conn->prepare("SELECT * FROM perencanaan WHERE user_id = ? ORDER BY id DESC");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$perencanaan = [];
while ($row = $result->fetch_assoc()) {
  $perencanaan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Daftar Perencanaan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-green-700">Daftar Perencanaan</h1>
    <a href="formPerencanaan.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
      + Tambah Perencanaan Baru
    </a>
  </div>

    <?php if (count($perencanaan) > 0): ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse bg-white shadow rounded-xl">
          <thead class="bg-green-600 text-white">
            <tr>
              <th class="px-4 py-3">#</th>
              <th class="px-4 py-3">Nama Rencana</th>
              <th class="px-4 py-3">Jenis Kegiatan</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Catatan</th>
              <th class="px-4 py-3">Luas (m²)</th>
              <th class="px-4 py-3">Daerah</th>
              <th class="px-4 py-3">Modal Tanam</th>
              <th class="px-4 py-3">Hasil Panen</th>
              <th class="px-4 py-3">Hasil Bersih</th>
              
            </tr>
          </thead>
          <tbody>
            <?php foreach ($perencanaan as $i => $data): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2"><?= $i + 1 ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($data['nama_rencana']) ?></td>
                <td class="px-4 py-2"><?= $data['jenis_kegiatan'] ?></td>
                <td class="px-4 py-2">
                  <?= date("d M Y", strtotime($data['tanggal_mulai'])) ?>
                  s/d
                  <?= date("d M Y", strtotime($data['tanggal_selesai'])) ?>
                </td>
                <td class="px-4 py-2"><?= $data['catatan'] ?></td>
                <td class="px-4 py-2"><?= number_format($data['luas_lahan']) ?></td>
                <td class="px-4 py-2"><?= $data['daerah'] ?></td>
                <td class="px-4 py-2 text-green-700 font-semibold">Rp <?= number_format($data['modal_tanam']) ?></td>
                <td class="px-4 py-2 text-blue-700 font-semibold">Rp <?= number_format($data['hasil_panen']) ?></td>
                <td class="px-4 py-2 text-purple-700 font-semibold">Rp <?= number_format($data['hasil_bersih']) ?></td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded">
        Belum ada perencanaan dibuat.
      </div>
    <?php endif; ?>

    <div class="mt-6 flex justify-between">
  <a href="../index.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-5 py-3 rounded-lg font-semibold">
    ← Kembali
  </a>
</div>

  </div>
</body>
</html>
