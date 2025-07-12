<?php
// Ambil data Harga GKP dan Beras dari Flask server
$flask_api_url = 'http://localhost:5000/api/bps-data';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $flask_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$scrapedData = ($response !== false) ? json_decode($response, true) : null;

$gkpData = $scrapedData['gkp'] ?? [];
$berasData = $scrapedData['beras'] ?? [];
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Harga GKP - Tanduria</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Memastikan canvas tidak "memaksa" ukuran parent */
    canvas {
      position: relative;
      height: 400px; /* Memberi tinggi yang cukup untuk desktop */
      width: 100%;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

<div>
  <iframe src="http://localhost:8501/" frameborder="0" class="w-full h-[500px]"></iframe>
</div>

<div class="w-full max-w-7xl mx-auto p-4 md:p-6">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Chart Area -->
    <div class="bg-white rounded-xl p-4 shadow-md flex flex-col lg:col-span-2">
      <p class="text-lg font-semibold mb-2">Harga GKP (Gabah Kering Panen)</p>
      <div class="relative flex-grow h-[300px]">
        <canvas id="hargaChart"></canvas>
      </div>
    </div>

    <!-- Harga Padi Terkini -->
    <div class="bg-white rounded-xl p-4 shadow-md flex flex-col justify-center items-center">
      <p class="text-sm text-gray-600 mb-1">Harga Padi GKP (Referensi)</p>
      <p class="text-xl font-bold text-green-700">Rp 5.600/kg</p>
      <p class="text-xs text-gray-500">(Data per 05 Juli 2025)</p>
    </div>

    <!-- Harga Beras Nasional -->
    <div class="bg-white rounded-xl p-4 shadow-md lg:col-span-3">
      <h2 class="text-lg font-semibold text-gray-800 mb-1">Harga Beras Nasional</h2>
      <p class="text-sm text-gray-500 mb-4">Update: <?php echo date('d F Y'); ?></p>
      <div class="overflow-x-auto">
        <?php
        if (!$berasData || empty($berasData)) {
            echo "<p class='text-red-600 text-center mt-4'>Gagal mengambil data dari API atau data tidak tersedia.</p>";
        } else {
            echo "<table class='table-auto w-full text-sm'>";
            echo "<thead class='bg-gray-100 sticky top-0'>";
            echo "<tr>
                    <th class='px-4 py-2 text-left font-semibold text-gray-600'>Kategori</th>
                    <th class='px-4 py-2 text-left font-semibold text-gray-600'>Harga (Rp/kg)</th>
                  </tr>";
            echo "</thead>";
            echo "<tbody class='bg-white divide-y divide-gray-200'>";
            foreach ($berasData as $item) {
                $kategori = htmlspecialchars($item['kategori']);
                $harga = isset($item['harga']) ? 'Rp ' . number_format($item['harga'], 0, ',', '.') : 'Tidak tersedia';
                echo "<tr class='hover:bg-gray-50'>";
                echo "<td class='px-4 py-3 whitespace-nowrap'>$kategori</td>";
                echo "<td class='px-4 py-3 whitespace-nowrap font-medium'>$harga</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
        ?>
      </div>
    </div>

  </div>

  <div class="mt-6">
    <a href="../index.php"><button class="w-full bg-green-600 text-white font-semibold shadow-md py-2.5 rounded-lg hover:bg-green-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">Kembali ke Dashboard</button></a>
  </div>

</div>


<?php
$labels = [];
$data = [];

if ($gkpData) {
    foreach ($gkpData as $item) {
        $labels[] = $item['Bulan'];
        $data[] = (float) $item['harga'];
    }
}
?>

<script>
  const ctx = document.getElementById('hargaChart').getContext('2d');
  const hargaChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [{
        label: 'Harga GKP Setiap Tiga Bulan',
        data: <?php echo json_encode($data); ?>,
        borderColor: '#ef4444',
        backgroundColor: 'rgba(239, 68, 68, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointBackgroundColor: '#ef4444',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          align: 'end',
          labels: { color: '#333', font: { size: 12 }, boxWidth: 20, padding: 20 }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Harga: Rp ' + context.parsed.y.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          ticks: { color: '#666', maxRotation: 45, minRotation: 0 }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Harga GKP (Rp)',
            color: '#333',
            font: { size: 12, weight: 'bold' }
          },
          ticks: {
            color: '#666',
            callback: function(value) {
              return 'Rp ' + (value / 1000) + 'k';
            }
          }
        }
      }
    }
  });
</script>


</body>
</html>