<?php
require '../php/db.php';
session_start();

$id_konsultasi = (int) ($_GET['id'] ?? $_POST['id_konsultasi'] ?? 0);

// Ambil data konsultasi jika ada ID
if ($id_konsultasi > 0) {
    $result = $conn->query("SELECT * FROM konsultasi WHERE id = $id_konsultasi");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("Data konsultasi tidak ditemukan.");
    }
} else {
    die("ID konsultasi tidak valid.");
}

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_masalah = $_POST['nama_masalah'] ?? '';
    $detail_masalah = $_POST['detail_masalah'] ?? '';
    $cara_mengatasi = $_POST['cara_mengatasi'] ?? '';

    $stmt = $conn->prepare("INSERT INTO response (id_konsultasi, nama_masalah, detail_masalah, cara_mengatasi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_konsultasi, $nama_masalah, $detail_masalah, $cara_mengatasi);
    $stmt->execute();

    $conn->query("UPDATE konsultasi SET status = 'respon', sudah_dibaca = 0 WHERE id = $id_konsultasi");

    header("Location: ../admin/index.php?id=$id_konsultasi&success=1");
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

    <a href="index.php"
        class="absolute top-4 left-4 z-50 flex items-center space-x-2 bg-white shadow-md rounded-full px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
    </a>
    <section class="overflow-hidden bg-gray-50 sm:grid sm:grid-cols-2">
        <div>

            <div class="hero bg-base-200 min-h-screen">
                <div class="hero-content flex-col">
                    <?php if (!empty($data['foto'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Gejala" class="w-80 rounded shadow">
                    <?php else: ?>
                        <p><em>Foto tidak diunggah.</em></p>
                    <?php endif; ?>

                    <div class="">
                        <p class="mb-4"><?= nl2br(htmlspecialchars($data['gejala'])) ?></p>
                        <p class="text-sm text-gray-600 mb-4 mt-10">Dikirim: <?= date('d M Y H:i', strtotime($data['waktu_kirim'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-10 px-4 mx-auto w-full">
            <form method="POST" action="solve.php">
                <input type="hidden" name="id_konsultasi" value="<?= $_GET['id'] ?>">

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Nama Masalah</legend>
                    <input type="text" class="input" name="nama_masalah" required>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Detail Masalah</legend>
                    <textarea class="textarea h-24" name="detail_masalah"></textarea>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Cara Mengatasi</legend>
                    <textarea class="textarea h-24" name="cara_mengatasi" required></textarea>
                </fieldset>

                <button type="submit" class="btn btn-soft btn-success mt-4">Kirim</button>
            </form>



        </div>


    </section>

    <script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>
</body>

</html>