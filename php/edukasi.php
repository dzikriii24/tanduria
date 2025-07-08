<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <title>Tanduria</title>
</head>
<body>
    <body class="bg-white text-gray-800">
    <!-- About Section -->
    <section class="py-12 px-4">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-green-600 font-bold uppercase text-sm mb-2">Edukasi</h2>
                <h1 class="text-3xl font-bold mb-4">Selamat Datang di Tanduria!</h1>
                <p class="mb-4 text-sm">Halaman edukasi ini hadir untuk membantumu memahami lebih dalam tentang budidaya padi — mulai dari penanaman, pemupukan, hingga panen. Semua informasi kami sajikan secara praktis dan mudah dipahami untuk mendukung pertanian yang berkelanjutan.</p>
                <!-- <button class="bg-green-600 text-white px-4 py-2 rounded">Read More</button> -->
            </div>
            <img src="https://images.unsplash.com/photo-1598550437137-21d6d89db0ec" class="w-full h-auto rounded-lg shadow-md" alt="Gardening">
        </div>
    </section>

    <!-- Info Sections -->
    <?php
    $sections = [
        [
            "title" => "Tahapan Budidaya Padi",
            "text" => "Budidaya padi terdiri dari beberapa tahap penting: pengolahan lahan, penyemaian, penanaman, pemeliharaan, hingga panen. Setiap tahap membutuhkan teknik yang tepat agar hasil maksimal.",
            "detail" => "Tahapan budidaya padi dimulai dari pengolahan lahan, yang dilakukan dengan mencangkul atau membajak sawah agar tanah menjadi gembur. Setelah itu, benih disemai dan ditanam saat bibit berusia 20–25 hari. Pemeliharaan meliputi pengairan teratur, pemupukan, dan pengendalian hama hingga masa panen.",
            "img" => "https://images.unsplash.com/photo-1603124898223-92fcd112f954"
        ],
        [
            "title" => "Pemupukan dan Nutrisi Tanaman Padi",
            "text" => "Padi membutuhkan unsur hara seperti nitrogen (N), fosfor (P), dan kalium (K). Pemupukan yang tepat waktu dan dosis sesuai sangat berpengaruh terhadap pertumbuhan dan hasil panen.",
            "detail" => "Pemupukan padi dilakukan dengan memberikan pupuk organik dan anorganik sesuai kebutuhan tanaman. Pupuk NPK (Nitrogen, Fosfor, Kalium) biasanya diberikan saat tanam dan menjelang panen. Selain itu, pemupukan foliar juga dapat dilakukan untuk memberikan nutrisi tambahan.",
            "img" => "https://images.unsplash.com/photo-1576267423445-b2e0074d68a6"
        ],
        [
            "title" => "Mengatasi Hama dan Penyakit Padi",
            "text" => "Hama seperti wereng, penggerek batang, dan tikus bisa menurunkan hasil panen. Pencegahan dini dan penggunaan pestisida alami menjadi solusi ramah lingkungan.",
            "detail" => "Pengendalian hama dan penyakit pada padi dapat dilakukan dengan cara mekanis, biologis, dan kimia. Penggunaan pestisida alami seperti neem oil dan insektisida nabati lainnya juga dapat menjadi alternatif yang ramah lingkungan.",
            "img" => "https://images.unsplash.com/photo-1599158141120-90f8e3a579c5"
        ],
        [
            "title" => "Inovasi Teknologi di Pertanian Padi",
            "text" => "Teknologi seperti drone pemantau lahan, sistem tanam jajar legowo, dan aplikasi digital seperti Tanduria dapat membantu petani meningkatkan produktivitas dan efisiensi kerja.",
            "detail" => "Inovasi teknologi di pertanian padi mencakup penggunaan drone untuk pemantauan lahan, sistem tanam jajar legowo yang meningkatkan efisiensi ruang tanam, serta aplikasi digital seperti Tanduria yang membantu petani dalam perencanaan dan pengelolaan tanaman.",
            "img" => "https://images.unsplash.com/photo-1589998059171-988d89c8db72"
        ],
        [
            "title" => "Waktu Tanam yang Tepat",
            "text" => "Menyesuaikan musim tanam dengan kondisi iklim sangat penting. Cuaca ekstrem atau keterlambatan tanam bisa memengaruhi hasil. Gunakan prakiraan cuaca sebagai panduan.",
            "detail" => "Waktu tanam yang tepat dapat meningkatkan hasil panen padi. Petani perlu memperhatikan pola cuaca dan memilih waktu tanam yang sesuai dengan kondisi iklim. Penggunaan teknologi seperti aplikasi cuaca dapat membantu petani dalam menentukan waktu tanam yang optimal.",
            "img" => "https://images.unsplash.com/photo-1589998059171-988d89c8db72"
        ],
        [
            "title" => "Bertani Itu Keren!",
            "text" => "Banyak anak muda kini terjun ke dunia pertanian. Dengan teknologi dan wawasan baru, pertanian menjadi ladang bisnis menjanjikan dan bermanfaat bagi lingkungan.",
            "detail" => "Bertani kini menjadi lebih menarik dengan adanya teknologi modern. Banyak anak muda yang mulai melirik pertanian sebagai peluang usaha. Dengan memanfaatkan teknologi, mereka dapat meningkatkan produktivitas dan efisiensi dalam bertani.",
            "img" => "https://images.unsplash.com/photo-1589998059171-988d89c8db72"
        ]
    ];

    foreach ($sections as $index => $sec) {
        $reverse = $index % 2 === 0 ? "" : "md:flex-row-reverse";
        echo <<<HTML
        <section class="py-10 px-4">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row $reverse gap-10 items-center">
                <div class="md:w-1/2 order-2 md:order-1">
                    <img src="{$sec['img']}" class="rounded-lg shadow-md w-full h-auto" alt="{$sec['title']}">
                </div>
                <div class="md:w-1/2 order-1 md:order-2">
                    <h2 class="text-xl font-bold mb-2">{$sec['title']}</h2>
                    <p class="mb-4 text-sm">{$sec['text']}</p>
                    <button class="bg-green-600 text-white px-4 py-2 rounded"
                        data-modal-title="{$sec['title']}"
                        data-modal-text="{$sec['detail']}">Read More</button>
                </div>
            </div>
        </section>
        HTML;
    }
    ?>

    <footer class="bg-green-600 h-40 text-white text-center py-6 mt-10">
        <p style="margin-bottom: 400px;">&copy; <?= date("Y") ?> Tanduria. All rights reserved.</p>
    </footer>

    <!-- Modal Template -->
    <div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-xl w-full p-6 rounded-lg shadow-lg relative">
        <button id="modal-close" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
        <h2 id="modal-title" class="text-xl font-bold mb-2"></h2>
        <p id="modal-text" class="text-sm text-gray-700"></p>
    </div>
    </div>





    <!-- Footer Navigasi -->
  <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-gray-200">
        <div class="grid grid-cols-5 text-center text-xs text-gray-500">
            <!-- Home -->
            <a href="index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all active-nav">
                <i class="fi fi-sr-home text-lg text-blue-600"></i>
                <span>Dashboard</span>
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
                <span class="mt-1">Lahan</span>
            </a>

            <!-- Edukasi -->
            <a href="../php/edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span class="text-blue-600">Edukasi</span>
            </a>
            <!-- Settings -->
            <a href="../php/profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-blue-600 transition-all">
                <i class="fi fi-sr-user text-lg"></i>
                <span>Profil</span>
            </a>
        </div>
    </div>
    
        <script src="../javascript/modal.js"></script>

</body>
</html>