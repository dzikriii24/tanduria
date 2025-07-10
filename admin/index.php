<?php
$conn = new mysqli("localhost", "root", "", "tanduria");

$result = $conn->query("SELECT id, gejala, foto, waktu_kirim FROM konsultasi ORDER BY waktu_kirim DESC");
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

    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl">Atmin</a>
        </div>
        <div class="flex gap-2">
            <input type="text" placeholder="cari laporan" class="input input-bordered w-24 md:w-auto" />
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img
                            alt="Tailwind CSS Navbar component"
                            src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                    </div>
                </div>
                <ul
                    tabindex="0"
                    class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li><a>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>



    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="mt-10 px-6 mx-auto grid grid-cols-1 sm:grid-cols-3">
            <div class="card card-side bg-base-100 shadow-sm">
                <figure class="w-40">
                    <img
                        src="../uploads/<?= htmlspecialchars($row['foto']) ?>"
                        alt="Foto Gejala" />
                </figure>
                <div class="card-body">
                    <p class="text-sm text-gray-600"><?= date('d M Y H:i', strtotime($row['waktu_kirim'])) ?></p>
                    <p class="font-semibold"><?= substr($row['gejala'], 0, 2000) ?></p>

                    <div class="card-actions justify-end">
                        <a href="solve.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>
</body>

</html>