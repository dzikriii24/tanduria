<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tanduria";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['user_id'])) {
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
    <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
    <title>Lahan</title>
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

  <body>
    <section class="bg-white lg:grid lg:h-screen lg:place-content-center">
      <div class="mx-auto w-screen max-w-screen-xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
        <div class="mx-auto max-w-prose text-center">
          <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl">
            Anda Belum Login!
            <strong class="text-indigo-600">Silakan Login Terlebih Dahulu</strong>
          </h1>

          <p class="mt-4 text-base text-pretty text-gray-700 sm:text-lg/relaxed">
            Halaman ini hanya bisa diakses jika Anda sudah login.
          </p>

          <div class="mt-4 flex justify-center gap-4 sm:mt-6">
            <a
              class="inline-block rounded border border-green-600 bg-green-600 px-5 py-3 font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
              href="login.php">
              Login Sekarang
            </a>

            <a
              class="inline-block rounded border border-gray-200 px-5 py-3 font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900"
              href="../">
              Kembali ke Beranda
            </a>
          </div>
        </div>
      </div>
    </section>
  </body>

  </html>
<?php
  exit;
}


$user_id = $_SESSION['user_id'];



// ✅ Simpan Data Jika POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama      = $_POST['namaLahan'];
  $luas      = $_POST['luasLahan'];
  $tempat    = $_POST['tempatLahan'];
  $jenis     = $_POST['jenisPadi'];
  $tanam     = $_POST['mulaiTanam'];
  $deskripsi = $_POST['deskripsiLahan'];
  $link_maps = $_POST['link_maps'];
  $pestisida = $_POST['pestisida'];
  $modal     = $_POST['modalTanam'];
  $lat = floatval($_POST['lat']);
  $lng = floatval($_POST['lng']);



  // 📷 Upload File
  $fotoName = $_FILES['fotoLahan']['name'];
  $tmpPath  = $_FILES['fotoLahan']['tmp_name'];
  $targetDir = "uploads/";
  if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $newFileName = uniqid() . '_' . basename($fotoName);
  $targetPath = $targetDir . $newFileName;

  if (move_uploaded_file($tmpPath, $targetPath)) {
    $stmt = $conn->prepare("INSERT INTO lahan (
      user_id, nama_lahan, luas_lahan, tempat_lahan, jenis_padi, mulai_tanam,
      foto_lahan, deskripsi, link_maps, pestisida, modal_tanam, koordinat_lat, koordinat_lng
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "isisssssssidd",
      $user_id,
      $nama,
      $luas,
      $tempat,
      $jenis,
      $tanam,
      $newFileName,
      $deskripsi,
      $link_maps,
      $pestisida,
      $modal,
      $lat,
      $lng
    );

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


// 🔄 Ambil semua data lahan user
$stmt = $conn->prepare("SELECT * FROM lahan WHERE user_id = ? AND status = 'aktif' ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lahanData = [];
while ($row = $result->fetch_assoc()) {
  $lahanData[] = $row;
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="id" class="bg-[#ffff] overflow-x-hidden">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
  <title>Lahan</title>
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
  <div class="navbar text-white bg-[#ffff] shadow-sm">
    <div class="flex-1">
      <p class="poppins-semibold text-xl text-[#4E4E4E]">Daftar Lahan</p>
    </div>
    <div class="flex gap-2">
      <label class="mt-1 poppins-regular bg-[#FFFFFF] aret-[#1F2937] text-[#1F2937] input items-center -mt-5 flex justify-self-center outline-none rounded-xl hover:outline-hidden focus:outline-hidden lg:w-[500px]" style="outline:none;">
        <input type="search" name="q" required placeholder="Cari Lahan" class="poppins-reguler caret-[#1F2937] text-[#1F2937] bg-[#1F2937 ] outline-none lg:p-4 rounded-lg" style="outline:none;" id="searchInput" onkeyup="searchLahan()" />
        <button class="hover:text-[#7C3AED] transition-colors duration-300 cursor-pointer" type="submit">
          <svg class="h-[1em] opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <g
              stroke-linejoin="round"
              stroke-linecap="round"
              stroke-width="2.5"
              fill="none"
              stroke="currentColor">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.3-4.3"></path>
            </g>
          </svg>
        </button>

      </label>
      <a href="#" id="tambahLahanBtn"
        onclick="handleTambahLahanClick(event)"
        class="group relative overflow-hidden inline-flex items-center gap-2 bg-gradient-to-r from-[#2C8F53] to-[#2C8F53] text-white px-6 py-2 rounded-lg shadow-lg hover:from-[#1D6034] hover:to-[#1D6034] transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="absolute inset-0 bg-white opacity-0 transition duration-300 rounded-lg" id="rippleEffect"></span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
        <div id="map" class="h-[500px] w-full rounded-lg border-none rounded-xl overflow-hidden shadow-md"></div>
      </div>

      <!-- DETAIL (1 kolom) -->
      <div class="w-full">
        <div id="info-panel" class="bg-[#ffff] text-white shadow-md rounded-lg p-4 hover-gelap">
          <h2 class="poppins-bold text-xl text-[#4E4E4E] md:text-xl mb-2">
            Detail Tempat
          </h2>
          <div id="info" class="text-[#4B5563] bg-white">
            <!-- Detail lokasi akan muncul di sini -->
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="mx-auto px-4 w-full gap-6 grid sm:grid-cols-4 grid-cols-1 pt-10 pb-30" id="lahan">
    <?php if (count($lahanData) > 0): ?>
      <?php foreach ($lahanData as $lahan): ?>
        <a href="detailLahan.php?id=<?= $lahan['id'] ?>"
          class="block rounded-lg p-4 shadow-xs shadow-sm bg-cover bg-center hover-gelap text-[#4E4E4E]"
          style="background-image: url('../asset/icon/bgcard.svg'); box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">

          <img
            alt=""
            src="uploads/<?= htmlspecialchars($lahan['foto_lahan']) ?>"
            class="h-56 w-full rounded-md object-cover" />

          <div class="mt-2 bg-[#ffff] rounded-lg p-3 shadow-lg">
            <dl>
              <div>
                <dt class="sr-only">Lokasi Lahan</dt>

                <dd class="text-sm"><?= htmlspecialchars($lahan['tempat_lahan']) ?></dd>
              </div>

              <div>
                <dt class="sr-only">Nama Lahan</dt>

                <dd class="font-bold text-lg nama_lahan"><?= htmlspecialchars($lahan['nama_lahan']) ?></dd>
              </div>
            </dl>

            <dl class="mt-3 flex gap-4 lg:gap-6">
              <div class="flex items-center gap-2">
                <dt class="">
                  <span class="sr-only"> Tanggal Tanam </span>

                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-5">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                  </svg>
                </dt>

                <dd class="text-xs"><?= htmlspecialchars(date("d/m/Y", strtotime($lahan['mulai_tanam']))) ?></dd>
              </div>
            </dl>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-gray-600 text-sm">Belum ada data lahan.</div>
    <?php endif; ?>


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
      <a href="notifikasi.php" class="group flex flex-col items-center justify-center py-2 relative hover:text-[#1D6034] transition-all">
        <div class="relative">
          <i class="fi fi-ss-bell text-lg"></i>
          <span id="notif-badge" class="absolute -top-1 -right-2 bg-red-500 text-white rounded-full px-1 text-[10px] hidden">0</span>
        </div>
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
      <a href="edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <i class="fi fi-ss-book-open-cover text-lg"></i>
        <span>Edukasi</span>
      </a>
      <!-- Settings -->
      <a href=" profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
        <i class="fi fi-sr-user text-lg"></i>
        <span>Profil</span>
      </a>
    </div>
  </div>
  <script src="../javascript/lahan.js"></script>
  <script src="../javascript/other.js"></script>
  <script>
    function handleTambahLahanClick(event) {
      event.preventDefault();

      const button = document.getElementById("tambahLahanBtn");
      const ripple = document.getElementById("rippleEffect");

      // Nonaktifkan tombol sementara (opsional)
      button.disabled = true;

      // Tampilkan efek ripple putih (opsional)
      ripple.classList.remove("opacity-0");
      ripple.classList.add("opacity-20");

      // Redirect ke halaman form setelah efek sebentar
      setTimeout(() => {
        window.location.href = "formLahan.php";
      }, 200);
    }

    function cekNotifBadge() {
      fetch("function/getNotif.php")
        .then(res => res.json())
        .then(data => {
          const badge = document.getElementById("notif-badge");
          if (data.total > 0) {
            badge.innerText = "•";
            badge.style.display = "inline";
          } else {
            badge.style.display = "none";
          }
        });
    }

    setInterval(cekNotifBadge, 10000);
    cekNotifBadge();


    function searchLahan() {
      let input = document.getElementById("searchInput").value.toLowerCase();
      let rows = document.querySelectorAll("#lahan a");

      rows.forEach(function(row) {
        let namaEl = row.querySelector(".nama_lahan");
        if (!namaEl) return; // skip kalau tidak ada

        let nama = namaEl.textContent.toLowerCase();

        if (nama.includes(input)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }
  </script>


</body>

</html>