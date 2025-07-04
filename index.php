<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Tanduria</title>
</head>

<body>
    <!-- DASHBOARD -->

    <!-- perkiraan cuaca -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 px-2 mx-auto">
        <!-- Panel Info Utama: Lokasi, Tanggal, Sapaan -->
        <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
            <div class="flex flex-col">
                <h2 class="text-lg sm:text-xl text-gray-500 font-medium">Sukatani</h2>
                <div class="flex items-center mt-1">
                    <span class="text-5xl sm:text-6xl font-bold">30</span>
                    <div class="ml-2 text-lg sm:text-xl text-gray-600">
                        <p>Jumat</p>
                        <p>June</p>
                    </div>
                </div>
                <p class="mt-4 text-gray-800 font-medium sm:text-xl">Selamat Siang, Kajung</p>
            </div>
        </div>

        <!-- Panel Cuaca -->
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center justify-center text-center">
            <img src="asset/content/sun.png" alt="Cuaca" class="w-26 h-26 mb-2">
            <p class="text-lg sm:text-xl font-semibold text-gray-700">Cerah</p>
            <p class="text-lg sm:text-xl text-gray-600">30°C</p>
        </div>
    </div>

    <!-- Prediksi Cuaca -->
    <div class="grid grid-cols-4 gap-2 mx-auto px-2 mt-2 lg:grid-cols-4 lg:gap-8">
        <div class="h-32 sm:h-40 rounded items-center justify-center bg-white shadow p-4 flex flex-col items-center">
            <img src="asset/content/sun.png" alt="Cuaca" class="w-18 h-18 mb-2">
            <p class="text-sm sm:text-lg font-semibold text-gray-700">Cerah</p>
            <p class="text-sm sm:text-lg text-gray-600">30°C</p>
        </div>
        <div class="h-32 sm:h-40 rounded items-center justify-center bg-white shadow p-4 flex flex-col items-center">
            <img src="asset/content/sun.png" alt="Cuaca" class="w-18 h-18 mb-2">
            <p class="text-sm sm:text-lg font-semibold text-gray-700">Cerah</p>
            <p class="text-sm sm:text-lg text-gray-600">30°C</p>
        </div>
        <div class="h-32 sm:h-40 rounded items-center justify-center bg-white shadow p-4 flex flex-col items-center">
            <img src="asset/content/sun.png" alt="Cuaca" class="w-18 h-18 mb-2">
            <p class="text-sm sm:text-lg font-semibold text-gray-700">Cerah</p>
            <p class="text-sm sm:text-lg text-gray-600">30°C</p>
        </div>
        <div class="h-32 sm:h-40 rounded items-center justify-center bg-white shadow p-4 flex flex-col items-center">
            <img src="asset/content/sun.png" alt="Cuaca" class="w-18 h-18 mb-2">
            <p class="text-sm sm:text-lg font-semibold text-gray-700">Cerah</p>
            <p class="text-sm sm:text-lg text-gray-600">30°C</p>
        </div>
    </div>

    <!-- Chart -->
    <!-- Chart Container -->
    <div>
        <div class="max-w-4xl mx-auto p-4 bg-white rounded-xl shadow">
            <canvas id="lahanChart" height="200"></canvas>
        </div>

        <!-- Modal -->
        <div id="faseModal" class="fixed inset-0 z-50 bg-black/50 hidden items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-80 shadow-lg">
                <h2 class="text-xl font-bold mb-2" id="modalTitle">Info Lahan</h2>
                <p id="faseText" class="text-gray-700">Loading...</p>
                <button onclick="closeModal()" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Tutup</button>
            </div>
        </div>
    </div>

    <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-2 mx-auto">
        <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
            <div class="card bg-base-100">
                <figure class="px-10 pt-10">
                    <img
                        src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
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
            <div class="card bg-base-100">
                <figure class="px-10 pt-10">
                    <img
                        src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                        alt="Shoes"
                        class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Harga Padi</h2>
                    <p>title and actions parts</p>
                </div>
            </div>
        </a>
         <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
            <div class="card bg-base-100">
                <figure class="px-10 pt-10">
                    <img
                        src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                        alt="Shoes"
                        class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Card Title</h2>
                    <p>title and actions parts</p>
                </div>
            </div>
        </a>
        <a href="" class="w-42 sm:w-50 shadow-sm rounded-lg mx-auto">
            <div class="card bg-base-100">
                <figure class="px-10 pt-10">
                    <img
                        src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                        alt="Shoes"
                        class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Card Title</h2>
                    <p>title and actions parts</p>
                </div>
            </div>
        </a>

    </div>






    <script src="javascript/chart.js"></script>
</body>

</html>