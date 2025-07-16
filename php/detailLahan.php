<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tanduria";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: lahan.php");
  exit;
}

$showSuccessAlert = isset($_GET['success']) && $_GET['success'] == 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis     = $_POST['jenis'] ?? '';
  $tanggal   = $_POST['tanggal'] ?? '';
  $pelaksana = $_POST['pelaksana'] ?? '';
  $jumlah    = $_POST['jumlah'] ?? '';
  $luas = $_POST['luas'] ?? '';
  $biaya     = (int) ($_POST['biaya'] ?? 0);
  $catatan   = $_POST['catatan'] ?? '';

  $stmt = $conn->prepare("INSERT INTO aktivitas_lahan (lahan_id, jenis_aktivitas, tanggal, pelaksana, jumlah, luas, biaya, catatan)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssis", $id, $jenis, $tanggal, $pelaksana, $jumlah, $luas, $biaya, $catatan);
  $stmt->execute();
  header("Location: detailLahan.php?id=$id&success=1");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM lahan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  echo "<h1>Lahan tidak ditemukan</h1>";
  exit;
}
$lahan = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM aktivitas_lahan WHERE lahan_id = ? ORDER BY tanggal DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$aktivitas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


function fetchData($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

function getHargaRataBerasHariIni()
{
  $url_summary = "http://127.0.0.1:5000/api/gkp-summary-latest";
  $dataSummary = json_decode(fetchData($url_summary), true);

  $totalHarga = 0;
  $jumlahProvinsi = 0;

  if (isset($dataSummary['data'])) {
    foreach ($dataSummary['data'] as $row) {
      if (isset($row['avg_price']) && is_numeric($row['avg_price']) && $row['avg_price'] > 0) {
        $totalHarga += floatval($row['avg_price']);
        $jumlahProvinsi++;
      }
    }
  }

  return $jumlahProvinsi > 0 ? round($totalHarga / $jumlahProvinsi) : 0;
}

// Contoh pemakaian
$hargaRataHariIni = getHargaRataBerasHariIni();
$total = $hargaRataHariIni * $lahan['luas_lahan'];
$bersih = $total -  $lahan['modal_tanam'];


$koordinat_lat = $lahan['koordinat_lat'];
$koordinat_lng = $lahan['koordinat_lng'];
$maps_embed_url = "https://www.google.com/maps?q={$koordinat_lat},{$koordinat_lng}&output=embed";
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../css/icon.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <title>Detail Lahan - <?= htmlspecialchars($lahan['nama_lahan']) ?></title>
</head>

<body class="bg-white">
  <?php if ($showSuccessAlert): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Aktivitas berhasil disimpan.',
        confirmButtonColor: '#3085d6'
      });


      // Hapus parameter ?success=1 dari URL
      if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);
      }
    </script>
  <?php endif; ?>


  <div class="navbar text-white bg-white shadow-sm flex justify-between items-center px-4 py-2">
    <!-- Tombol Back -->
    <a href="lahan.php" class="flex items-center space-x-2 bg-white shadow-md rounded-full px-3 py-2 text-gray-800 hover:bg-gray-100 transition">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
      </svg>
    </a>

    <!-- Nama Lahan -->
    <div class="text-center">
      <p class="poppins-semibold text-base text-xl text-[#4E4E4E]"><?= htmlspecialchars($lahan['nama_lahan']) ?></p>
    </div>

    <!-- Lokasi -->
    <div class="text-right">
      <p class="poppins-reguler text-xs sm:text-sm text-[#4E4E4E]"><?= htmlspecialchars($lahan['tempat_lahan']) ?></p>
    </div>
  </div>

  <!-- Header -->
  <section class="mt-2 overflow-hidden bg-gray-50 sm:grid sm:grid-cols-2 sm:items-center px-4 mx-auto gap-4">
    <img
      alt=""
      src="uploads/<?= htmlspecialchars($lahan['foto_lahan']) ?>" class="rounded w-full h-[400px] object-cover"
      class="h-full w-full object-cover sm:h-[calc(100%_-_2rem)] sm:self-end sm:rounded-ss-[30px] md:h-[calc(100%_-_4rem)] md:rounded-ss-[60px]" />


    <iframe
      src="<?= htmlspecialchars($maps_embed_url) ?>"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>


  </section>


  <!-- Spesifikasi Lahan -->
  <h3 class="text-black text-xl font-semibold mx-auto px-4 mt-4">Spesifikasi Lahan</h3>
  <div class="flow-root grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 mx-auto px-4">
    <div>
      <dl class="divide-y divide-gray-200 rounded border border-gray-200 text-sm *:even:bg-gray-50">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Luas Lahan</dt>
          <dd class="text-gray-700 sm:col-span-2">
            <?= number_format($lahan['luas_lahan'] / 10000, 2, '.', '') ?> Hektar
          </dd>

        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Tanggal Penanaman</dt>
          <dd class="text-gray-700 sm:col-span-2"><?= date("d/m/Y", strtotime($lahan['mulai_tanam'])) ?></dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Jenis Padi</dt>
          <dd class="text-gray-700 sm:col-span-2"><?= htmlspecialchars($lahan['jenis_padi']) ?></dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Jenis Pestisida</dt>
          <dd class="text-gray-700 sm:col-span-2"><?= htmlspecialchars($lahan['pestisida']) ?></dd>
        </div>
      </dl>
    </div>
    <div>
      <dl class="divide-y divide-gray-200 rounded border border-gray-200 text-sm *:even:bg-gray-50">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Modal Penanaman</dt>
          <dd class="text-gray-700 sm:col-span-2">Rp. <?= number_format($lahan['modal_tanam'], 0, ',', '.') ?></dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Lokasi</dt>
          <dd class="text-gray-700 sm:col-span-2">
            <?= htmlspecialchars($lahan['tempat_lahan']) ?>
          </dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Prediksi Hasil Tanam</dt>
          <dd class="text-gray-700 sm:col-span-2">
            Rp. <?= number_format($total, 0, ',', '.') ?>
          </dd>
        </div>

        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Hasil Bersih</dt>
          <dd class="text-gray-700 sm:col-span-2">
            Rp. <?= number_format($bersih, 0, ',', '.') ?>
          </dd>
        </div>
      </dl>
    </div>
  </div>

  <div class="mx-auto max-w-screen-xl px-4 mt-6">
    <div class="text-center">
      <h2 class="text-3xl font-extrabold text-gray-900 sm:text-5xl">Rp. <?= number_format($hargaRataHariIni, 0, ',', '.') ?></h2>

      <p class="mx-auto mt-4 max-w-sm text-gray-500">
        Prediksi rata-rata harga beras di Indonesia untuk tanggal <?= date('d-m-Y'); ?> berdasarkan data terbaru.
      </p>
    </div>
  </div>
  <!-- Tambahan: Jika mau iframe embed -->



  <!-- Tambah Aktivitas -->
  <div class="mt-10 px-4 mb-30">
    <!-- Daftar Aktivitas -->
    <div class="mt-4 mx-auto px-4">
      <h3 class="text-black text-xl font-semibold">Riwayat Aktivitas Lahan</h3>

      <?php if (count($aktivitas) === 0): ?>
        <p class="text-gray-600 mt-2">Belum ada aktivitas tercatat.</p>
      <?php else: ?>
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
          <?php foreach ($aktivitas as $act): ?>
            <div class="block rounded-md border border-gray-300 p-4 shadow-sm sm:p-6 bg-gray-50">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  <?= htmlspecialchars($act['jenis_aktivitas']) ?>
                </h3>
                <p class="mt-1 text-sm text-gray-700">Oleh <?= htmlspecialchars($act['pelaksana']) ?></p>
              </div>

              <dl class="mt-6 flex gap-4 lg:gap-6">
                <div>
                  <dt class="text-sm font-medium text-gray-700">Tanggal</dt>
                  <dd class="text-xs text-gray-700">
                    <p class="mt-1 text-sm text-gray-700"><?= htmlspecialchars($act['tanggal']) ?></p>
                  </dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-700">Jumlah</dt>
                  <dd class="text-xs text-gray-700">
                    <p><?= htmlspecialchars($act['jumlah'] ?: $act['luas']) ?></p>
                  </dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-700">Biaya</dt>
                  <dd class="text-xs text-gray-700">
                    <?php if ($act['biaya'] > 0): ?>
                      <p>Rp. <?= number_format($act['biaya'], 0, ',', '.') ?></p>
                    <?php else: ?>
                      <p>-</p>
                    <?php endif; ?>
                  </dd>
                </div>
              </dl>

              <div class="mt-4">
                <?php if (!empty($act['catatan'])): ?>
                  <p class="text-sm text-gray-700 italic border-l-4 border-blue-400 pl-3"><?= nl2br(htmlspecialchars($act['catatan'])) ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <dialog id="modalAktifitas" class="modal">
    <div class="modal-box w-full max-w-2xl">
      <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
      </form>

      <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Tambah Aktifitas Lahan</h3>

      <form method="post" id="aktifitasForm">
        <div class="mb-4 mt-4">
          <label class="block text-sm font-medium text-gray-700">Jenis Aktivitas</label>
          <select id="jenisAktifitas" name="jenis" class="w-full border border-gray-300 rounded px-3 py-2" required>
            <option disabled selected>Jenis Aktivitas</option>
            <option value="Penyiraman Pestisida">Penyiraman Pestisida</option>
            <option value="Pemberian Pupuk">Pemberian Pupuk</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Nama Pelaksana</legend>
            <input type="text" class="input" placeholder="Ucok" name="pelaksana" />
            <p class="label">Input nama pelaksana</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Tanggal</legend>
            <input type="date" id="datepicker" class="input input-bordered w-full" name="tanggal">
            <p class="label">Input tanggal</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Jumlah</legend>
            <input type="text" class="input input-bordered w-full" placeholder="10 Karung" name="jumlah">
            <p class="label">Opsional</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Biaya</legend>
            <input type="number" class="input input-bordered w-full" placeholder="10.000" name="biaya">
            <p class="label">Opsional</p>
          </fieldset>

          <fieldset class="fieldset sm:col-span-2">
            <legend class="fieldset-legend">Tambah Catatan</legend>
            <textarea class="textarea" placeholder="Catatan" name="catatan"></textarea>
            <p class="label">Opsional</p>
          </fieldset>

        </div>

        <button class="btn btn-success w-full" type="submit">Tambah Aktivitas</button>
      </form>
    </div>
  </dialog>



  <div class="dock">
    <a href="<?= htmlspecialchars($lahan['link_maps']) ?>" target="_blank">
      <i class="fi fi-ss-marker text-lg"></i>
      <span class="dock-label">Lokasi Lahan</span>
    </a>

    <button onclick="document.getElementById('modalAktifitas').showModal()">
      <i class="fi fi-sr-square-plus text-lg"></i>
      <span class="dock-label">Tambah Aktifitas</span>
    </button>

    <button class="btn-selesai" data-id="<?= $lahan['id'] ?>">
      <i class="fi fi-sr-clipboard-check text-lg"></i>
      <span class="dock-label">Selesaikan Lahan</span>
    </button>
    <button class="btn-hapus" data-id="<?= $lahan['id'] ?>">
      <i class="fi fi-ss-trash text-lg"></i>
      <span class="dock-label">Hapus Lahan</span>
    </button>
  </div>

  <script>
    $(".btn-selesai").click(function() {
      const lahanId = $(this).data("id");

      Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Lahan akan ditandai sebagai selesai.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#1D6034",
        cancelButtonColor: "#B03C3C",
        confirmButtonText: "Ya, Selesaikan!"
      }).then((result) => {
        if (result.isConfirmed) {
          // Kirim request ke PHP untuk update
          $.post("function/updateLahan.php", {
            id: lahanId
          }, function(response) {
            Swal.fire({
              title: "Berhasil!",
              text: "Lahan telah ditandai selesai.",
              icon: "success"
            }).then(() => {
              window.location.href = "lahan.php";
            });
          });
        }
      });
    });


    $(".btn-hapus").click(function() {
      const lahanId = $(this).data("id");

      Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Lahan akan ditandai sebagai selesai.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#1D6034",
        cancelButtonColor: "#B03C3C",
        confirmButtonText: "Ya, Hapus!"
      }).then((result) => {
        if (result.isConfirmed) {
          // Kirim request ke PHP untuk update
          $.post("function/hapusLahan.php", {
            id: lahanId
          }, function(response) {
            Swal.fire({
              title: "Berhasil!",
              text: "Lahan telah ditandai selesai.",
              icon: "success"
            }).then(() => {
              window.location.href = "lahan.php";
            });
          });
        }
      });
    });
  </script>

  <script src="../javascript/maps.js"></script>
  <script src="../javascript/other.js"></script>
</body>

</html>