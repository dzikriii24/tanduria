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
        const {
            koordinat_lat,
            koordinat_lng,
            nama_lahan
        } = data;

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
                    
                                        <button onclick="foto.showModal()" style="cursor: pointer;">
                                <img
                                    src="${lahan.foto_lahan}"
                                    style="cursor: pointer; box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;"
                                    class="h-60 mx-auto w-[430px] object-cover sm:h-60 lg:h-96 rounded-lg"
                                />
                            </button>
                            

                            <dialog id="foto" class="modal">
                                <div class="modal-box w-auto max-w-[400px] p-0 overflow-hidden">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10">✕</button>
                                    </form>
                                    <img src="${lahan.foto_lahan}" alt="Gambar"
                                        class="max-w-full h-auto block rounded-lg" />
                                </div>
                            </dialog>

                            <div class="hovers mt-2 rounded-lg p-4 poppins-regular bg-[#2C8F53]" style="box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;">
                                <dl>
                                    <div>
                                        <dd class="font-medium">${lahan.nama_lahan}</dd>
                                    </div>
                                    <div>
                                        <p class="text-[12px] text-white w-full">Koordinat</p>
                                        <p class="text-[12px] text-white w-full">${lahan.koordinat_lat}, ${lahan.koordinat_lng}</p>
                                    </div>
                                </dl>

                                <div class="mt-6 flex items-center gap-8 text-xs">
                                    <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                                       <i class="fi fi-sr-land-layers text-xl"></i>
                                        <div class="mt-1.5 sm:mt-0">
                                            <p class="text-[#ffff]">Nama Lahan</p>

                                            <p class="font-lg nunito-regular text-white">${lahan.nama_lahan}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-3 gap-8 text-xs">

                                    <p class="text-[#ffff] mt-3">Tanggal Tanam</p>

                                    <p class="font-lg nunito-regular text-white">${lahan.mulai_tanam}</p>

                                     <div class="grid grid-cols-2 gap-2 mt-4">
                                        <a role="button" href='../php/detailLahan.php?id=${lahan.id} ?' class="btn text-sm text-[#1D6034]">Detail Lahan</a>
                                        <a role="button" href='${lahan.link_maps}' class="btn text-sm text-[#1D6034]">Lihat di Maps</a>
        </div>
                     
                                </div>
                            </div>
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