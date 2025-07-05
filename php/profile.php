<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
      <h2 class="text-xl md:text-2xl font-semibold">Rayap Besi</h2>
      <p class="text-gray-500 text-sm md:text-base">hidupjokowi@gmail.com</p>
    </div>

    <!-- Tombol Data -->
    <div class="mt-8 space-y-4 w-full">
      <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">Nama Lengkap</button>
      <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">Nomor Telepon</button>
      <button class="w-full border border-gray-400 rounded-lg py-3 text-base md:text-lg hover:bg-gray-100 transition">Jenis Kelamin</button>
    </div>

    <!-- Tombol Update Profil -->
    <div class="mt-10">
      <button class="w-full bg-[#2129B3] text-white font-medium py-3 rounded-lg text-base md:text-lg hover:bg-blue-900 transition">
        Update Profil
      </button>
    </div>
  </div>

</body>
</html>
