<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

require '../php/db.php';    
    
$query = "
SELECT r.*, k.user_id 
FROM response r
JOIN konsultasi k ON r.id_konsultasi = k.id
WHERE k.user_id = ?
ORDER BY r.waktu_input DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md overflow-hidden mb-5 p-6">
            <h3 class="text-xl font-semibold text-green-700 mb-2"><?= htmlspecialchars($row['nama_masalah']) ?></h3>
            <p class="text-gray-700 text-sm mb-2"><?= nl2br(htmlspecialchars($row['detail_masalah'])) ?></p>
            <div class="bg-green-50 border-l-4 border-green-600 p-4 text-sm text-green-900 rounded">
                <strong>Cara Mengatasi:</strong><br>
                <?= nl2br(htmlspecialchars($row['cara_mengatasi'])) ?>
            </div>
            <p class="text-right text-xs text-gray-400 mt-2"><?= date('d M Y H:i', strtotime($row['waktu_input'])) ?></p>
        </div>
    <?php endwhile; ?>


    <script src="javascript/chart.js"></script>
    <script src="javascript/index.js"></script>

</body>

</html>