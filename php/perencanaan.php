<?php
session_start();
include 'db.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// PAGINATION SETTINGS
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = $conn->prepare("SELECT COUNT(*) FROM perencanaan WHERE user_id = ?");
$countQuery->bind_param("i", $user_id);
$countQuery->execute();
$countQuery->bind_result($total_rows);
$countQuery->fetch();
$countQuery->close();

$total_pages = ceil($total_rows / $limit);

// Ambil data per halaman
$query = $conn->prepare("SELECT * FROM perencanaan WHERE user_id = ? ORDER BY id ASC LIMIT ? OFFSET ?");
$query->bind_param("iii", $user_id, $limit, $offset);
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
      <table class="w-full text-left border-collapse bg-white shadow rounded-xl text-sm">
        <thead class="bg-green-600 text-white">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Nama Rencana</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Catatan</th>
            <th class="px-4 py-3">Luas (m²)</th>
            <th class="px-4 py-3">Daerah</th>
            <th class="px-4 py-3">Jumlah Karung</th>
            <th class="px-4 py-3">Total Harga Pupuk</th>
            <th class="px-4 py-3">Total Harga Bibit</th>
            <th class="px-4 py-3">Modal Tanam</th>
            <th class="px-4 py-3">Hasil Panen</th>
            <th class="px-4 py-3">Hasil Bersih</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($perencanaan as $i => $data): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2"><?= ($offset + $i + 1) ?></td>
              <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($data['nama_rencana']) ?></td>
              <td class="px-4 py-2">
                <?= date("d M Y", strtotime($data['tanggal_mulai'])) ?>
                s/d
                <?= date("d M Y", strtotime($data['tanggal_selesai'])) ?>
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($data['catatan']) ?></td>
              <td class="px-4 py-2"><?= number_format($data['luas_lahan']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($data['daerah']) ?></td>
              <td class="px-4 py-2"><?= $data['jumlah_pupuk'] ?> karung</td>
              <td class="px-4 py-2 text-green-700">Rp <?= number_format($data['total_harga_pupuk']) ?></td>
              <td class="px-4 py-2 text-green-700">Rp <?= number_format($data['total_harga_bibit']) ?></td>
              <td class="px-4 py-2 text-green-700 font-bold">Rp <?= number_format($data['modal_tanam']) ?></td>
              <td class="px-4 py-2 text-blue-700 font-bold">Rp <?= number_format($data['hasil_panen']) ?></td>
              <td class="px-4 py-2 text-purple-700 font-bold">Rp <?= number_format($data['hasil_bersih']) ?></td>
              <td class="px-4 py-2 text-center">
                <button onclick="hapusRencana(<?= $data['id'] ?>)" class="text-red-600 hover:text-red-800">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 2a1 1 0 00-.894.553L6.382 4H4a1 1 0 000 2h12a1 1 0 100-2h-2.382l-.724-1.447A1 1 0 0012 2H8zm-3 6a1 1 0 011 1v7a1 1 0 102 0V9a1 1 0 112 0v7a1 1 0 102 0V9a1 1 0 112 0v7a1 1 0 102 0V9a1 1 0 011-1H5z" clip-rule="evenodd" />
                  </svg>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
      <div class="mt-6 flex justify-center">
        <nav class="inline-flex space-x-1">
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" 
              class="px-4 py-2 border rounded-lg 
              <?= $i == $page ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </nav>
      </div>
    <?php endif; ?>

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

<script>
  function hapusRencana(id) {
    Swal.fire({
      title: 'Yakin hapus perencanaan?',
      text: "Data yang dihapus tidak dapat dikembalikan.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('hapusPerencanaan.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + id
        })
        .then(response => response.text())
        .then(result => {
          if (result.trim() === 'success') {
            Swal.fire('Terhapus!', 'Perencanaan berhasil dihapus.', 'success')
              .then(() => location.reload());
          } else {
            Swal.fire('Gagal', result, 'error');
          }
        })
        .catch(() => {
          Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
        });
      }
    });
  }
</script>
</body>
</html>
