<?php
include 'db.php';

// Ambil data user ID 1 (sementara ditembak manual)
$query = mysqli_query($conn, "SELECT * FROM user WHERE id = 1");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- CDN Icon (gunakan jika fi-* tidak muncul) -->
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-straight/css/uicons-regular-straight.css">
</head>
<body class="bg-white min-h-screen flex flex-col">

  <!-- Container Full Width -->
  <div class="w-full px-4 py-10 sm:px-6 md:px-10 lg:px-20 xl:px-40">
    <div class="text-center mb-8">
      <h1 class="text-3xl md:text-4xl font-semibold">PROFILE</h1>
    </div>

    <!-- Profil Section -->
    <div class="flex flex-col items-center">
      <!-- Foto Profil -->
      <div class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-gray-300 mb-4"></div>

      <!-- Nama dan Email -->
      <h2 class="text-xl md:text-2xl font-semibold"><?= htmlspecialchars($user['nama']) ?></h2>
      <p class="text-gray-500 text-sm md:text-base"><?= htmlspecialchars($user['email']) ?></p>
    </div>

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


    <!-- Bottom Navigation -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
      <div class="grid grid-cols-5 text-center text-xs text-gray-500">
          <!-- Home -->
          <a href="index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all active-nav">
              <i class="fi fi-sr-home text-lg text-blue-600"></i>
              <span class="text-blue-600">Dashboard</span>
          </a>

          <!-- Notifikasi -->
          <a href="#" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
              <i class="fi fi-ss-bell text-lg"></i>
              <span>Notifikasi</span>
          </a>

          <!-- Lahan -->
          <a href="php/lahan.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
              <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg">
                  <i class="fi fi-sr-land-layers text-xl"></i>
              </div>
              <span class="mt-1 text-blue-600">Lahan</span>
          </a>

          <!-- Edukasi -->
          <a href="search.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
              <i class="fi fi-ss-book-open-cover text-lg"></i>
              <span>Edukasi</span>
          </a>

          <!-- Profil -->
          <a href="settings.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
              <i class="fi fi-sr-user text-lg"></i>
              <span>Profil</span>
          </a>
      </div>
    </div>
  </div>

</body>
</html>
