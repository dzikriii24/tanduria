<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;


$notif_respon = [];
$notif_panen = [];

$notif_pemupukan = [];
$notif_penyemprotan = [];
$notif_penyiraman = [];



if ($user_id > 0) {
    // Notifikasi Respon Konsultasi
    $res = $conn->query("SELECT k.gejala, k.foto, k.waktu_kirim, r.nama_masalah, r.detail_masalah, r.cara_mengatasi, r.id_konsultasi
                     FROM konsultasi k
                     JOIN response r ON k.id = r.id_konsultasi
                     WHERE k.user_id = $user_id AND r.sudah_dibaca = 0
                     ORDER BY k.waktu_kirim DESC");


    while ($row = $res->fetch_assoc()) {
        $notif_respon[] = $row;
    }

    // Notifikasi Panen
    $res2 = $conn->query("SELECT nama_lahan, mulai_tanam, DATEDIFF(NOW(), mulai_tanam) AS hari
                            FROM lahan 
                          WHERE user_id = $user_id AND DATEDIFF(NOW(), mulai_tanam) >= 120");
    while ($row = $res2->fetch_assoc()) {
        $notif_panen[] = $row;
    }

    $pemupukan = $conn->query("SELECT nama_lahan, mulai_tanam, DATEDIFF(NOW(), mulai_tanam) AS hari 
                                FROM lahan 
                                WHERE user_id = $user_id 
                                AND DATEDIFF(NOW(), mulai_tanam) IN (7, 25, 45)");
    while ($row = $pemupukan->fetch_assoc()) {
        $notif_pemupukan[] = $row;
    }

    // Penyemprotan Pestisida
    $pestisida = $conn->query("SELECT nama_lahan, mulai_tanam, DATEDIFF(NOW(), mulai_tanam) AS hari 
                            FROM lahan 
                            WHERE user_id = $user_id 
                            AND (
                                (DATEDIFF(NOW(), mulai_tanam) BETWEEN 20 AND 30) OR
                                (DATEDIFF(NOW(), mulai_tanam) BETWEEN 35 AND 50) OR
                                (DATEDIFF(NOW(), mulai_tanam) BETWEEN 50 AND 60)
                            )");

    $notif_penyemprotan = [];
    while ($row = $pestisida->fetch_assoc()) {
        $notif_penyemprotan[] = $row;
    }


    // Penyiraman → dibuat manual dari rentang hari berdasarkan fase
    $penyiraman = $conn->query("SELECT nama_lahan, mulai_tanam, DATEDIFF(NOW(), mulai_tanam) AS hari 
                                 FROM lahan 
                                 WHERE user_id = $user_id 
                                 AND DATEDIFF(NOW(), mulai_tanam) BETWEEN 0 AND 80");
    while ($row = $penyiraman->fetch_assoc()) {
        $hari = (int)$row['hari'];

        if (
            ($hari >= 0 && $hari <= 10) ||        // Fase tanam awal
            ($hari >= 10 && $hari <= 40) ||       // Fase pertumbuhan anakan
            ($hari >= 40 && $hari <= 60) ||       // Fase pemeliharaan
            ($hari >= 60 && $hari <= 80)          // Fase pengisian gabah
        ) {
            $notif_penyiraman[] = $row;
        }
    }
}
if (isset($_POST['hapus_notif_respon'])) {
    $id_konsultasi = (int) ($_POST['id_konsultasi'] ?? 0);
    if ($id_konsultasi > 0) {
        $update = $conn->query("UPDATE response SET sudah_dibaca = 1 WHERE id_konsultasi = $id_konsultasi");
        if (!$update) {
            die("Query gagal: " . $conn->error);
        }
    }
    header("Location: notifikasi.php");
    exit;
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

<body class="poppins-regular">

    <div class="navbar bg-[#1D6034] shadow-sm poppins-semibold">
        <h2 class="text-xl text-white">Notifikasi</h2>
    </div>
    <!-- Respon Konsultasi -->











    <!-- Notifikasi FIXXXX -->
    <div class="mx-auto px-4 mt-4">
        <div class="space-y-4">
            <details class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer rounded-lg" open>
                <summary class="flex items-center justify-between gap-1.5 bg-[#1D6034] p-4 rounded-lg relative">
                    <?php if (!empty($notif_respon)): ?>
                        <span class="indicator absolute -top-2 -right-2">
                            <span class="indicator-item badge badge-[#ffff] text-[#1D6034]"><?= count($notif_respon) ?></span>
                        </span>
                    <?php endif; ?>
                    <h2 class="text-lg poppins-semibold text-white">Response Konsultasi</h2>

                    <svg class="size-5 text-white shrink-0 transition-transform duration-300 group-open:-rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                </summary>

                <?php if (empty($notif_respon)): ?>
                    <div class="p-4 bg-[#2C8F53] rounded shadow text-white -mt-3">Belum ada respon konsultasi.</div>
                <?php else: ?>
                    <?php foreach ($notif_respon as $row): ?>
                        <ul class="list bg-[#2C8F53] shadow-md rounded-b-lg -mt-3 pt-3">
                            <li class="list-row text-white poppins-reguler">
                                <div class="">
                                    <?php if (!empty($row['foto']) && file_exists('../uploads/' . $row['foto'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" class="size-10 rounded-box">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div><?= htmlspecialchars($row['nama_masalah']) ?></div>
                                    <div class="text-xs font-reguler opacity-80"><?= nl2br(htmlspecialchars($row['detail_masalah'])) ?></div>
                                </div>
                                <p class="list-col-wrap text-xs">
                                    Cara mengatasi masalah :<?= nl2br(htmlspecialchars($row['cara_mengatasi'])) ?>
                                </p>
                                <p class="list-col-wrap text-xs">
                                    <?= date('d M Y H:i', strtotime($row['waktu_kirim'])) ?>
                                </p>
                                <form method="POST">
                                    <input type="hidden" name="id_konsultasi" value="<?= $row['id_konsultasi'] ?>">

                                    <button type="submit" name="hapus_notif_respon" class="btn btn-square btn-ghost hover:text-red-500">
                                        <i class="fi fi-sr-cross-circle"></i>
                                    </button>
                                </form>



                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>

            </details>

            <details class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer" open>
                <summary class="flex items-center justify-between gap-1.5 bg-[#1D6034] p-4 text-white rounded-lg relative">
                    <?php if (!empty($notif_panen)): ?>
                        <span class="indicator absolute -top-2 -right-2">
                            <span class="indicator-item badge badge-white"><?= count($notif_panen) ?></span>
                        </span>
                    <?php endif; ?>
                    <h2 class="text-lg font-medium">Lahan Panen</h2>

                    <svg class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                </summary>

                <?php if (empty($notif_panen)): ?>
                   
                    <div class="p-4 bg-[#2C8F53] rounded shadow text-white -mt-3">Belum ada lahan siap panen.</div>
                <?php else: ?>
                    <?php foreach ($notif_panen as $row): ?>
                        <ul class="list shadow-md rounded-b-lg -mt-3 text-white bg-[#2C8F53] pt-3">
                            <li class="list-row">
                                <div>
                                    <i class="fi fi-ss-shovel text-xl"></i>
                                </div>
                                <div>
                                    <div><?= htmlspecialchars($row['nama_lahan']) ?></div>
                                    <div class="text-xs font-reguler opacity-80"><?= nl2br(htmlspecialchars($row['hari'])) ?></div>
                                </div>
                                <p class="list-col-wrap text-xs">
                                    Hari ke-<?= $row['hari'] ?> dari tanam — Waktu Panen
                                </p>
                                <p class="list-col-wrap text-xs">
                                    Mulai Tanam <br><?= date('d M Y H:i', strtotime($row['mulai_tanam'])) ?>
                                </p>


                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>

            </details>

            <details class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer" open>
                <summary class="flex items-center justify-between gap-1.5 bg-[#1D6034] p-4 text-white rounded-lg relative">
                    <?php if (!empty($notif_pemupukan)): ?>
                        <span class="indicator absolute -top-2 -right-2">
                            <span class="indicator-item badge badge-white"><?= count($notif_pemupukan) ?></span>
                        </span>
                    <?php endif; ?>
                    <h2 class="text-lg font-medium">Jadwal Pemupukan Lahan</h2>

                    <svg class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                </summary>
                <?php if (empty($notif_pemupukan)): ?>
                    
                    <div class="p-4 bg-[#2C8F53] rounded shadow text-white -mt-3">Belum ada jadwal pemupukan saat ini.</div>
                    
                <?php else: ?>
                    <?php foreach ($notif_pemupukan as $item): ?>
                        <ul class="list bg-[#2C8F53] shadow-md rounded-b-lg -mt-3 text-white pt-3">

                            <li class="list-row">
                                <div>
                                    <i class="fi fi-sr-bag-seedling text-xl"></i>
                                </div>

                                <div>
                                    <div><?= htmlspecialchars($item['nama_lahan']) ?></div>

                                </div>
                                <p class="list-col-wrap text-xs">
                                    Hari ke-<?= $item['hari'] ?> dari tanam — Waktu Pemupukan
                                </p>
                              <p class="list-col-wrap text-xs">
                                    Mulai Tanam <br><?= date('d M Y H:i', strtotime($row['mulai_tanam'])) ?>
                                </p>


                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>

            </details>


            <details class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer" open>
                <summary class="flex items-center justify-between gap-1.5 bg-[#1D6034] p-4 text-white rounded-lg relative">
                    <?php if (!empty($notif_penyiraman)): ?>
                        <span class="indicator absolute -top-2 -right-2">
                            <span class="indicator-item badge badge-white"><?= count($notif_penyiraman) ?></span>
                        </span>
                    <?php endif; ?>
                    <h2 class="text-lg font-medium">Jadwal Penyiraman Lahan</h2>

                    <svg class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                </summary>

                <?php if (empty($notif_penyiraman)): ?>
                 
                    <div class="p-4 bg-[#2C8F53] rounded shadow text-white -mt-3">Belum ada jadwal penyiraman saat ini.</div>
                <?php else: ?>
                    <?php foreach ($notif_penyiraman as $item): ?>
                        <ul class="list bg-[#2C8F53] text-white shadow-md rounded-b-lg -mt-3 pt-3">
                            <li class="list-row">
                                <div>
                                    <i class="fi fi-sr-water text-xl"></i>
                                </div>
                                <div>
                                    <div><?= htmlspecialchars($item['nama_lahan']) ?></div>

                                </div>
                                <p class="list-col-wrap text-xs">
                                    Hari ke-<?= $item['hari'] ?> dari tanam — Fase Penyiraman
                                </p>
                           <p class="list-col-wrap text-xs">
                                    Mulai Tanam <br><?= date('d M Y H:i', strtotime($row['mulai_tanam'])) ?>
                                </p>




                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>

            </details>




            <details class="group [&_summary::-webkit-details-marker]:hidden cursor-pointer mb-30" open>
                <summary class="flex items-center justify-between gap-1.5 bg-[#1D6034] p-4 text-white rounded-lg relative">
                    <?php if (!empty($notif_penyemprotan)): ?>
                        <span class="indicator absolute -top-2 -right-2">
                            <span class="indicator-item badge badge-white"><?= count($notif_penyemprotan) ?></span>
                        </span>
                    <?php endif; ?>
                    <h2 class="text-lg font-medium">Penyemprotan Pestisida</h2>

                    <svg class="size-5 shrink-0 transition-transform duration-300 group-open:-rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                </summary>

                <?php if (empty($notif_penyemprotan)): ?>
                    <div class="p-4 bg-[#2C8F53] rounded shadow text-white -mt-3">Belum ada jadwal penyemprotan pestisida saat ini.</div>
                
                <?php else: ?>
                    <?php foreach ($notif_penyemprotan as $item): ?>
                        <ul class="list bg-[#2C8F53] text-white shadow-md rounded-b-lg -mt-3 pt-3">
                            <li class="list-row">
                                <div>
                                    <i class="fi fi-sr-mosquito-net text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item['nama_lahan']) ?></p>

                                </div>
                                <p class="list-col-wrap text-xs">
                                    Hari ke-<?= $item['hari'] ?> dari tanam — Waktu Penyemprotan
                                </p>
                            <p class="list-col-wrap text-xs">
                                    Mulai Tanam <br><?= date('d M Y H:i', strtotime($row['mulai_tanam'])) ?>
                                </p>

                            </li>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>

            </details>
        </div>
    </div>




























    <!-- Bottom Navigation Dock -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-md rounded-3xl shadow-lg bg-white border border-white">
        <div class="grid grid-cols-5 text-center text-xs text-[#4E4E4E]">

            <!-- Dashboard -->
            <a href="../index.php" class="group flex flex-col items-center justify-center py-2 hover:text-[#1D6034] transition-all">
                <i class="fi fi-sr-home text-lg"></i>
                <span>Dashboard</span>
            </a>

            <!-- Notifikasi -->
            <a href="notifikasi.php" class="group flex flex-col items-center justify-center py-2 relative hover:text-[#1D6034] transition-all">
                <div class="relative">
                    <i class="fi fi-ss-bell text-lg text-[#1D6034]"></i>
                    <span id="notif-badge" class="absolute -top-1 -right-2 bg-red-500 text-white rounded-full px-1 text-[10px] hidden">0</span>
                </div>
                <span class="text[#1D6034]">Notifikasi</span>
            </a>

            <!-- Lahan -->
            <a href="lahan.php" class="group flex flex-col items-center justify-center py-2 hover:text-[#1D6034] transition-all">
                <div class="w-10 h-10 rounded-full bg-[#1D6034] text-white flex items-center justify-center shadow-lg">
                    <i class="fi fi-sr-land-layers text-xl"></i>
                </div>
                <span class="mt-1">Lahan</span>
            </a>

            <!-- Edukasi -->
            <a href="edukasi.php" class="group flex flex-col items-center justify-center py-2 hover:text-[#1D6034] transition-all">
                <i class="fi fi-ss-book-open-cover text-lg"></i>
                <span>Edukasi</span>
            </a>

            <!-- Profil -->
            <a href="profile.php" class="group flex flex-col items-center justify-center py-2 hover:text-[#1D6034] transition-all">
                <i class="fi fi-sr-user text-lg"></i>
                <span>Profil</span>
            </a>

        </div>
    </div>

    <script>
        function cekNotifBadge() {
            fetch("function/getNotif.php")
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById("notif-badge");
                    if (data.total > 0) {
                        badge.innerText = data.total > 9 ? "9+" : data.total;
                        badge.style.display = "inline";
                    } else {
                        badge.style.display = "none";
                    }
                });
        }

        setInterval(cekNotifBadge, 10000);
        cekNotifBadge();
    </script>
    <script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>

</body>

</html>