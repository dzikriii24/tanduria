const apiKey = "2de2de72b98b6a2f84c0b9a4042c43fe";

const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Ambil waktu lokal sekarang
const now = new Date();
document.getElementById('tanggal').innerText = now.getDate(); // Tanggal (angka)
document.getElementById('hari').innerText = hariIndo[now.getDay()]; // Hari (string)
document.getElementById('bulan').innerText = bulanIndo[now.getMonth()]; // Bulan (string)
document.getElementById('tahun').innerText = now.getFullYear();


const hari = hariIndo[now.getDay()];
const tanggal = now.getDate();
const buland = bulanIndo[now.getMonth()];
const tahun = now.getFullYear();

const fullTanggal = `${hari}, ${tanggal} ${buland} ${tahun}`;
document.getElementById('fullTanggal1').innerText = fullTanggal;
document.getElementById('fullTanggal').innerText = fullTanggal;

// Tambahkan sapaan dinamis (opsional)
function pad(n) {
    return n < 10 ? "0" + n : n;
}

function updateJam() {
    const now = new Date();
    const jam = pad(now.getHours());
    const menit = pad(now.getMinutes());
    const detik = pad(now.getSeconds());
    const jamText = `${jam}:${menit}:${detik}`;
    document.getElementById("jam").textContent = jamText;

    let sapaan = "";
    const hour = now.getHours();
    if (hour >= 4 && hour < 11) {
        sapaan = "Selamat Pagi!";
    } else if (hour >= 11 && hour < 15) {
        sapaan = "Selamat Siang!";
    } else if (hour >= 15 && hour < 18) {
        sapaan = "Selamat Sore!";
    } else {
        sapaan = "Selamat Malam!";
    }

    document.getElementById("sapaan").textContent = sapaan;
}

setInterval(updateJam, 1000);
updateJam(); // Panggil sekali agar langsung muncul

// Ambil lokasi user dan kota dari OpenWeatherMap
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude;
        const lon = pos.coords.longitude;

        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=id`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('kota').innerText = data.name;
            })
            .catch(err => {
                document.getElementById('kota').innerText = "Lokasi tidak ditemukan";
            });
    }, () => {
        document.getElementById('kota').innerText = "Izin lokasi ditolak";
    });
} else {
    document.getElementById('kota').innerText = "Browser tidak mendukung lokasi";
}
//   Musim Tanam
const bulan = new Date().getMonth() + 1;
let musim = "";

if ([10, 11, 12, 1].includes(bulan)) musim = "Musim Tanam 1 (MT1)";
else if ([2, 3, 4, 5].includes(bulan)) musim = "Musim Tanam 2 (MT2)";
else if ([6, 7, 8, 9].includes(bulan)) musim = "Musim Panen atau MT3";

document.getElementById("kalender-tanam").innerText = `Saat ini adalah ${musim}.`