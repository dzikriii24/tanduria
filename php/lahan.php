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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Anda bisa menambahkan custom font atau style di sini jika perlu */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-white">

    <div class="container mx-auto max-w-md p-6 pb-24"> <header class="flex justify-between items-center mb-6">
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

    <nav class="fixed bottom-0 left-0 right-0 bg-rose-200 text-gray-700 shadow-[0_-2px_5px_rgba(0,0,0,0.1)]">
        <div class="flex justify-around items-center max-w-md mx-auto p-4">
            <a href="#" class="text-center hover:text-black">
                <span class="text-sm font-semibold">logo home</span>
            </a>
            <a href="#" class="text-center hover:text-black">
                <span class="text-sm font-semibold">logo lahan</span>
            </a>
            <a href="#" class="text-center hover:text-black">
                <span class="text-sm font-semibold">logo profile</span>
            </a>
        </div>
    </nav>

</body>
</html>