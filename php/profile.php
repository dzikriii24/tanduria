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
<html lang="id" class="#F5F2EB">

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
  <link rel="stylesheet" href="../css/icon.css">

</head>

<body class="poppins-reguler">
  <div class="navbar shadow-sm bg-[#1D6034] text-white">
    <p class="text-xl font-semibold">Profil Anda</p>
  </div>

  <div>

    <div class="sm:flex sm:justify-between shadow-sm">

      <!-- Head Profile -->
      <div class="flex justify-self-start pb-4">
        <div class="avatar px-4 mx-auto mt-4 grid grid-cols-2">
          <div class="w-30 h-30 rounded-full cursor-pointer" onclick="showFotoModal()">
            <?php if (!empty($user['foto']) && file_exists($user['foto'])): ?>
              <img src="<?= $user['foto'] ?>" alt="Foto Profil" class="w-full h-full object-cover" id="fotoProfilSrc">
            <?php else: ?>
              <img src="uploads/default_user.jpg" alt="Default Foto" class="w-full h-full object-cover" id="fotoProfilSrc">
            <?php endif; ?>
          </div>

          <dialog id="my_modal_foto" class="modal">
            <div class="modal-box p-0 max-w-[80vw] max-h-[80vh] flex justify-center items-center">
              <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
              </form>
              <img id="modalFotoImage" src="" alt="Foto" class="max-w-full max-h-full rounded-lg">
            </div>
          </dialog>

        </div>
        <div class="collapse">
          <input type="radio" name="my-accordion-1" checked="checked" />
          <div class="collapse-title">
            <p class="font-semibold text-[#1D6034]"><?= htmlspecialchars($user['nama']) ?></p>
            <p class="text-sm font-reguler opacity-75 text-[#4E4E4E]"><?= htmlspecialchars($user['email']) ?></p>
          </div>
          <div class="collapse-content text-sm text-[#4E4E4E]"><?= htmlspecialchars($lokasi_nama) ?></div>
          <div class="mt-2 text-sm">
            <a href="update_profile.php" class="btn btn-active btn-primary btn-sm">Edit Profile</a>
          </div>

        </div>
      </div>



      <!-- Stat -->
      <div class="stats shadow mt-6 sm:mt-4 w-[42 0px] sm:w-[50%] mb-4">
        <div class="stat bg-[#A3CC5A] text-white">
          <div class="stat-figure">
            <i class="fi fi-sr-layer-plus text-xl sm:text-3xl"></i>
          </div>
          <div class="stat-title" style="color:white">Total Lahan</div>
          <div class="stat-value"><?= $totalLahan ?></div>
          <div class="stat-desc" style="color:white">Lahan</div>
        </div>

        <div class="stat bg-[#2C8F53] text-white">
          <div class="stat-figure">
            <i class="fi fi-sr-wheat-awn-circle-exclamation text-xl sm:text-3xl"></i>
          </div>
          <div class="stat-title" style="color:white">Total Panen</div>
          <div class="stat-value"><?= $jumlahPanen ?></div>
          <div class="stat-desc" style="color:white">Lahan</div>
        </div>

        <div class="stat bg-[#1D6034] text-white">
          <div class="stat-figure">
            <i class="fi fi-ss-rectangle-history-circle-plus text-xl sm:text-3xl"></i>
          </div>
          <div class="stat-title" style="color:white">Total Luas</div>
          <div class="stat-value"><?= $totalHektar ?></div>
          <div class="stat-desc" style="color:white">Hektare</div>
        </div>
      </div>


    </div>

    <div class="mt-10 mx-auto px-2 mb-40">
      <div class="flow-root">
        <dl class="-my-3 divide-y divide-gray-200 rounded border border-[#4E4E4E]/20 text-lg">
          <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
            <dt class="font-medium text-gray-900">Status</dt>

            <dd class="text-gray-700 sm:col-span-2">Petani</dd>
          </div>

          <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
            <dt class="font-medium text-gray-900">Jenis Kelamin</dt>

            <dd class="text-gray-700 sm:col-span-2"> <?= htmlspecialchars($user['jenis_kelamin']) ?></dd>
          </div>

          <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
            <dt class="font-medium text-gray-900">Nomor Telepon</dt>

            <dd class="text-gray-700 sm:col-span-2"><?= htmlspecialchars($user['no_telepon']) ?></dd>
          </div>

          <div class="grid grid-cols-1 gap-1 p-3 sm:grid-cols-3 sm:gap-4">
            <dt class="font-medium text-gray-900">Log Out</dt>

            <dd class="text-gray-700 sm:col-span-2"><button onclick="confirmLogout()"
                class="mt-4 w-30 bg-[#C23132] text-white font-medium p-2 rounded-sm text-base md:text-sm hover:bg-red-600 transition">
                Logout
              </button></dd>
          </div>
        </dl>
      </div>
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

  </div>
  <!-- SweetAlert Login Berhasil -->
  <?php if (isset($_SESSION['login_success'])): ?>
    <script>
      Swal.fire({
        title: 'Berhasil Login',
        text: 'Selamat datang!',
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