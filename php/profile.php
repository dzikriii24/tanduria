<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$lokasi_nama = "Lokasi tidak diketahui";
$lat = null;
$lng = null;


// Ambil data user yang login
$id_user = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM user WHERE id = $id_user");
$user = mysqli_fetch_assoc($query);



$totalLahan = 0;
$totalHektar = 0;
$jumlahPanen = 0;

if ($id_user) {
  $stmt = $conn->prepare("SELECT luas_lahan, mulai_tanam FROM lahan WHERE user_id = ?");
  $stmt->bind_param("i", $id_user);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $totalLahan++;
    $totalHektar += (int)$row['luas_lahan'];

    if (!empty($row['mulai_tanam'])) {
      $mulaiTanam = new DateTime($row['mulai_tanam']);
      $hariIni = new DateTime();
      $selisihHari = $mulaiTanam->diff($hariIni)->days;

      if ($selisihHari >= 120) {
        $jumlahPanen++;
      }
    }
  }
}

if ($id_user) {
  $stmt = $conn->prepare("SELECT lat, lng FROM user WHERE id = ?");
  $stmt->bind_param("i", $id_user);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $lat = $row['lat'];
    $lng = $row['lng'];
  }
}

if ($lat && $lng) {
  $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lng}&format=json";

  $options = [
    'http' => [
      'header' => "User-Agent: MyApp/1.0\r\n"
    ]
  ];
  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $data = json_decode($response, true);

  if (!empty($data['display_name'])) {
    $lokasi_nama = $data['display_name'];
  }
}


?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="../css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Tanduria</title>
  <style type="text/tailwind">
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/font.css">
  <link rel="stylesheet" href="../css/hover.css">

</head>

<body class="">
  <div class="navbar bg-base-100 shadow-sm">
    <p class="text-xl">Profil Anda</p>
  </div>

  <div>

    <div class="sm:flex sm:justify-between">

      <!-- Head Profile -->
      <div class="flex justify-self-start">
        <div class="avatar px-4 mx-auto mt-4 grid grid-cols-2">
          <div class="w-30 h-30 rounded-full">
            <?php if (!empty($user['foto']) && file_exists($user['foto'])): ?>
              <img src="<?= $user['foto'] ?>" alt="Foto Profil" class="w-full h-full object-cover">
            <?php else: ?>
              <img src="uploads/default_user.jpg" alt="Default Foto" class="w-full h-full object-cover">
            <?php endif; ?>
          </div>

        </div>
        <div class="collapse">
          <input type="radio" name="my-accordion-1" checked="checked" />
          <div class="collapse-title font-semibold"><?= htmlspecialchars($user['nama']) ?> <p class="text-sm font-reguler"><?= htmlspecialchars($user['email']) ?></p></div>
          <div class="collapse-content text-sm"><?= htmlspecialchars($lokasi_nama) ?></div>
          <div class="mt-2 text-sm"></div>
          <a href="update_profile.php" class="btn btn-sm sm:w-40">Edit Profile</a>
        </div>
      </div>



      <!-- Stat -->
      <div class="stats shadow mt-10 sm:mt-4">
        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              class="inline-block h-8 w-8 stroke-current">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="stat-title">Total Lahan</div>
          <div class="stat-value"><?= $totalLahan ?></div>
          <div class="stat-desc">Lahan</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              class="inline-block h-8 w-8 stroke-current">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
          </div>
          <div class="stat-title">Total Lahan Panen</div>
          <div class="stat-value"><?= $jumlahPanen ?></div>
          <div class="stat-desc">Lahan Panen</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              class="inline-block h-8 w-8 stroke-current">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
          </div>
          <div class="stat-title">Total Luas Lahan</div>
          <div class="stat-value"><?= $totalHektar ?></div>
          <div class="stat-desc">Hektare</div>
        </div>
      </div>


    </div>







































    <!-- Container Full Width -->
    <div class="w-full px-4 py-10 sm:px-6 md:px-10 lg:px-20 xl:px-40">

      <!-- Tombol Data -->
      <div class="mt-8 space-y-4 w-full">
        <!-- Nama lengkap -->
        <!-- <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
        Nama Lengkap: <?= htmlspecialchars($user['nama']) ?>
      </button> -->

        <!-- No Telepon -->
        <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
          Nomor Telepon: <?= htmlspecialchars($user['no_telepon']) ?>
          =======
          <!-- No Telepon -->
          <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
            <?= htmlspecialchars($user['no_telepon']) ?>
          </button>

          <!-- Jenis Kelamin -->
          <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
            <?= htmlspecialchars($user['jenis_kelamin']) ?>
          </button>
      </div>

      <!-- Tombol Update Profil -->
      <div class="mt-10">


        <!-- Jenis Kelamin -->
        <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
          Jenis Kelamin: <?= htmlspecialchars($user['jenis_kelamin']) ?>
        </button>
      </div>

      <!-- Tombol Update Profil -->
      <div class="mt-10">

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
          <a href="notifikasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
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
          <a href="edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
            <i class="fi fi-ss-book-open-cover text-lg"></i>
            <span>Edukasi</span>
          </a>
          <!-- Settings -->
          <a href="profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
            <i class="fi fi-sr-user text-lg text-[#1D6034]"></i>
            <span class="text-[#1D6034]">Profil</span>
          </a>
        </div>
      </div>
    </div>
    <div class="mt-2">
      <button onclick="confirmLogout()"
        class="w-full bg-[#C23132] text-white font-medium py-3 rounded-lg text-base md:text-lg hover:bg-red-600 transition">
        Logout
      </button>
    </div>

  </div>
  <!-- SweetAlert Login Berhasil -->
  <?php if (isset($_SESSION['login_success'])): ?>
    <script>
      Swal.fire({
        title: 'Berhasil Login!',
        text: 'Selamat datang kembali!',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500
      });
    </script>
    <?php unset($_SESSION['login_success']); ?>
  <?php endif; ?>

  <script src="../javascript/other.js"></script>
</body>

</html>