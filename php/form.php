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

</body>
</html>
