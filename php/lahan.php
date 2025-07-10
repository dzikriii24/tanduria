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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <title>Tanduria</title>
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
  <div class="navbar bg-base-100 shadow-sm">
    <div class="flex-1">
      <p class="poppins-semibold text-xl">Daftar Lahan</p>
    </div>
    <div class="flex gap-2">
      <input type="text" placeholder="Cari Lahan" class="input input-bordered w-24 md:w-auto" />
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
      </a>
    </div>
  </div>
  <div class="px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full mt-10">
      <!-- MAP (2 kolom dari 3) -->
      <div class="relative z-10 md:col-span-2">
        <!-- Overlay untuk mengunci interaksi -->
        <div id="map-lock"
          class="absolute inset-0 z-10 cursor-pointer bg-transparent"
          onclick="unlockMap()"
          title="Klik untuk aktifkan peta">
        </div>

        <!-- Map -->
        <div id="map" class="h-[500px] w-full rounded-xl"></div>
      </div>

      <!-- DETAIL (1 kolom) -->
      <div class="w-full">
        <div id="info-panel" class="bg-white shadow-md rounded-xl p-4">
          <h2 class="poppins-bold text-2xl text-[#1F2937] md:text-3xl mb-2">
            Detail Tempat
          </h2>
          <div id="info" class="text-[#4B5563]">
            <!-- Detail lokasi akan muncul di sini -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-10 mx-auto px-4 w-full gap-6 grid sm:grid-cols-2 grid-cols-1">
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

    <a href="#" class="block rounded-lg p-4 shadow-xs shadow-indigo-100">
      <img
        alt=""
        src="https://images.unsplash.com/photo-1613545325278-f24b0cae1224?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80"
        class="h-56 w-full rounded-md object-cover" />

      <div class="mt-2">
        <dl>
          <div>
            <dt class="sr-only">Price</dt>

            <dd class="text-sm text-gray-500">$240,000</dd>
          </div>

          <div>
            <dt class="sr-only">Address</dt>

            <dd class="font-medium">123 Wallaby Avenue, Park Road</dd>
          </div>
        </dl>

        <div class="mt-6 flex items-center gap-8 text-xs">
          <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
            <svg
              class="size-4 text-indigo-700"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
            </svg>

            <div class="mt-1.5 sm:mt-0">
              <p class="text-gray-500">Parking</p>

              <p class="font-medium">2 spaces</p>
            </div>
          </div>

          <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
            <svg
              class="size-4 text-indigo-700"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>

            <div class="mt-1.5 sm:mt-0">
              <p class="text-gray-500">Bathroom</p>

              <p class="font-medium">2 rooms</p>
            </div>
          </div>

          <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
            <svg
              class="size-4 text-indigo-700"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>

            <div class="mt-1.5 sm:mt-0">
              <p class="text-gray-500">Bedroom</p>

              <p class="font-medium">4 rooms</p>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Bottom Navigation Dock -->
  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-white">
    <div class="grid grid-cols-5 text-center text-xs text-[#4E4E4E]">
      <!-- Home -->
      <a href="../index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all active-nav">
        <i class="fi fi-sr-home text-lg"></i>
        <span class="">Dashboard</span>
      </a>

      <!-- Bookmark -->
      <a href="../php/notifikasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <i class="fi fi-ss-bell text-lg"></i>
        <span>Notifikasi</span>
      </a>

      <!-- Post -->
      <a href="" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <div class="w-10 h-10 rounded-full bg-[#1D6034] text-white flex items-center justify-center shadow-lg">
          <i class="fi fi-sr-land-layers text-xl"></i>
        </div>
        <span class="mt-1">Lahan</span>
      </a>

      <!-- Search -->
      <a href="../php/edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <i class="fi fi-ss-book-open-cover text-lg"></i>
        <span>Edukasi</span>
      </a>
      <!-- Settings -->
      <a href="../php/profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <i class="fi fi-sr-user text-lg"></i>
        <span>Profil</span>
      </a>
    </div>
  </div>
  <script src="../javascript/lahan.js"></script>
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