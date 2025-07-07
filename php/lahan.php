<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Lahan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../css/icon.css">
</head>
<body class="bg-white min-h-screen flex flex-col">

  <!-- Kontainer Utama -->
  <div class="w-full px-6 py-10 sm:px-10 md:px-20 lg:px-32 xl:px-48 flex-grow">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-xl md:text-2xl font-semibold">LAHAN ANDA</h1>
      <button class="bg-gray-200 text-sm md:text-base px-4 py-2 rounded-lg hover:bg-gray-300 transition">
        tambah lahan
      </button>
    </div>

    <!-- Kartu Daftar Lahan -->
    <div class="space-y-6">
      <!-- Kartu Lahan -->
      <div class="bg-gray-200 rounded-xl p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-2">Lahan 1</h2>
        <p>Jenis padi : Premium</p>
        <p>Mulai Tanam : 15/05/1945</p>
        <div class="mt-3 text-right">
          <a href="detailLahan.php" class="text-sm text-black hover:underline flex items-center justify-end gap-1">
            Lihat detail lahan →
          </a>
        </div>
      </div>

      <!-- Duplikat kartu (bisa disesuaikan dari backend) -->
      <div class="bg-gray-200 rounded-xl p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-2">Lahan 1</h2>
        <p>Jenis padi : Premium</p>
        <p>Mulai Tanam : 15/05/1945</p>
        <div class="mt-3 text-right">
          <a href="#" class="text-sm text-black hover:underline flex items-center justify-end gap-1">
            Lihat detail lahan →
          </a>
        </div>
      </div>

      <div class="bg-gray-200 rounded-xl p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-2">Lahan 1</h2>
        <p>Jenis padi : Premium</p>
        <p>Mulai Tanam : 15/05/1945</p>
        <div class="mt-3 text-right">
          <a href="#" class="text-sm text-black hover:underline flex items-center justify-end gap-1">
            Lihat detail lahan →
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Navigasi -->
  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
        <div class="grid grid-cols-5 text-center text-xs text-gray-500">
            <!-- Home -->
            <a href="index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all active-nav">
                <i class="fi fi-sr-home text-lg text-blue-600"></i>
                <span class="text-blue-600">Dashboard</span>
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
        </div>
    </div>

</body>
</html>
