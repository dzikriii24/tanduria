<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Ambil daftar notifikasi
$notif = [];

// 1. Respon konsultasi yang belum dibaca
$res = $conn->query("SELECT * FROM konsultasi WHERE user_id = $user_id AND status = 'respon' ORDER BY waktu_kirim DESC");
while ($row = $res->fetch_assoc()) {
    $notif[] = [
        'judul' => 'Respon Konsultasi',
        'deskripsi' => 'Konsultasi Anda sudah direspon.',
        'waktu' => $row['waktu_kirim']
    ];
}

// 2. Lahan panen
$res = $conn->query("SELECT * FROM lahan WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) >= 120 ORDER BY mulai_tanam DESC");
while ($row = $res->fetch_assoc()) {
    $notif[] = [
        'judul' => 'Lahan Siap Panen',
        'deskripsi' => $row['nama_lahan'] . ' sudah mencapai 120 hari.',
        'waktu' => $row['mulai_tanam']
    ];
}

// 3. Fase pemupukan/pestisida
$res = $conn->query("SELECT * FROM lahan WHERE user_id = $user_id");
while ($row = $res->fetch_assoc()) {
    $hari = (new DateTime())->diff(new DateTime($row['mulai_tanam']))->days;

    if (in_array($hari, [7, 25, 45, 20, 35, 50, 60])) {
        $notif[] = [
            'judul' => 'Jadwal Perawatan',
            'deskripsi' => 'Hari ke-' . $hari . ' untuk lahan ' . $row['nama_lahan'] . '. Waktunya pupuk atau pestisida.',
            'waktu' => $row['mulai_tanam']
        ];
    }
}
?>



<!DOCTYPE html>
<html lang="en" class="bg-[#F5F2EB] overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Tanduria</title>
    <style type="text/tailwind">
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/hover.css">
    <link rel="stylesheet" href="../css/icon.css">

</head>

<body class="poppins-reguler">

    <h1 class="text-xl font-bold">Notifikasi</h1>
    <div class="space-y-4 mt-4">
        <?php foreach ($notif as $n): ?>
            <div class="p-4 bg-white rounded shadow">
                <h2 class="font-semibold"><?= htmlspecialchars($n['judul']) ?></h2>
                <p><?= htmlspecialchars($n['deskripsi']) ?></p>
                <small class="text-gray-500"><?= htmlspecialchars($n['waktu']) ?></small>
            </div>
        <?php endforeach; ?>
        <?php if (empty($notif)): ?>
            <p>Tidak ada notifikasi saat ini.</p>
        <?php endif; ?>
    </div>


    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-white">
        <div class="grid grid-cols-5 text-center text-xs text-[#4E4E4E]">
            <!-- Home -->
            <a href="../index.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all active-nav">
                <i class="fi fi-sr-home text-lg"></i>
                <span class="">Dashboard</span>
            </a>

            <!-- Bookmark -->
            <a href="notifikasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
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
            <a href="edukasi.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span>Edukasi</span>
            </a>
            <!-- Settings -->
            <a href="profile.php" class="group py-2 px-3 flex flex-col items-center justify-center hover:text-[#1D6034] transition-all">
                <i class="fi fi-sr-user text-lg text-[#1D6034]"></i>
                <span class="text-[#1D6034]">Profil</span>
            </a>
        </div>
    </div>

    <script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>

</body>

</html>