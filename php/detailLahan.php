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
  $biaya     = (int) ($_POST['biaya'] ?? 0);
  $catatan   = $_POST['catatan'] ?? '';

  $stmt = $conn->prepare("INSERT INTO aktivitas_lahan (lahan_id, jenis_aktivitas, tanggal, pelaksana, jumlah, biaya, catatan)
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssis", $id, $jenis, $tanggal, $pelaksana, $jumlah, $biaya, $catatan);
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../css/icon.css">
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

  <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
  <a href="lahan.php"
   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
  <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
       xmlns="http://www.w3.org/2000/svg">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
  </svg>
  Kembali ke Daftar Lahan
</a>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:items-center md:gap-8 mt-4">
      <div class="md:col-span-3">
        <img src="uploads/<?= htmlspecialchars($lahan['foto_lahan']) ?>" class="rounded w-full h-[400px] object-cover" alt="<?= htmlspecialchars($lahan['nama_lahan']) ?>" />
      </div>

      <div class="md:col-span-1">
        <div class="max-w-lg md:max-w-none px-4 mx-auto">
          <h2 class="text-2xl font-semibold text-gray-900 sm:text-3xl"><?= htmlspecialchars($lahan['nama_lahan']) ?></h2>
          <p class="mt-2 text-gray-700"><?= htmlspecialchars($lahan['deskripsi']) ?></p>

          <div class="flex-box items-center gap-2 mt-8">
            <p class="text-black text-lg font-semibold">Lokasi Detail Lahan</p>
            <p class="mt-2 text-black texl-sm font-semibold"><?= htmlspecialchars($lahan['tempat_lahan']) ?></p>
          </div>
        </div>

        <?php if (!empty($lahan['link_maps'])): ?>
          <div class="mt-8 mx-auto px-2 flex justify-start items-start">
            <a href="<?= htmlspecialchars($lahan['link_maps']) ?>" target="_blank"
              class="group flex items-center justify-between gap-4 rounded-lg border border-indigo-600 bg-indigo-600 px-5 py-3 transition-colors hover:bg-transparent focus:ring-3 focus:outline-hidden">
              <span class="font-medium text-white transition-colors group-hover:text-indigo-600">
                Lihat di Maps
              </span>
              <span class="shrink-0 rounded-full border border-current bg-white p-2 text-indigo-600">
                <svg class="size-5 shadow-sm rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Spesifikasi Lahan -->
  <h3 class="text-black text-xl font-semibold mx-auto px-4 mt-4">Spesifikasi Lahan</h3>
  <div class="flow-root grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 mx-auto px-4">
    <div>
      <dl class="divide-y divide-gray-200 rounded border border-gray-200 text-sm *:even:bg-gray-50">
        <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
          <dt class="font-medium text-gray-900">Luas Lahan</dt>
          <dd class="text-gray-700 sm:col-span-2"><?= htmlspecialchars($lahan['luas_lahan']) ?> Ha</dd>
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
          <dt class="font-medium text-gray-900">Link Maps</dt>
          <dd class="text-gray-700 sm:col-span-2">
            <a href="<?= htmlspecialchars($lahan['link_maps']) ?>" class="text-blue-600 hover:underline" target="_blank">Lihat Lokasi</a>
          </dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- Tambahan: Jika mau iframe embed -->
  <?php if (!empty($lahan['link_maps']) && str_contains($lahan['link_maps'], 'embed')): ?>
    <div class="px-4 mx-auto mt-6">
      <h3 class="text-black text-xl font-semibold">Peta Lokasi</h3>
      <iframe class="rounded-md mt-4" src="<?= $lahan['link_maps'] ?>" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
  <?php endif; ?>

  <!-- Tambah Aktivitas -->
<div class="mt-10 px-4">
  <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Aktivitas Lahan</h2>
  <form method="post" class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Jenis Aktivitas</label>
        <select name="jenis" required class="w-full border border-gray-300 rounded px-3 py-2">
          <option value="Pemupukan">Pemupukan</option>
          <option value="Penyemprotan">Penyemprotan Pestisida</option>
          <option value="Pengairan">Pengairan</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Tanggal</label>
        <input type="date" name="tanggal" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Pelaksana</label>
        <input type="text" name="pelaksana" required class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Ucok / Mang Ujang">
      </div>
      <div>
        <label class="block text-sm font-medium">Jumlah (opsional)</label>
        <input type="text" name="jumlah" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="10 karung / 5 liter">
      </div>
      <div>
        <label class="block text-sm font-medium">Biaya (Rp)</label>
        <input type="number" name="biaya" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="10000">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Catatan</label>
        <textarea name="catatan" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Deskripsi aktivitas..."></textarea>
      </div>
    </div>
    <button type="submit" class="mt-4 px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Simpan Aktivitas</button>
  </form>

 <!-- Daftar Aktivitas -->
 <div class="mt-4 mx-auto px-4">
    <h3 class="text-black text-xl font-semibold mx-auto px-4 flex justify-start items-start">Riwayat Aktivitas Lahan</h3>
    <?php if (count($aktivitas) === 0): ?>
      <p class="text-gray-600 mt-2">Belum ada aktivitas tercatat.</p>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 gap-4 mt-4">
        <?php foreach ($aktivitas as $act): ?>
          <div class="block rounded-md border border-gray-300 p-4 shadow-sm sm:p-6 bg-gray-50">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  <?= htmlspecialchars($act['jenis_aktivitas']) ?>
                </h3>
                <p class="text-sm text-gray-700">Pelaksana: <?= htmlspecialchars($act['pelaksana']) ?></p>
                <?php if ($act['jumlah']): ?><p class="text-sm text-gray-700">Jumlah: <?= htmlspecialchars($act['jumlah']) ?></p><?php endif; ?>
                <?php if ($act['biaya'] > 0): ?><p class="text-sm text-gray-700">Biaya: Rp. <?= number_format($act['biaya'], 0, ',', '.') ?></p><?php endif; ?>
                <?php if ($act['catatan']): ?><p class="text-sm text-gray-700 mt-1"><?= nl2br(htmlspecialchars($act['catatan'])) ?></p><?php endif; ?>
              </div>
              <div class="text-sm text-right text-gray-500 whitespace-nowrap">
                <?= date('d M Y', strtotime($act['tanggal'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <br>
<p>alamak oyy kajung ruan</p>
</div>
</body>
</html>
