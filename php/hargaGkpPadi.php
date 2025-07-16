<?php
$tahunSekarang = date('Y');
$provinsiDipilih = isset($_GET['provinsi']) ? $_GET['provinsi'] : 'Jawa Barat';
$tahunChart = isset($_GET['tahun_chart']) ? intval($_GET['tahun_chart']) : $tahunSekarang;
$tahunTabel = isset($_GET['tahun_tabel']) ? intval($_GET['tahun_tabel']) : $tahunSekarang;
$javaProvinces = ['Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];
$labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$url_chart = "http://127.0.0.1:5000/api/gkp-per-bulan?province=" . urlencode($provinsiDipilih) . "&year=" . $tahunChart;
$dataChartResponse = json_decode(fetchData($url_chart), true);



$dataChart = [];
foreach ($labels as $bln) {
    $hargaBulan = 0;
    if (isset($dataChartResponse['data'])) {
        foreach ($dataChartResponse['data'] as $item) {
            if ($item['month'] === $bln) {
                $hargaBulan = round($item['avg_price']);
                break;
            }
        }
    }
    $dataChart[] = $hargaBulan;
}

$url_summary = "http://127.0.0.1:5000/api/gkp-summary-latest";
$dataSummary = json_decode(fetchData($url_summary), true);

$hargaSemuaProvinsi = [];
if (isset($dataSummary['data'])) {
    foreach ($dataSummary['data'] as $row) {
        $hargaSemuaProvinsi[] = [
            'provinsi' => $row['province'],
            'harga' => round($row['avg_price']),
        ];
    }
}
$url_semua_provinsi = "http://127.0.0.1:5000/api/gkp-per-bulan-semua-provinsi?year=" . $tahunTabel;
$dataSemuaProvinsiResponse = json_decode(fetchData($url_semua_provinsi), true);

$provinsiBulan = [];

if (isset($dataSemuaProvinsiResponse['data'])) {
    foreach ($dataSemuaProvinsiResponse['data'] as $row) {
        $provinsi = $row['province'];
        $bulan = $row['month'];
        $hargaRaw = $row['avg_price'];

        if (in_array($provinsi, $javaProvinces)) {
            if (
                isset($row['avg_price']) &&
                is_numeric($row['avg_price']) &&
                !is_nan(floatval($row['avg_price'])) &&
                $row['avg_price'] > 0
            ) {
                $provinsiBulan[$provinsi][$bulan] = round(floatval($row['avg_price']));
            } else {
                $provinsiBulan[$provinsi][$bulan] = 0;
            }
        }
    }

    // Lengkapi bulan yang kosong agar tabel tetap rapi
    foreach ($provinsiBulan as $prov => $bulanHarga) {
        $bulanHargaLengkap = [];
        foreach ($labels as $bln) {
            $bulanHargaLengkap[$bln] = isset($bulanHarga[$bln]) ? $bulanHarga[$bln] : 0;
        }
        $provinsiBulan[$prov] = $bulanHargaLengkap;
    }
}



$hargaRataGkp = 0;
foreach ($hargaSemuaProvinsi as $row) {
    if ($row['provinsi'] === $provinsiDipilih) {
        $hargaRataGkp = $row['harga'];
        break;
    }
}

$url_avg_januari = "http://127.0.0.1:5000/api/gkp-per-bulan-semua-provinsi?year=$tahunChart";
$dataAvgJanuari = json_decode(fetchData($url_avg_januari), true);

$hargaJanuariArray = [];

if (isset($dataAvgJanuari['data'])) {
    foreach ($dataAvgJanuari['data'] as $row) {
        if ($row['month'] === 'Januari' && in_array($row['province'], $javaProvinces)) {
            if (isset($row['avg_price']) && is_numeric($row['avg_price']) && $row['avg_price'] > 0) {
                $hargaJanuariArray[] = floatval($row['avg_price']);
            }
        }
    }
}

$hargaRataJanuari = count($hargaJanuariArray) > 0 ? round(array_sum($hargaJanuariArray) / count($hargaJanuariArray)) : 0;
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

<div class="w-full max-w-7xl mx-auto p-4 md:p-6 flex flex-col gap-6">
  <!-- Chart Section -->
  <div class="bg-white rounded-xl p-4 shadow-md flex flex-col lg:col-span-2">
    <form method="get" class="mb-4 flex flex-wrap gap-4 items-center">
        <input type="hidden" name="tahun_tabel" value="<?= htmlspecialchars($tahunTabel) ?>">

        <div class="flex items-center gap-2">
            <label for="provinsi" class="font-semibold">Provinsi:</label>
            <select name="provinsi" id="provinsi" onchange="this.form.submit()" class="p-2 border rounded">
                <?php foreach ($javaProvinces as $prov): ?>
                    <option value="<?= htmlspecialchars($prov) ?>" <?= $provinsiDipilih === $prov ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label for="tahun_chart" class="font-semibold">Tahun Chart:</label>
            <select name="tahun_chart" id="tahun_chart" onchange="this.form.submit()" class="p-2 border rounded">
                <?php for ($i = 2026; $i >= 2021; $i--): ?>
                    <option value="<?= $i ?>" <?= $tahunChart == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </form>

    <p class="text-lg font-semibold mb-2">Harga Beras Setiap Tahun <?= htmlspecialchars($tahunChart) ?></p>
    <div class="relative flex-grow h-[300px]">
        <canvas id="hargaChart"></canvas>
    </div>
  </div>

  <!-- Average Price Section -->
  <div class="bg-white rounded-xl p-4 shadow-md flex flex-col justify-center items-center">
    <p class="text-sm text-gray-600 mb-1">Harga Rata-rata Beras Tahun <?= htmlspecialchars($tahunChart) ?></p>
    <p class="text-xl font-bold text-green-700">
      Rp <?= $hargaRataJanuari ? number_format($hargaRataJanuari, 0, ',', '.') : 'Data Kosong'; ?>/kg
    </p>
  </div>

 <!-- tabel padi tiap provinsi -->
  <div class="bg-white rounded-xl p-4 shadow-md lg:col-span-3">
      <h2 class="text-lg font-semibold text-gray-800 mb-1">Harga Padi per Provinsi per Bulan</h2>
      <form method="get" class="mb-4 flex flex-wrap gap-2 items-center">
          <input type="hidden" name="provinsi" value="<?= htmlspecialchars($provinsiDipilih) ?>">
          <input type="hidden" name="tahun_chart" value="<?= htmlspecialchars($tahunChart) ?>">

          <label for="tahun_tabel" class="font-semibold">Pilih Tahun Tabel:</label>
          <select name="tahun_tabel" id="tahun_tabel" onchange="this.form.submit()" class="p-2 border rounded">
              <?php for ($i = 2026; $i >= 2021; $i--): ?>
                  <option value="<?= $i ?>" <?= $tahunTabel == $i ? 'selected' : '' ?>><?= $i ?></option>
              <?php endfor; ?>
          </select>
      </form>                             
      <!-- <p class="text-sm text-gray-500 mb-4">Update: <?= date('d F Y'); ?></p> -->
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-gray-600">Provinsi</th>
              <?php foreach ($labels as $bln): ?>
                <th class="px-4 py-2 text-left font-semibold text-gray-600"><?= $bln ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($provinsiBulan as $prov => $bulanHarga): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"><?= htmlspecialchars($prov) ?></td>
                <?php foreach ($bulanHarga as $harga): ?>
                  <td class="px-4 py-3 font-medium">
                    <?= $harga > 0 ? 'Rp ' . number_format($harga, 0, ',', '.') : '-' ?>
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
  </div>
</div>

<div class="mt-6">
  <div class="mt-6 flex justify-center">
      <a href="../index.php">
          <button class="bg-green-600 text-white font-semibold shadow-md px-6 py-2.5 rounded-lg hover:bg-green-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 mb-10">
              Kembali ke Dashboard
          </button>
      </a>
  </div>
</div>

<script>
const ctx = document.getElementById('hargaChart').getContext('2d');
const hargaChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Harga Padi',
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
              if (context.parsed.y === 0) {
                return 'Harga tidak tersedia';
              } else {
                return 'Harga: Rp ' + context.parsed.y.toLocaleString('id-ID');
              }
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
            text: 'Harga Padi (Rp)',
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
