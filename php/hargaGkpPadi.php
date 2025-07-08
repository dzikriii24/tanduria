<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Harga GKP - Tanduria</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-white text-gray-800">

  <!-- Container -->
  <div class="max-w-md mx-auto p-4">
    <p class="text-lg font-semibold mb-2">Harga GKP per 3 bulan</p>
    <!-- Chart Area -->
    <div class="bg-white rounded-xl p-2 md: shadow">
      <canvas id="hargaChart" class="w-full h-52"></canvas>
    </div>



    <!-- Harga padi terkini -->
    <div class="bg-white md: shadow text-sm text-center py-2 rounded-lg mt-2">
      Harga padi saat ini: <span class="font-semibold text-green-700">Rp 5.600/kg</span> <br>
      (per 5 Juli 2025) <br>
      halah nyocot
    </div>

    <!-- Tombol Kembali Ke Dashboard Jang-->
    <div class="mt-4">
      <button class="w-500px h-100px bg-white font-semibold shadow py-2 rounded-lg hover:bg-gray-100 transition duration-200">
        Jangankan ewe
      </button>
    </div>

  </div>

  <?php
  // Ambil tanggal hari ini
  function getHargaBerasHariIni()
  {
    $tanggal = date('d/m/Y');
    $period = urlencode("$tanggal - $tanggal");

    $url = "https://api-panelhargav2.badanpangan.go.id/api/front/harga-peta-provinsi?level_harga_id=1&komoditas_id=2&period_date=$period&multi_status_map[0]=&multi_province_id[0]=";

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTPHEADER => array(
        'User-Agent: Mozilla/5.0',
        'Accept: application/json'
      )
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode !== 200 || !$response) {
      return null;
    }

    return json_decode($response, true);
  }


  // Cek jika API tidak bisa diakses
  $data = getHargaBerasHariIni();

  if (!$data || !isset($data['data'])) {
    echo "<p class='text-red-600'>Gagal mengambil data dari API.</p>";
    exit;
  }


  echo "<div class='w-full max-w-4xl'>";
  echo "<h1 class='text-2xl font-bold text-green-800 mb-4'>Harga Beras Nasional Hari Ini ($tanggal)</h1>";
  echo "<table class='table-auto w-full bg-white shadow-md rounded-lg overflow-hidden'>";
  echo "<thead class='bg-green-600 text-white'>";
  echo "<tr>
        <th class='px-4 py-2 text-left'>Provinsi</th>
        <th class='px-4 py-2 text-left'>Harga (Rp/kg)</th>
      </tr>";
  echo "</thead>";
  echo "<tbody>";

  foreach ($data['data'] as $item) {
    $provinsi = $item['province_name'];
    $harga = isset($item['harga']) ? number_format($item['harga'], 0, ',', '.') : 'Tidak tersedia';

    echo "<tr class='border-b hover:bg-green-100'>";
    echo "<td class='px-4 py-2'>$provinsi</td>";
    echo "<td class='px-4 py-2'>Rp $harga</td>";
    echo "</tr>";
  }


  echo "</tbody>";
  echo "</table>";
  echo "</div>";
  ?>

  <!-- Chart Script -->
  <script>
    const ctx = document.getElementById('hargaChart').getContext('2d');
    const hargaChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [
          'Jun 2023', 'Sep 2023', 'Des 2023',
          'Mar 2024', 'Jun 2024', 'Sep 2024',
          'Des 2024', 'Mar 2025', 'Jun 2025',
          'Sep 2025', 'Des 2025', 'Mar 2026', 'Jun 2026'
        ],
        datasets: [{
          label: 'Harga GKP per 3 Bulan',
          data: [2000, 4000, 3500, 6000, 7500, 5000, 8500, 10000, 12000, 11500, 14000, 13500, 14500],
          borderColor: 'red',
          backgroundColor: 'rgba(255, 0, 0, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointRadius: 3,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            labels: {
              color: '#333',
              font: {
                size: 12
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
              }
            }
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Waktu (per 3 bulan)',
              color: '#333',
              font: {
                size: 12,
                weight: 'bold'
              }
            },
            ticks: {
              color: '#333'
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Harga GKP (Rp)',
              color: '#333',
              font: {
                size: 12,
                weight: 'bold'
              }
            },
            ticks: {
              color: '#333',
              callback: function(value) {
                return 'Rp ' + (value).toLocaleString('id-ID');
              }
            }
          }
        }
      }
    });
  </script>

  <script src="../javascript/chart.js"></script>

</body>

</html>