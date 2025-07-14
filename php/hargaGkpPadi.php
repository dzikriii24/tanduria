<?php
$provinsiDipilih = isset($_GET['provinsi']) ? $_GET['provinsi'] : 'Jawa Barat';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : 'Januari';
$javaProvinces = ['Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];
$bulanOrder = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Tambahkan timeout agar tidak infinite
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$response_full = fetchData("http://127.0.0.1:5000/api/gkp-full-data");
$dataFull = $response_full ? json_decode($response_full, true) : [];

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<pre>JSON Error: " . json_last_error_msg() . "</pre>";
    exit;
}

if (!$dataFull || !is_array($dataFull)) {
    echo "<pre>";
    echo "RAW JSON:\n";
    var_dump($response_full);
    echo "\n\njson_decode result:\n";
    var_dump($dataFull);
    echo "</pre>";
    exit;
}
if (empty($dataFull)) {
    echo "<pre>Data kosong atau tidak terbaca dari API</pre>";
}


$labels = [];
$dataChart = [];

// Untuk chart provinsi dan bulan yang dipilih
if (is_array($dataFull)) {
    $filtered = array_filter($dataFull, function($item) use ($provinsiDipilih, $bulan) {
        return $item['province'] === $provinsiDipilih && $item['month'] === $bulan;
    });

    // Sort data berdasarkan day
    usort($filtered, function ($a, $b) {
        return $a['day'] <=> $b['day'];
    });

    foreach ($filtered as $row) {
        $labels[] = $row['day'];
        $dataChart[] = $row['price'];
    }
}

// Harga rata-rata semua provinsi
$hargaSemuaProvinsi = [];
foreach ($javaProvinces as $prov) {
    $provData = array_filter($dataFull, function ($item) use ($prov, $bulan) {
        return $item['province'] === $prov && $item['month'] === $bulan;
    });

    $hargaList = array_column($provData, 'price');
    $hargaRata = !empty($hargaList) ? array_sum($hargaList) / count($hargaList) : 0;

    $hargaSemuaProvinsi[] = [
        'provinsi' => $prov,
        'harga' => round($hargaRata),
    ];
}

// Harga rata-rata provinsi yang dipilih
$hargaRataGkp = 0;
foreach ($hargaSemuaProvinsi as $row) {
    if ($row['provinsi'] === $provinsiDipilih) {
        $hargaRataGkp = $row['harga'];
        break;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Harga GKP - Tanduria</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">
<div class="w-full max-w-7xl mx-auto p-4 md:p-6">
  <form method="get" class="mb-4">
    <label for="provinsi" class="block mb-2 font-semibold">Pilih Provinsi untuk Chart:</label>
    <select name="provinsi" id="provinsi" onchange="this.form.submit()" class="p-2 border rounded mb-2">
      <?php foreach ($javaProvinces as $prov): ?>
        <option value="<?= htmlspecialchars($prov) ?>" <?= $provinsiDipilih === $prov ? 'selected' : '' ?>>
          <?= htmlspecialchars($prov) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="bulan" class="block mb-2 font-semibold">Pilih Bulan untuk Chart:</label>
    <select name="bulan" id="bulan" onchange="this.form.submit()" class="p-2 border rounded">
      <?php foreach ($bulanOrder as $bln): ?>
        <option value="<?= htmlspecialchars($bln) ?>" <?= $bulan === $bln ? 'selected' : '' ?>>
          <?= htmlspecialchars($bln) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>

    <p>Provinsi yang dipilih: <b><?= htmlspecialchars($provinsiDipilih) ?></b></p>
    <p>Harga rata-rata GKP: Rp <?= $hargaRataGkp ? number_format($hargaRataGkp, 0, ',', '.') : 'Data tidak tersedia' ?></p>
    <div class="bg-white rounded-xl p-4 shadow-md flex flex-col lg:col-span-2">
      <p class="text-lg font-semibold mb-2">Harga Padi (Gabah Kering Panen)</p>
      <div class="relative flex-grow h-[300px]">
        <canvas id="hargaChart"></canvas>
      </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-md flex flex-col justify-center items-center">
      <p class="text-sm text-gray-600 mb-1">Harga Rata-rata Padi</p>
      <p class="text-xl font-bold text-green-700">
        Rp <?= $hargaRataGkp ? number_format($hargaRataGkp, 0, ',', '.') : 'Data Kosong'; ?>/kg
      </p>
      <p class="text-xs text-gray-500">(Bulan <?= $bulan; ?>, <?= date('Y'); ?>)</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-md lg:col-span-3">
      <h2 class="text-lg font-semibold text-gray-800 mb-1">Harga Padi Rata-rata Per Provinsi</h2>
      <p class="text-sm text-gray-500 mb-4">Update: <?= date('d F Y'); ?></p>
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-gray-600">Provinsi</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-600">Harga (Rp/kg)</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($hargaSemuaProvinsi as $item): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"><?= htmlspecialchars($item['provinsi']) ?></td>
                <td class="px-4 py-3 font-medium">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mt-6">
    <a href="../index.php"><button class="w-full bg-green-600 text-white font-semibold shadow-md py-2.5 rounded-lg hover:bg-green-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">Kembali ke Dashboard</button></a>
  </div>
</div>
<script>
const ctx = document.getElementById('hargaChart').getContext('2d');
const hargaChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Harga GKP Setiap Tiga Bulan',
        data: <?= json_encode($dataChart) ?>,
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
