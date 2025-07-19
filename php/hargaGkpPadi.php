<?php
$tahunSekarang = date('Y');
$provinsiDipilih = isset($_GET['provinsi']) ? $_GET['provinsi'] : '';
$tahunChart = isset($_GET['tahun_chart']) ? intval($_GET['tahun_chart']) : $tahunSekarang;
$tahunTabel = isset($_GET['tahun_tabel']) ? intval($_GET['tahun_tabel']) : $tahunSekarang;
$javaProvinces = ['Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];
$labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

function getHargaRataBerasHariIni()
{
  $url_summary = "http://127.0.0.1:5000/api/gkp-summary-latest";
  $dataSummary = json_decode(fetchData($url_summary), true);

  $totalHarga = 0;
  $jumlahProvinsi = 0;

  if (isset($dataSummary['data'])) {
    foreach ($dataSummary['data'] as $row) {
      if (isset($row['avg_price']) && is_numeric($row['avg_price']) && $row['avg_price'] > 0) {
        $totalHarga += floatval($row['avg_price']);
        $jumlahProvinsi++;
      }
    }
  }

  return $jumlahProvinsi > 0 ? round($totalHarga / $jumlahProvinsi) : 0;
}

$hargaRataHariIni = getHargaRataBerasHariIni();

function fetchData($url)
{
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
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
  <title>Prediksi Harga Beras</title>
  <style type="text/tailwind">
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/font.css">
  <link rel="stylesheet" href="../css/hover.css">
  <link rel="stylesheet" href="../css/icon.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="poppins-regular">
  <div class="navbar bg-base-100 shadow-sm">
    <div class="flex-1">
      <div class="sm:text-xl text-sm font-semibold text-[#4E4E4E]">Prediksi Harga Beras</div>
    </div>
    <div class="flex-none">
      <ul class="menu menu-horizontal px-1">
        <form action="" method="get" class="flex sm:gap-8 gap-1">
          <input type="hidden" name="tahun_tabel" value="<?= htmlspecialchars($tahunTabel) ?>">
          <select class="select select-neutral" name="provinsi" id="provinsi" onchange="this.form.submit()">
            <option disabled selected>Pilih Provinsi</option>
            <?php foreach ($javaProvinces as $prov): ?>
              <option value="<?= htmlspecialchars($prov) ?>" <?= $provinsiDipilih === $prov ? 'selected' : '' ?>>
                <?= htmlspecialchars($prov) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select class="select select-neutral" name="tahun_chart" id="tahun_chart" onchange="this.form.submit()">
            <option disabled selected>Pilih Tahun</option>
            <?php for ($i = 2026; $i >= 2021; $i--): ?>
              <option value="<?= $i ?>" <?= $tahunChart == $i ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </form>
      </ul>
    </div>
  </div>

  <!-- CHART & HARGA PADI -->
  <section class="overflow-hidden sm:grid sm:grid-cols-2 mt-4 px-2 mx-auto">
    <div>
      <div class="bg-white rounded-xl p-4 shadow-md flex flex-col lg:col-span-2">
        <div class="relative flex-grow h-[350px]">
          <div id="hargaChart" style="height: 350px;"></div>
        </div>
        <p class="text-lg text-[#4E4E4E]">Harga Beras Tahun <?= htmlspecialchars($tahunChart) ?></p>
      </div>
    </div>
    <div class="py-6 my-auto">
      <div class="mx-auto max-w-screen-xl px-4 mt-6">
        <div class="text-center">
          <h2 class="text-2xl font-extrabold text-[#1D6034] sm:text-4xl"> Rp <?= $hargaRataJanuari ? number_format($hargaRataJanuari, 0, ',', '.') : 'Data Kosong'; ?> /Kg</h2>

          <p class="mx-auto mt-4 max-w-sm text-[#4E4E4E]">
            Prediksi rata-rata harga beras di Indonesia untuk Tahun <?= htmlspecialchars($tahunChart) ?> berdasarkan data.
          </p>
        </div>
      </div>
      <div class="mx-auto max-w-screen-xl px-2 mt-6">
        <div class="text-center">
          <h2 class="text-2xl font-extrabold text-[#1D6034] sm:text-4xl">Rp. <?= number_format($hargaRataHariIni, 0, ',', '.') ?> /Kg</h2>

          <p class="mx-auto mt-4 max-w-sm text-[#4E4E4E]">
            Prediksi rata-rata harga beras di Indonesia untuk tanggal <?= date('d-m-Y'); ?> berdasarkan data terbaru.
          </p>
        </div>
      </div>
      <div class="mt-6 hidden sm:flex justify-center">
        <div class="mt-6 flex justify-center">
          <a href="../index.php">
            <button class="bg-[#2C8F53] text-white font-semibold shadow-md px-6 py-2.5 rounded-lg hover:bg-green-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 mb-10">
              Kembali ke Dashboard
            </button>
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- TABLEE -->


  <div class="mx-auto px-4 mt-4 mb-15">
    <form action="" method="get">
      <input type="hidden" name="provinsi" value="<?= htmlspecialchars($provinsiDipilih) ?>">
      <input type="hidden" name="tahun_chart" value="<?= htmlspecialchars($tahunChart) ?>">
      <select class="select select-neutral" name="tahun_tabel" id="tahun_tabel" onchange="this.form.submit()">
        <option disabled selected>Pilih Tahun</option>
        <?php for ($i = 2026; $i >= 2021; $i--): ?>
          <option value="<?= $i ?>" <?= $tahunTabel == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </form>
    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm mt-3">
      <table class="min-w-full divide-y-2 divide-gray-200">
        <thead class="ltr:text-left rtl:text-right bg-[#2C8F53]">
          <tr class="*:font-medium *:text-white">
            <th class="px-3 py-2 whitespace-nowrap">Provinsi</th>
            <?php foreach ($labels as $bln): ?>
              <th class="px-3 py-2 whitespace-nowrap"><?= $bln ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <?php foreach ($provinsiBulan as $prov => $bulanHarga): ?>
            <tr class="*:text-[#4E4E4E] *:first:font-medium">
              <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($prov) ?></td>
              <?php foreach ($bulanHarga as $harga): ?>
                <td class="px-3 py-2 whitespace-nowrap">
                  <?= $harga > 0 ? 'Rp ' . number_format($harga, 0, ',', '.') : '-' ?>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6 sm:hidden justify-center">
    <div class="mt-6 flex justify-center">
      <a href="../index.php">
        <button class="bg-[#2C8F53] text-white font-semibold shadow-md px-6 py-2.5 rounded-lg hover:bg-green-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 mb-10">
          Kembali ke Dashboard
        </button>
      </a>
    </div>
  </div>


  <script>
    const labels = <?= json_encode($labels) ?>;
    const dataChart = <?= json_encode($dataChart) ?>;

    // Format data ApexCharts tipe datetime-value array
    const dates = labels.map((label, index) => {
      return {
        x: label,
        y: dataChart[index]
      };
    });

    var options = {
      series: [{
        name: 'Harga Padi',
        data: dates
      }],
      colors: ['#2C8F53'],
      chart: {
        type: 'area',
        stacked: false,
        height: 350,
        zoom: {
          type: 'x',
          enabled: true,
          autoScaleYaxis: true
        },
        toolbar: {
          autoSelected: 'zoom'
        }
      },
      dataLabels: {
        enabled: false
      },
      markers: {
        size: 3
      },
      title: {
        text: 'Harga Padi Per Bulan',
        align: 'left'
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          inverseColors: false,
          opacityFrom: 0.5,
          opacityTo: 0,
          stops: [0, 90, 100]
        },
      },
      yaxis: {
        labels: {
          formatter: function(val) {
            return 'Rp ' + val.toLocaleString('id-ID');
          }
        },
        title: {
          text: 'Harga Padi (Rp)'
        }
      },
      xaxis: {
        type: 'category',
        labels: {
          rotate: -45
        }
      },
      tooltip: {
        shared: false,
        y: {
          formatter: function(val) {
            return 'Rp ' + val.toLocaleString('id-ID');
          }
        }
      }
    };

    var chart = new ApexCharts(document.querySelector("#hargaChart"), options);
    chart.render();
  </script>
</body>

</html>