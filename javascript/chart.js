fetch('php/chart.php')
    .then(response => response.json())
    .then(data => {
        console.log(data); // pastikan datanya sudah masuk

        const categories = data.map(lahan => `${lahan.nama_lahan}`);
        const hariValues = data.map(lahan => lahan.hariKe);

        const options = {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        pan: false,
                        reset: false
                    }
                },
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        const index = config.dataPointIndex;
                        const hari = hariValues[index];
                        const fase = getFaseByHari(hari);
                        const nama = data[index].nama_lahan;

                        document.getElementById('modalTitle').textContent = nama;
                        document.getElementById('faseText').innerHTML = `Hari ke-${hari}\nFase: ${fase}`;
                        document.getElementById('faseModal').classList.remove('hidden');
                    }
                }
            },
            series: [{
                name: 'Hari Pertumbuhan',
                data: hariValues
            }],
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        fontSize: '12px',
                        whiteSpace: 'pre-line'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Hari ke-'
                    
                },
                min: 0,
                max: 130
            },
            tooltip: {
                y: {
                    formatter: (val) => `Hari ke-${val}`
                }
            },
            annotations: {
                yaxis: [{
                    y: 120,
                    borderColor: '#1D6034',
                    label: {
                        text: 'Batas Panen',
                        style: {
                            color: '#fff',
                            background: 'linear-gradient(to top, #191a19, #1e5128, #4e9f3d);'
                        }
                    }
                }]
            },
            colors: ['#2C8F53']
        };

        const chart = new ApexCharts(document.querySelector("#lahanChartApex"), options);
        chart.render();
    })
    .catch(error => console.error('Error fetching data:', error));

// Fase pertumbuhan
function getFaseByHari(hari) {
    if (hari <= 20) return 'Persemaian';
    else if (hari <= 45) return 'Vegetatif';
    else if (hari <= 80) return 'Berbunga';
    else if (hari <= 120) return 'Pematangan / Menjelang Panen';
    else return 'Panen / Melebihi Siklus';
}

// Modal close
function closeModal() {
    document.getElementById('faseModal').classList.add('hidden');
}





// // Chart GKP
// const ctx = document.getElementById('hargaChart').getContext('2d');
// const hargaChart = new Chart(ctx, {
//     type: 'line',
//     data: {
//         labels: [
//             'Jun 2023', 'Sep 2023', 'Des 2023',
//             'Mar 2024', 'Jun 2024', 'Sep 2024',
//             'Des 2024', 'Mar 2025', 'Jun 2025',
//             'Sep 2025', 'Des 2025', 'Mar 2026', 'Jun 2026'
//         ],
//         datasets: [{
//             label: 'Harga GKP per 3 Bulan',
//             data: [2000, 4000, 3500, 6000, 7500, 5000, 8500, 10000, 12000, 11500, 14000, 13500, 14500],
//             borderColor: 'red',
//             backgroundColor: 'rgba(255, 0, 0, 0.1)',
//             borderWidth: 2,
//             fill: true,
//             tension: 0.3,
//             pointRadius: 3,
//             pointHoverRadius: 5
//         }]
//     },
//     options: {
//         responsive: true,
//         maintainAspectRatio: false,
//         plugins: {
//             legend: {
//                 display: true,
//                 labels: {
//                     color: '#333',
//                     font: {
//                         size: 12
//                     }
//                 }
//             },
//             tooltip: {
//                 callbacks: {
//                     label: function (context) {
//                         return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
//                     }
//                 }
//             }
//         },
//         scales: {
//             x: {
//                 title: {
//                     display: true,
//                     text: 'Waktu (per 3 bulan)',
//                     color: '#333',
//                     font: {
//                         size: 12,
//                         weight: 'bold'
//                     }
//                 },
//                 ticks: {
//                     color: '#333'
//                 }
//             },
//             y: {
//                 beginAtZero: true,
//                 title: {
//                     display: true,
//                     text: 'Harga GKP (Rp)',
//                     color: '#333',
//                     font: {
//                         size: 12,
//                         weight: 'bold'
//                     }
//                 },
//                 ticks: {
//                     color: '#333',
//                     callback: function (value) {
//                         return 'Rp ' + (value).toLocaleString('id-ID');
//                     }
//                 }
//             }
//         }
//     }
// });