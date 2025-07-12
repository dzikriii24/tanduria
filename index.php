<?php
require_once 'php/function/weatherAPI.php';
require_once 'php/function/forecast.php';
require_once 'php/function/helper.php';
session_start();

$apikey = '2de2de72b98b6a2f84c0b9a4042c43fe';
$kota = 'Bandung';

$weather = getCurrentWeather($kota, $apikey);
$forecast = getForecast($kota, $apikey);

$cuacaSekarangURL = "https://api.openweathermap.org/data/2.5/weather?q=$kota&appid=$apikey&units=metric&lang=id";
$forecastURL = "https://api.openweathermap.org/data/2.5/forecast?q=$kota&appid=$apikey&units=metric&lang=id";

$cuacaNow = json_decode(file_get_contents($cuacaSekarangURL), true);
$forecast = json_decode(file_get_contents($forecastURL), true);

// Ambil suhu, deskripsi, icon
$suhu = round($cuacaNow['main']['temp']);
$cuaca = ucfirst($cuacaNow['weather'][0]['description']);
$icon = $cuacaNow['weather'][0]['icon'];

// Ambil 4 waktu berbeda dari forecast
$prediksi = [];
for ($i = 0; $i < 4; $i++) {
    $data = $forecast['list'][$i * 2]; // ambil data per 6 jam
    $prediksi[] = [
        'cuaca' => ucfirst($data['weather'][0]['description']),
        'suhu' => round($data['main']['temp']),
        'icon' => $data['weather'][0]['icon']
    ];
}

// Format tanggal
$hari = date("l");
$tanggal = date("j");
$bulan = date("FY");


$avg_temp = 0;
$rainy_day = 0;

for ($i = 0; $i < 8; $i++) {
    $avg_temp += $forecast['list'][$i]['main']['temp'];
    $desc = $forecast['list'][$i]['weather'][0]['main'];
    if (strtolower($desc) == "rain") $rainy_day++;
}
$avg_temp = round($avg_temp / 8);
$rekomendasi = "";

// Logika Tanam Berdasarkan Temperatur & Hujan
if ($avg_temp >= 25 && $avg_temp <= 34 && $rainy_day <= 2) {
    $rekomendasi = "🌱 Kondisi cocok untuk menanam padi!";
} elseif ($rainy_day > 4) {
    $rekomendasi = "⚠️ Terlalu sering hujan. Tunda penanaman.";
} else {
    $rekomendasi = "ℹ️ Kondisi belum ideal, perhatikan cuaca harian.";
}

$conn = new mysqli("localhost", "root", "", "tanduria");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // ⬅️ Ambil dari session
    $gejala = $_POST['gejala'] ?? '';
    $fotoName = '';

    // Upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoName = uniqid('foto_', true) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName);
    }

    // Simpan
    $stmt = $conn->prepare("INSERT INTO konsultasi (user_id, gejala, foto) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $gejala, $fotoName);
    $stmt->execute();

    header("Location: index.php?success=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en" class="bg-[#F5F2EB] overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <title>Tanduria</title>
    <style type="text/tailwind">
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="css/hover.css">

</head>

<body class="poppins-reguler">

    <!-- perkiraan cuaca -->
    <div class="grid grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-6 px-2 mx-auto mt-2 text-white">
        <div class="bg-[#1D6034] rounded-xl shadow p-4 hovers">
            <div class="flex flex-col lg:grid lg:grid-cols-2">
                <div>
                    <div class="flex gap-2">
                        <i class="fi fi-ss-marker text-lg sm:text-xl"></i>
                        <h2 class="text-sm sm:text-lg font-medium" id="kota">Memuat lokasi...</h2>
                    </div>

                    <div class="flex items-center mt-1">
                        <span class="text-5xl sm:text-6xl font-bold" id="tanggal">-</span>
                        <div class="ml-2 text-lg sm:text-xl">
                            <p id="hari">-</p>
                            <div class="flex">
                                <p id="bulan">-</p>
                                <p>&nbsp;</p>
                                <p id="tahun"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <p id="jam" class="mt-4 font-medium sm:text-4xl text-xl">00:00:00</p>
                    <p id="sapaan" class="mt-4 font-medium sm:text-xl">Selamat!</p>
                </div>

            </div>

        </div>

        <!-- Panel Cuaca -->
        <div class="hovers bg-[#1D6034] rounded-xl shadow p-4 flex flex-col items-center justify-center text-center lg:grid lg:grid-cols-2 flex justify-center">
            <div class="flex justify-center items-center flex-col">
                <h2 class="text-lg sm:text-xl  font-medium">Cuaca Hari Ini</h2>
                <p class="text-lg sm:text-xl font-semibold "><?= $cuaca ?></p>
                <p class="text-lg sm:text-xl "><?= $suhu ?>°C</p>

            </div>
            <div class="flex justify-center items-center flex-col bg-[#2C8F53] w-full rounded-xl">
                <img src="https://openweathermap.org/img/wn/<?= $icon ?>@2x.png" class="w-26 h-26 mb-2">
            </div>

        </div>
    </div>

    <!-- Perkiraan -->


    <div class="w-full overflow-x-auto mt-4 px-2 mx-auto">
        <!-- Kontainer scroll horizontal di mobile -->
        <div class="mx-auto bg-[#1D6034] text-[#ffff] p-4 rounded-xl shadow w-max sm:w-full min-w-[600px] sm:min-w-full">
            <p class="text-xl font-semibold mb-4">Perkiraan Cuaca</p>

            <!-- Kontainer scroll (khusus isi kartu-kartu) -->
            <div class="flex gap-4 overflow-x-auto px-2 pb-2 flex justify-self-center">
                <?php
                $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $index = 1;
                ?>
                <?php foreach ($prediksi as $p): ?>
                    <?php
                    $tanggal = strtotime("+$index day");
                    $hari = $hariIndo[date('w', $tanggal)];
                    $index++;
                    ?>
                    <div class="flex-shrink-0 bg-[#2C8F53] rounded-xl shadow p-4 w-32 sm:w-40 text-center hover-gelap">
                        <p class="text-sm font-medium mb-1 truncate"><?= $hari ?></p>
                        <img src="https://openweathermap.org/img/wn/<?= $p['icon'] ?>@2x.png" class="w-16 h-16 mx-auto mb-2" alt="icon cuaca">
                        <p class="text-xs sm:text-sm font-semibold leading-tight line-clamp-2"><?= $p['cuaca'] ?></p>
                        <p class="text-sm"><?= $p['suhu'] ?>°C</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>



    <!-- Kalender tanam -->
    <div class="grid grid-cols-2 mx-auto px-2 gap-2">

        <div class="block rounded-xl border border-white bg-[#B03C3C] p-4 shadow-sm sm:p-6 mt-4 text-white">
            <div class="sm:flex sm:justify-between sm:gap-4 lg:gap-6">
                <div class="sm:order-last sm:shrink-0">
                    <i class="fi fi-rs-diamond-exclamation text-xl"></i>
                </div>

                <div class="mt-4 sm:mt-0">
                    <h3 class="text-lg font-medium text-pretty">
                        Rekomendasi Tanam
                    </h3>

                    <p class="mt-1 text-sm">Rekomendasi menanam padi oleh Tanduria</p>

                    <p class="mt-4 text-sm text-pretty">
                        <?= $rekomendasi ?>
                    </p>
                </div>
            </div>

            <dl class="mt-6 flex gap-4 lg:gap-6">
                <div class="flex items-center gap-2">
                    <dt class="text-gray-700">


                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-white">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </dt>

                    <dd class="text-xs" id="fullTanggal1"></dd>
                </div>
            </dl>
        </div>

        <div class="block rounded-xl border border-white text-white p-4 shadow-sm sm:p-6 mt-4 bg-[#4E4E4E]">
            <div class="sm:flex sm:justify-between sm:gap-4 lg:gap-6">
                <div class="sm:order-last sm:shrink-0">
                    <i class="fi fi-sr-calendar-clock text-xl"></i>
                </div>

                <div class="mt-4 sm:mt-0">
                    <h3 class="text-lg font-medium text-pretty">
                        Status Kalender Tanam
                    </h3>

                    <p class="mt-1 text-sm ">Saran menanam padi oleh Tanduria</p>

                    <p class="mt-4 text-sm text-pretty" id="kalender-tanam">

                    </p>
                </div>
            </div>

            <dl class="mt-6 flex gap-4 lg:gap-6">
                <div class="flex items-center gap-2">
                    <dt class="text-gray-700">


                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-5 text-white">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </dt>

                    <dd class="text-xs" id="fullTanggal"></dd>
                </div>
            </dl>
        </div>
    </div>


    <!-- Chart Container -->
    <div class="bg-white mx-auto px-4 mt-10 rounded-xl">
        <div id="lahanChartApex"></div>

        <!-- Modal -->
        <div id="faseModal" class="hidden fixed inset-0 bg-black/50 flex justify-center items-center z-50">
            <div class="bg-white rounded-2xl p-6 shadow-xl w-[90%] max-w-md animate-fade-in-up">
                <h2 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-2"></h2>
                <p id="faseText" class="text-gray-600 whitespace-pre-line"></p>
                <div class="text-right mt-4">
                    <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fitur & Chatbot -->
    <div class="px-2 mx-auto rounded-xl">
        <div class="mt-10 mx-auto px-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:gap-8 mb-30 bg-[#2C8F53] p-6 rounded-xl">

            <div class="grid grid-cols-2 gap-4 sm:gap-8  mx-auto">
                <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
                    <div class="card bg-white hovers">
                        <figure class="px-10 pt-10">
                            <img
                                src="asset/icon/lahan.svg"
                                alt="Shoes"
                                class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">Kelola Lahan</h2>
                            <p>title and actions parts</p>
                        </div>
                    </div>
                </a>
                <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
                    <div class="card bg-white hovers">
                        <figure class="px-10 pt-10">
                            <img
                                src="asset/icon/harga.svg"
                                alt="Shoes"
                                class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">Prediksi Harga</h2>
                            <p>title and actions parts</p>
                        </div>
                    </div>
                </a>
                <a href="php/chatbot.php" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
                    <div class="card bg-white hovers">
                        <figure class="px-10 pt-10">
                            <img
                                src="asset/icon/chatbot.svg"
                                alt="Shoes"
                                class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">Chatbot</h2>
                            <p>title saasdasdasdasdas</p>
                        </div>
                    </div>
                </a>
                <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
                    <div class="card bg-white hovers">
                        <figure class="px-10 pt-10">
                            <img
                                src="asset/icon/planing.svg"
                                alt="Shoes"
                                class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">Perencanaan</h2>
                            <p>title and actions parts</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="">
                <div class="space-y-6">
                    <!-- Katalog Penyakit -->
                    <article class="overflow-hidden rounded-lg text-white bg-[#1D6034] shadow-xs px-4 mx-auto p-4">
                        <div class="">
                            <a href="#">
                                <h3 class="text-lg font-medium">
                                    Penyakit & Hama Umum
                                </h3>
                            </a>

                            <ul class="list-disc list-inside space-y-1 text-sm">
                                <li>Wereng Coklat</li>
                                <li>Blast (Hawar Daun)</li>
                                <li>Penggerek Batang</li>
                                <li>Busuk Akar</li>
                            </ul>

                            <a href="#" class="group mt-4 inline-flex items-center gap-1 text-sm font-medium text-[#fff] hover:underline">
                                Lihat Semua Daftar Penyakit

                                <span aria-hidden="true" class="block transition-all group-hover:ms-0.5 rtl:rotate-180">
                                    &rarr;
                                </span>
                            </a>
                        </div>
                    </article>

                    <!-- Form Konsultasi Manual -->
                    <div class="bg-white rounded-xl shadow p-4">
                        <h3 class="text-lg font-semibold text-[#1D6034] mb-2">📤 Konsultasi Manual</h3>
                        <form action="" id="formKonsultasi" method="POST" enctype="multipart/form-data" class="space-y-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Deskripsikan Gejalanya</legend>
                                <textarea class="textarea h-24" placeholder="Daun menguning sejak 3 hari ..." required name="gejala"></textarea>
                                <div class="label">Tunggu response di notifikasi</div>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Upload Foto</legend>
                                <input type="file" class="file-input" name="foto" accept="image/*" />
                                <label class="label">Opsional</label>
                            </fieldset>
                            <button type="submit" class="bg-[#2C8F53] hover:bg-[#1D6034] text-white px-4 py-2 rounded">
                                Kirim Konsultasi
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

    </div>




    <!-- Menu -->
    <!-- Bottom Navigation Dock -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-white">
        <div class="grid grid-cols-5 text-center text-xs text-[#4E4E4E]">
            <!-- Home -->
            <a href="index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all active-nav">
                <i class="fi fi-sr-home text-lg text-[#1D6034]"></i>
                <span class="text-[#1D6034]">Dashboard</span>
            </a>

            <!-- Bookmark -->
            <a href="php/notifikasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <i class="fi fi-ss-bell text-lg"></i>
                <span>Notifikasi</span>
            </a>

            <!-- Post -->
            <a href="php/lahan.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <div class="w-10 h-10 rounded-full bg-[#1D6034] text-white flex items-center justify-center shadow-lg">
                    <i class="fi fi-sr-land-layers text-xl"></i>
                </div>
                <span class="mt-1">Lahan</span>
            </a>

            <!-- Edukasi -->
            <a href="php/edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span>Edukasi</span>
            </a>
            <!-- Settings -->
            <a href="php/profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <i class="fi fi-sr-user text-lg"></i>
                <span>Profil</span>
            </a>
            
        </div>
    </div>

    <!-- Jotform AI Chatbot -->
    <script>
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        document.getElementById('formKonsultasi').addEventListener('submit', function(e) {
            if (!isLoggedIn) {
                e.preventDefault();
                alert("Anda harus login terlebih dahulu untuk mengirim konsultasi.");
                window.location.href = "php/login.php"; // redirect ke login jika ingin
            }
        });
    </script>











<script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>
</body>

</html>