<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM perencanaan ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Perencanaan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6">Data Perencanaan</h2>


    <div class="overflow-x-auto">
      <table class="min-w-full table-auto border border-gray-300 text-sm">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Nama Rencana</th>
            <th class="px-4 py-2 border">Jenis</th>
            <th class="px-4 py-2 border">Tanggal</th>
            <th class="px-4 py-2 border">Lahan</th>
            <th class="px-4 py-2 border">Daerah</th>
            <th class="px-4 py-2 border">Harga Pupuk</th>
            <th class="px-4 py-2 border">Harga Bibit</th>
            <th class="px-4 py-2 border">Modal</th>
            <th class="px-4 py-2 border">Hasil</th>
            <th class="px-4 py-2 border">Bersih</th>
            <th class="px-4 py-2 border">Catatan</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            $warna = $row['hasil_bersih'] >= 0 ? 'text-green-600' : 'text-red-600';
          ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border text-center"><?= $no++ ?></td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['nama_rencana']) ?></td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
            <td class="px-4 py-2 border"><?= $row['tanggal_mulai'] ?> s.d. <?= $row['tanggal_selesai'] ?></td>
            <td class="px-4 py-2 border"><?= $row['luas_lahan'] ?> m²</td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['daerah']) ?></td>
            <td class="px-4 py-2 border">Rp<?= number_format($row['harga_pupuk']) ?></td>
            <td class="px-4 py-2 border">Rp<?= number_format($row['harga_bibit']) ?>/m²</td>
            <td class="px-4 py-2 border">Rp<?= number_format($row['modal']) ?></td>
            <td class="px-4 py-2 border">Rp<?= number_format($row['hasil_panen']) ?></td>
            <td class="px-4 py-2 border font-semibold <?= $warna ?>">Rp<?= number_format($row['hasil_bersih']) ?></td>
            <td class="px-4 py-2 border"><?= htmlspecialchars($row['catatan']) ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
