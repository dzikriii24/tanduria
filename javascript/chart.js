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

const categories = lahanData.map(
    (lahan) => `${lahan.nama}\n${lahan.tanggalTanam}`
);
const hariValues = lahanData.map(lahan => lahan.hariKe);

const options = {
    chart: {
        type: 'bar',
        height: 400,
        toolbar: {
            show: true, // ✅ Menampilkan tombol menu di pojok kanan atas chart
            tools: {
                download: true, // ✅ Aktifkan tombol download PNG/SVG/CSV
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false,
                customIcons: []
            }
        },
        events: {
            dataPointSelection: function (event, chartContext, config) {
                const index = config.dataPointIndex;
                const hari = lahanData[index].hariKe;
                const fase = getFaseByHari(hari);
                const nama = lahanData[index].nama;

                document.getElementById('modalTitle').textContent = nama;
                document.getElementById('faseText').textContent = `Hari ke-${hari}, fase: ${fase}`;
                document.getElementById('faseModal').classList.remove('hidden');
                document.getElementById('faseModal').classList.add('flex');
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
                fontSize: '12px'
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
    markers: {
        size: 5,
        colors: ['#10b981'],
        strokeColors: '#111'
    },
    annotations: {
        yaxis: [{
            y: 120,
            borderColor: '#ef4444',
            label: {
                text: 'Batas Panen',
                style: {
                    color: '#fff',
                    background: '#ef4444'
                }
            }
        }]
    },
    colors: ['#0ea5e9']
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