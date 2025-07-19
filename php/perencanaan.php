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
<html lang="id" class="bg-white">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="../css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
  <title>Perencanaan</title>
  <style type="text/tailwind">
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/font.css">
  <link rel="stylesheet" href="../css/hover.css">
  <link rel="stylesheet" href="../css/icon.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="poppins-regular">
  <div class="navbar shadow-sm">
    <div class="navbar-start">
      <a href="../index.php" class="flex items-center space-x-2 bg-[#2C8F53] shadow-md rounded-full px-3 py-2 text-white hover:bg-[#1D6034] transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
      </a>
    </div>
    <div class="navbar-center">
      <h2 class="text-xl font-semibold text-[#4E4E4E]">Daftar Perencanaan</h2>
    </div>
    <div class="navbar-end">
      <a href="formPerencanaan.php"
        class="group relative overflow-hidden inline-flex items-center gap-2 bg-gradient-to-r from-[#2C8F53] to-[#2C8F53] text-white px-6 py-2 rounded-lg shadow-lg hover:from-[#1D6034] hover:to-[#1D6034] transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="absolute inset-0 bg-white opacity-0 transition duration-300 rounded-lg" id="rippleEffect"></span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
      </a>
    </div>
  </div>

  <!-- CONTENT -->
  <?php if (count($perencanaan) > 0): ?>
    <div class="mx-auto px-4 grid lg:grid-cols-4 grid-cols-1 gap-4 mt-5">
      <?php foreach ($perencanaan as $i => $data): ?>

        <div class="block rounded-lg p-4 shadow-xs shadow-indigo-100" style="background: linear-gradient(to bottom left, #ffe4e6, #ccfbf1);">
          <div class="mt-2">
            <dl>
              <div class="flex grid grid-cols-3">
                <div>
                  <dt class="sr-only">Lokasi</dt>

                  <dd class="text-sm text-[#4E4E4E]"><?= htmlspecialchars($data['daerah']) ?></dd>
                </div>
                <div>
                  <dt class="sr-only">Tanggal</dt>

                  <dd class="text-xs text-[#4E4E4E]"><?= date("d M Y", strtotime($data['tanggal_mulai'])) ?>
                    s/d
                    <?= date("d M Y", strtotime($data['tanggal_selesai'])) ?></dd>
                </div>
                <div>
                  <dt class="sr-only">Luas</dt>

                  <dd class="text-sm text-[#4E4E4E] font-semibold"><?= number_format($data['luas_lahan']) ?> m²</dd>
                </div>
              </div>

              <div>
                <dt class="sr-only">Nama Rencana</dt>

                <dd class="font-medium"><?= htmlspecialchars($data['nama_rencana']) ?></dd>
              </div>
            </dl>

            <p class="mt-4 text-sm">Kebutuhan Sampai Panen</p>
            <div class="mt-2 flex items-center gap-8 text-xs">

              <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                <i class="fi fi-sr-bag-seedling"></i>

                <div class="mt-1.5 sm:mt-0">
                  <p class="text-[#4E4E4E]">Pupuk</p>

                  <p class="font-medium"><?= $data['jumlah_pupuk'] ?> Karung</p>
                  <p class="font-medium">Rp. <?= number_format($data['total_harga_pupuk']) ?></p>
                </div>
              </div>

              <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                <i class="fi fi-sc-seedling"></i>

                <div class="mt-1.5 sm:mt-0">
                  <p class="text-[#4E4E4E]">Bibit</p>

                  <p class="font-medium">Rp. <?= number_format($data['total_harga_bibit']) ?></p>
                </div>
              </div>
            </div>


            <div class="mt-6 flex items-center gap-8 text-xs">
              <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                <i class="fi fi-sr-coins"></i>

                <div class="mt-1.5 sm:mt-0">
                  <p class="text-[#4E4E4E]">Modal Tanam</p>

                  <p class="font-medium">Rp. <?= number_format($data['modal_tanam']) ?></p>
                </div>
              </div>

              <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                <i class="fi fi-br-chart-mixed-up-circle-dollar"></i>

                <div class="mt-1.5 sm:mt-0">
                  <p class="text-[#4E4E4E]">Hasil Panen</p>

                  <p class="font-medium">Rp. <?= number_format($data['hasil_panen']) ?></p>
                </div>
              </div>
            </div>

            <div class="mt-4 sm:mt-0">
              <p class="mt-4 line-clamp-3 text-sm text-pretty text-gray-700">
                <?= htmlspecialchars($data['catatan']) ?>
              </p>
            </div>
            <dl class="mt-6 flex gap-4 lg:gap-6">
              <div>
                <dt class="text-sm font-medium text-gray-700">Hasil Bersih</dt>

                <dd class="text-xs text-gray-700">Rp. <?= number_format($data['hasil_bersih']) ?></dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-700">Hapus Perencanaan</dt>
                <div class="flex justify-center">
                  <button onclick="hapusRencana(<?= $data['id'] ?>)" class="text-red-600 hover:text-red-800">
                    <i class="fi fi-ss-cross-circle"></i>
                  </button>
                </div>

              </div>
            </dl>
          </div>
        </div>
      <?php endforeach; ?>


    </div>
  <?php else: ?>
    <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded">
      Belum ada perencanaan dibuat.
    </div>
  <?php endif; ?>


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
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
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