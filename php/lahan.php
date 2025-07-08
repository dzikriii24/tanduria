<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tanduria";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Proses simpan data jika POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama      = $_POST['namaLahan'];
  $luas      = $_POST['luasLahan'];
  $tempat    = $_POST['tempatLahan'];
  $jenis     = $_POST['jenisPadi'];
  $tanam     = $_POST['mulaiTanam'];
  $deskripsi = $_POST['deskripsiLahan'];
  $maps      = $_POST['linkMaps'];
  $pestisida = $_POST['pestisida'];
  $modal     = $_POST['modalTanam'];

  // Upload file
  $fotoName = $_FILES['fotoLahan']['name'];
  $tmpPath  = $_FILES['fotoLahan']['tmp_name'];
  $targetDir = "uploads/";
  if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $newFileName = uniqid() . '_' . basename($fotoName);
  $targetPath = $targetDir . $newFileName;

  if (move_uploaded_file($tmpPath, $targetPath)) {
    $stmt = $conn->prepare("INSERT INTO lahan (nama_lahan, luas_lahan, tempat_lahan, jenis_padi, mulai_tanam, foto_lahan, deskripsi, link_maps, pestisida, modal_tanam) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssssi", $nama, $luas, $tempat, $jenis, $tanam, $newFileName, $deskripsi, $maps, $pestisida, $modal);

    if ($stmt->execute()) {
      header("Location: lahan.php?success=1");
      exit;
    } else {
      header("Location: formLahan.php?error=db");
      exit;
    }
  } else {
    header("Location: formLahan.php?error=upload");
    exit;
  }
}

// Ambil data lahan dari database
$result = $conn->query("SELECT * FROM lahan ORDER BY id DESC");
$lahanData = [];
while ($row = $result->fetch_assoc()) {
  $lahanData[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Lahan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../css/icon.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">

  <?php if (isset($_GET['success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Lahan berhasil ditambahkan.',
        confirmButtonColor: '#10b981'
      });
      if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);
      }
    </script>
  <?php endif; ?>

  <div class="w-full px-6 py-10 sm:px-10 md:px-20 lg:px-32 xl:px-48 flex-grow">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Lahan Anda</h1>
      <a href="#" id="tambahLahanBtn"
         onclick="handleTambahLahanClick(event)"
         class="group relative overflow-hidden inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2 rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="absolute inset-0 bg-white opacity-0 transition duration-300 rounded-lg" id="rippleEffect"></span>
        <svg id="spinnerIcon" class="hidden w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <svg id="plusIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        <span class="font-medium">Tambah Lahan</span>
      </a>
    </div>

    <div class="space-y-6">
      <?php if (count($lahanData) > 0): ?>
        <?php foreach ($lahanData as $lahan): ?>
          <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
              <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-800 mb-1"><?= htmlspecialchars($lahan['nama_lahan']) ?></h2>
                <p class="text-sm text-gray-600">Jenis padi: <?= htmlspecialchars($lahan['jenis_padi']) ?></p>
                <p class="text-sm text-gray-600">Mulai tanam: <?= htmlspecialchars(date("d/m/Y", strtotime($lahan['mulai_tanam']))) ?></p>
              </div>
              <div class="flex justify-end w-full md:w-auto">
                <a href="detailLahan.php?id=<?= $lahan['id'] ?>"
                   class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-800 font-medium transition duration-200 group">
                  Lihat detail lahan
                  <svg class="w-4 h-4 transform transition-transform duration-200 group-hover:translate-x-1" fill="none"
                       stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                       xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-gray-600 text-sm">Belum ada data lahan.</div>
      <?php endif; ?>
    </div>
  </div>

<<<<<<< HEAD
  <footer class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
    <div class="grid grid-cols-5 text-center text-xs text-gray-500">
      <a href="index.php" class="group py-2 px-3 flex flex-col items-center hover:text-blue-600">
        <i class="fi fi-sr-home text-lg text-blue-600"></i>
        <span class="text-blue-600">Dashboard</span>
      </a>
      <a href="../php/notifikasi.php" class="group py-2 px-3 flex flex-col items-center hover:text-blue-600">
        <i class="fi fi-ss-bell text-lg"></i>
        <span>Notifikasi</span>
      </a>
      <a href="../php/lahan.php" class="group py-2 px-3 flex flex-col items-center hover:text-blue-600">
        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg">
          <i class="fi fi-sr-land-layers text-xl"></i>
=======
  <!-- Footer Navigasi -->
  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
        <div class="grid grid-cols-5 text-center text-xs text-gray-500">
            <!-- Home -->
            <a href="../index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all active-nav">
                <i class="fi fi-sr-home text-lg"></i>
                <span class="">Dashboard</span>
            </a>

            <!-- Bookmark -->
            <a href="../php/notifikasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-ss-bell text-lg"></i>
                <span>Notifikasi</span>
            </a>

            <!-- Post -->
            <a href="../php/lahan.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg">
                    <i class="fi fi-sr-land-layers text-xl"></i>
                </div>
                <span class="mt-1 text-blue-600">Lahan</span>
            </a>

            <!-- Search -->
            <a href="../php/edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span>Edukasi</span>
            </a>
            <!-- Settings -->
            <a href="../php/profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-sr-user text-lg"></i>
                <span>Profil</span>
            </a>
>>>>>>> 6e692d4 (indexs)
        </div>
        <span class="mt-1 text-blue-600">Lahan</span>
      </a>
      <a href="../php/edukasi.php" class="group py-2 px-3 flex flex-col items-center hover:text-blue-600">
        <i class="fi fi-ss-book-open-cover text-lg"></i>
        <span>Edukasi</span>
      </a>
      <a href="../php/profile.php" class="group py-2 px-3 flex flex-col items-center hover:text-blue-600">
        <i class="fi fi-sr-user text-lg"></i>
        <span>Profil</span>
      </a>
    </div>
  </footer>

  <script>
    function handleTambahLahanClick(event) {
      event.preventDefault();

      const button = document.getElementById("tambahLahanBtn");
      const ripple = document.getElementById("rippleEffect");
      const spinner = document.getElementById("spinnerIcon");
      const plusIcon = document.getElementById("plusIcon");

      button.disabled = true;
      spinner.classList.remove("hidden");
      plusIcon.classList.add("hidden");

      ripple.classList.remove("opacity-0");
      ripple.classList.add("opacity-20");

      setTimeout(() => {
        window.location.href = "formLahan.php";
      }, 200);
    }
  </script>

</body>