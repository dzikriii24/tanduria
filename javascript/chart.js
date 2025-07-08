const lahanData = [{
        nama: "Lahan 1",
        tanggalTanam: "23/02/2024",
        hariKe: 120
    },
    {
        nama: "Lahan 2",
        tanggalTanam: "01/03/2024",
        hariKe: 90
    },
    {
        nama: "Lahan 3",
        tanggalTanam: "10/03/2024",
        hariKe: 60
    },
    {
        nama: "Lahan 4",
        tanggalTanam: "20/03/2024",
        hariKe: 30
    }
];
const categories = lahanData.map(lahan => `${lahan.nama}\n${lahan.tanggalTanam}`);
const hariValues = lahanData.map(lahan => lahan.hariKe);

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
                const hari = lahanData[index].hariKe;
                const fase = getFaseByHari(hari);
                const nama = lahanData[index].nama;

                document.getElementById('modalTitle').textContent = nama;
                document.getElementById('faseText').innerHTML = `Hari ke-${hari}<br>Fase: ${fase}`;
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
                    background: '#1D6034'
                }
            }
        }]
    },
    colors: ['#B03C3C']
};

const chart = new ApexCharts(document.querySelector("#lahanChartApex"), options);
chart.render();

function getFaseByHari(hari) {
    if (hari <= 20) return 'Persemaian';
    else if (hari <= 45) return 'Vegetatif';
    else if (hari <= 80) return 'Berbunga';
    else if (hari <= 120) return 'Pematangan / Menjelang Panen';
    else return 'Panen / Melebihi Siklus';
}

function closeModal() {
    document.getElementById('faseModal').classList.add('hidden');
}



// Chart GKP
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
                    label: function (context) {
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
                    callback: function (value) {
                        return 'Rp ' + (value).toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});