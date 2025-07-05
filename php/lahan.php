<?php
// Data dummy untuk daftar lahan.
// Anda bisa mengganti array ini dengan data dari database Anda.
$daftar_lahan = [
    [
        'nama' => 'Lahan 1',
        'jenis_padi' => 'Premium',
        'mulai_tanam' => '15/05/1945'
    ],
    [
        'nama' => 'Lahan 2',
        'jenis_padi' => 'Premium',
        'mulai_tanam' => '15/05/1945'
    ],
    [
        'nama' => 'Lahan 3',
        'jenis_padi' => 'Premium',
        'mulai_tanam' => '15/05/1945'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lahan Anda</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Anda bisa menambahkan custom font atau style di sini jika perlu */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-white">

    <div class="container mx-auto max-w-md p-6 pb-24">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">LAHAN ANDA</h1>
            <button class="bg-gray-200 text-gray-700 font-semibold py-2 px-5 rounded-xl shadow-sm hover:bg-gray-300 transition-colors">
                tambah lahan
            </button>
        </header>

        <main class="space-y-4">
            <?php foreach ($daftar_lahan as $lahan): ?>
                <div class="bg-stone-200 p-6 rounded-2xl shadow-sm">
                    <div class="flex flex-col">
                        <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($lahan['nama']) ?></h2>
                        <p class="text-gray-600 mt-1">Jenis padi : <?= htmlspecialchars($lahan['jenis_padi']) ?></p>
                        <p class="text-gray-600">Mulai Tanam : <?= htmlspecialchars($lahan['mulai_tanam']) ?></p>
                        <a href="#" class="text-right font-semibold text-gray-800 mt-4 hover:text-black">
                            Lihat detail lahan &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>

    </div>

    <!-- Bottom Navigation Dock -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
        <div class="grid grid-cols-5 text-center text-xs text-gray-500">
            <!-- Home -->
            <a href="../index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all active-nav">
                <i class="fi fi-sr-home text-lg"></i>
                <span class="">Dashboard</span>
            </a>

            <!-- Bookmark -->
            <a href="" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-ss-bell text-lg"></i>
                <span>Notifikasi</span>
            </a>

            <!-- Post -->
            <a href="php/lahan.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg">
                    <i class="fi fi-sr-land-layers text-xl"></i>
                </div>
                <span class="mt-1 text-blue-800">Lahan</span>
            </a>

            <!-- Search -->
            <a href="search.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span>Edukasi</span>
            </a>
            <!-- Settings -->
            <a href="settings.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-sr-user text-lg"></i>
                <span>Profil</span>
            </a>
        </div>
    </div>

</body>

</html>