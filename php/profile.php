<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Ambil data user yang login
$id_user = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM user WHERE id = $id_user");
$user = mysqli_fetch_assoc($query);
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
    <div class="flex justify-self-start">
      <div class="avatar px-4 mx-auto mt-4 grid grid-cols-2">
        <div class="w-30 rounded-full">
          <?php if (!empty($user['foto']) && file_exists($user['foto'])): ?>
            <img src="<?= $user['foto'] ?>" alt="Foto Profil" class="w-full h-full object-cover">
          <?php else: ?>
            <img src="uploads/default_user.jpg" alt="Default Foto" class="w-full h-full object-cover">
          <?php endif; ?>
        </div>

      </div>
      <div class="collapse">
        <input type="radio" name="my-accordion-1" checked="checked" />
        <div class="collapse-title font-semibold"><?= htmlspecialchars($user['nama']) ?></div>
        <div class="collapse-content text-sm"><?= htmlspecialchars($user['email']) ?></div>
        <a class="btn btn-sm">Edit Profile</a>
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
        </button>

        <!-- Jenis Kelamin -->
        <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">
          Jenis Kelamin: <?= htmlspecialchars($user['jenis_kelamin']) ?>
        </button>
      </div>

      <!-- Tombol Update Profil -->
      <div class="mt-10">
        <a href="update_profile.php">
          <button class="w-full bg-[#2129B3] text-white font-medium py-3 rounded-lg text-base md:text-lg hover:bg-blue-900 transition">
            Update Profil
          </button>
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
            <i class="fi fi-sr-user text-lg text-[#1D6034]"></i>
            <span class="text-[#1D6034]">Profil</span>
          </a>
        </div>
      </div>
    </div>

    <script src="../javascript/other.js"></script>
</body>

</html>