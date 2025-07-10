const graphhopperApiKey = 'f5f22686-c5f8-4f07-97b2-192a043ce751';
let map; // 👈 Global agar bisa dipakai di mana saja
let routeLayer = null;

const icons = {
    lahan: L.icon({
        iconUrl: '../asset/icon/location.svg',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    }),
};

// Ambil koordinat awal (terakhir diinput user)
fetch('../php/function/getKoordinat.php')
    .then(res => res.json())
    .then(data => {
        const { koordinat_lat, koordinat_lng, nama_lahan } = data;

        map = L.map('map').setView([koordinat_lat, koordinat_lng], 18); // 👈 Disimpan ke variabel global

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: 'Tanduria',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.marker([koordinat_lat, koordinat_lng])
            .addTo(map)
            .bindPopup(`Lahan: ${nama_lahan}`).openPopup();

        // Setelah map siap, baru fetch data semua lahan
        fetchAllLahan(); // 👈 Panggil di sini agar map sudah siap
    })
    .catch(err => {
        console.error('Gagal ambil data koordinat:', err);
    });

// Fungsi ambil semua lahan dan tambahkan marker
function fetchAllLahan() {
    fetch('../php/function/getData.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(lahan => {
                const marker = L.marker([lahan.koordinat_lat, lahan.koordinat_lng], {
                    icon: icons.lahan
                }).addTo(map);

                marker.bindPopup(`
                    <strong>${lahan.nama_lahan}</strong><br>
                    Tanggal Tanam: ${lahan.mulai_tanam}
                `);

                marker.on('click', () => {
                    document.getElementById('info-panel').innerHTML = `
                        <h2 class="text-xl font-semibold text-[#1F2937]">${lahan.nama_lahan}</h2>
                        <p class="text-gray-600 text-sm">Tanggal Tanam: ${lahan.mulai_tanam}</p>
                        <p class="text-gray-600 text-sm">Koordinat: ${lahan.koordinat_lat}, ${lahan.koordinat_lng}</p>
                    `;
                });
            });
        })
        .catch(error => console.error('Gagal ambil data lahan:', error));
}

async function drawRoute(start, end) {
    const url = `https://graphhopper.com/api/1/route?point=${start.lat},${start.lng}&point=${end.lat},${end.lng}&vehicle=foot&locale=id&points_encoded=false&key=${graphhopperApiKey}`;
    const response = await fetch(url);
    const data = await response.json();

    if (!data.paths || data.paths.length === 0) {
        alert("Rute tidak ditemukan.");
        return;
    }

    if (routeLayer) {
        map.removeLayer(routeLayer);
    }

    const coords = data.paths[0].points.coordinates.map(c => [c[1], c[0]]);
    routeLayer = L.polyline(coords, {
        color: 'blue',
        weight: 4
    }).addTo(map);
    map.fitBounds(routeLayer.getBounds());

    const summary = data.paths[0];
    document.getElementById('info').innerHTML =
        `<b>Jarak:</b> ${summary.distance.toFixed(0)} m, <b>Waktu tempuh:</b> ${Math.round(summary.time / 60000)} menit`;
}

function unlockMap() {
    document.getElementById('map-lock').classList.add('hidden');
    event.target.style.display = 'none';
}
