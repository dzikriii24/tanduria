<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['short_link'])) {
  $shortLink = trim($_POST['short_link']);

  function traceFinalRedirect($url)
  {
    $currentUrl = $url;
    while (true) {
      $ch = curl_init($currentUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
      $response = curl_exec($ch);
      preg_match('/Location:\s*(.*)/i', $response, $matches);
      curl_close($ch);
      if (!empty($matches[1])) {
        $currentUrl = trim($matches[1]);
      } else {
        break;
      }
    }
    return $currentUrl;
  }

  function extractCoordinates($url)
  {
    // 1. Format @lat,lng di path
    if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 2. Format /search/lat,lng
    if (preg_match('/\/search\/(-?\d+\.\d+),\+?(-?\d+\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 3. Format ll=lat,lng di query string
    if (preg_match('/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 4. Format !3dlat!4dlng di path /data=
    if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 5. Fallback: Cek apakah ada format lat,lng di mana saja
    if (preg_match('/(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 5. Format dir/... dengan @lat,lng di belakang
    if (preg_match('/dir\/.*\/(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 6. Format query=lat,lng
    if (preg_match('/[?&]query=(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 7. Format tempat+koordinat jadi satu (jika memang ada)
    if (preg_match('/\+(-?\d{1,3}\.\d+)\+(-?\d{1,3}\.\d+)/', $url, $matches)) {
      return ['lat' => $matches[1], 'lng' => $matches[2]];
    }

    // 8. Fallback: cari 2 angka desimal berdampingan di seluruh string (ngakalin link yang panjang dengan hash)
    if (preg_match_all('/(-?\d{1,3}\.\d{4,}),\s*(-?\d{1,3}\.\d{4,})/', $url, $matches) && count($matches[0]) > 0) {
      return ['lat' => $matches[1][0], 'lng' => $matches[2][0]];
    }
    return ['lat' => null, 'lng' => null];
  }



  $finalLink = traceFinalRedirect($shortLink);
  $coords = extractCoordinates($finalLink);

  echo json_encode([
    'final_link' => $finalLink,
    'lat' => $coords['lat'],
    'lng' => $coords['lng'],
  ]);
  exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="bg-white">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
  <title>Tambah Lahan</title>
  <style type="text/tailwind">
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/font.css">
  <link rel="stylesheet" href="css/hover.css">

</head>

<body class="poppins-regular">

  <div class="navbar bg-base-100 shadow-lg">
    <div class="navbar-start">
      <a href="lahan.php" class="flex items-center space-x-2 bg-[#2C8F53] shadow-md rounded-full px-3 py-2 text-white hover:bg-[#1D6034] transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
      </a>
    </div>
    <div class="navbar-end">
      <h2 class="text-xl font-semibold">Lahan Anda</h2>
    </div>
  </div>

  <div class="bg-white mt-3 mx-auto px-2 lg:grid lg:grid-cols-5 shadow-lg">
    <div class="relative block h-32 lg:col-span-2 lg:h-full">
      <img
        src="../asset/icon/poster.svg"
        alt=""
        class="absolute inset-0 h-full w-full object-cover" />
    </div>

    <div class="px-4 py-16 sm:px-6 lg:col-span-3 lg:px-8">

      <div class="px-4 mx-auto">

        <form id="formLahan" action="lahan.php" method="POST" enctype="multipart/form-data" onsubmit="return handleSubmit(event)" class="grid grid-cols-1 gap-8 sm:grid-cols-2">
          <!-- NAMA LAHAN -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Nama Lahan</legend>
            <input type="text" class="input" id="namaLahan" name="namaLahan" placeholder="Lahan A" />
            <p class="label">Wajib diisi</p>
          </fieldset>
          <!-- Luas Lahan -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Luas Lahan</legend>
            <input type="number" class="input" id="luasLahan" name="luasLahan" placeholder="10000" />
            <p class="label hidden" id="hasilKonversi"></p>
          </fieldset>
          <!-- Tempat Lahan -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Tempat Lahan</legend>
            <input type="text" class="input" id="tempatLahan" name="tempatLahan" placeholder="Mekaruma" />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <!-- Jenis Padi -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Jenis Padi</legend>
            <select class="select" id="jenisPadi" name="jenisPadi">
              <option value="" disabled selected>Pilih Jenis Padi</option>
              <option value="IR64">IR64</option>
              <option value="Ciherang">Ciherang</option>
              <option value="Inpari 32">Inpari 32</option>
              <option value="Pandan Wangi">Pandan Wangi</option>
            </select>
            <span class="label">Wajib diisi</span>
          </fieldset>


          <!-- Mulai Tanam -->

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Mulai Tanam</legend>
            <input type="date" class="input" id="mulaiTanam" name="mulaiTanam" placeholder="12012025" />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <!-- DESKRIPSI -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Deskripsi Lahan</legend>
            <textarea class="textarea h-24" id="deskripsiLahan" name="deskripsiLahan" placeholder="lahan ini berlokasi di surya kencana dengan luas 10 hektar"></textarea>
            <div class="label">Wajib diisi</div>
          </fieldset>



          <!-- Jenis Pestisida -->
          <fieldset class="fieldset">
            <legend class="fieldset-legend">Jenis Pestisida</legend>
            <select class="select" id="pestisida" name="pestisida">
              <option value="" disabled selected>Pilih Jenis Pestisida</option>
              <option value="">Pilih Pestisida</option>
              <option value="Organik">Organik</option>
              <option value="Kimia Sistemik">Kimia Sistemik</option>
              <option value="Kimia Kontak">Kimia Kontak</option>
              <option value="Hayati">Hayati</option>
            </select>
            <span class="label">Wajib diisi</span>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Modal Tanam</legend>
            <input class="input" type="number" id="modalTanam" name="modalTanam" placeholder="10000"></input>
            <div class="label">Wajib diisi</div>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Masukan Link G-Maps</legend>
            <input type="text" id="shortLink" class="input" placeholder="Isi disini" />
            <div class="grid grid-cols-2 gap-2">
              <a href="https://www.google.com/maps" class="btn btn-sm w-30" target="_blank">Buka Maps</a>
              <button type="button" id="convertButton" class="btn btn-sm w-30">Convert Maps</button>
            </div>

            <p class="label">Wajib diisi</p>

            <input type="text" name="link_maps" id="linkFinal" placeholder="Link G-Maps" class="input" readonly />
            <input type="text" name="lat" id="latFinal" placeholder="Latitude" class="input" readonly />
            <input type="text" name="lng" id="lngFinal" placeholder="Longtitude" class="input" readonly />
          </fieldset>

          <!-- FOTOO -->
          <div>
            <label for="fotoLahan" class="mt-2 block text-sm font-medium text-gray-700 mb-1">Foto Lahan</label>
            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer bg-white hover:bg-gray-100 transition duration-150">
              <div class="flex flex-col items-center justify-center pt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                </svg>
                <p class="text-sm text-gray-500">Klik untuk unggah foto</p>
              </div>
              <input type="file" id="fotoLahan" name="fotoLahan" accept="image/*" class="hidden" onchange="previewImage(event)">
            </label>
            <img id="preview" class="mt-3 w-full rounded-xl shadow-sm hidden max-h-64 object-cover" alt="Preview Foto Lahan">
            <div id="progressWrapper" class="w-full bg-gray-200 rounded-full h-2.5 mt-3 hidden">
              <div id="progressBar" class="bg-green-500 h-2.5 rounded-full w-0 transition-all duration-300 ease-in-out"></div>
            </div>
          </div>

          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-semibold flex items-center">
            <svg class="h-5 w-5 mr-2 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Lahan
          </button>

      </div>
    </div>





    </form>
  </div>
  </div>
  </div>


  <!-- SCRIPT -->
  <script>
    function previewImage(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('preview');
      const progressWrapper = document.getElementById('progressWrapper');
      const progressBar = document.getElementById('progressBar');

      if (file) {
        const reader = new FileReader();
        progressWrapper.classList.remove('hidden');
        progressBar.style.width = '0%';

        reader.onloadstart = () => progressBar.style.width = '10%';
        reader.onprogress = (e) => {
          if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
          }
        };
        reader.onloadend = () => {
          preview.src = reader.result;
          preview.classList.remove('hidden');
          progressBar.style.width = '100%';
          setTimeout(() => progressWrapper.classList.add('hidden'), 800);
        };
        reader.readAsDataURL(file);
      } else {
        preview.classList.add('hidden');
        progressWrapper.classList.add('hidden');
      }
    }

    // Ambil koordinat otomatis dari link maps
    document.getElementById("convertButton").addEventListener("click", function() {
      const shortLink = document.getElementById("shortLink").value;
      if (!shortLink) {
        alert("Masukkan link maps terlebih dahulu.");
        return;
      }

      fetch(window.location.href, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: "short_link=" + encodeURIComponent(shortLink)
        })
        .then(res => res.json())
        .then(data => {
          document.getElementById("linkFinal").value = data.final_link || "";
          document.getElementById("latFinal").value = data.lat || "";
          document.getElementById("lngFinal").value = data.lng || "";
        })
        .catch(err => {
          console.error(err);
          alert("Gagal convert link.");
        });
    });

    function handleSubmit(event) {
      event.preventDefault();
      const form = document.getElementById('formLahan');
      const required = ['namaLahan', 'luasLahan', 'tempatLahan', 'jenisPadi', 'mulaiTanam', 'fotoLahan', 'deskripsiLahan', 'linkFinal', 'pestisida', 'modalTanam'];
      let pesan = "";

      required.forEach(id => {
        const val = document.getElementById(id).value.trim();
        if (!val) pesan += `- ${id}\n`;
      });

      if (pesan !== "") {
        Swal.fire({
          icon: 'error',
          title: 'Data Belum Lengkap',
          text: "Mohon isi:\n" + pesan,
          confirmButtonColor: '#d33'
        });
        return false;
      }

      const inputDate = new Date(document.getElementById('mulaiTanam').value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (inputDate < today) {
        Swal.fire({
          icon: 'error',
          title: 'Tanggal Tanam Tidak Valid',
          text: 'Minimal hari ini.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      Swal.fire({
        title: 'Simpan Data?',
        text: 'Yakin ingin menyimpan data ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
          });
          setTimeout(() => {
            form.submit();
          }, 800);
        }
      });

      return false;
    }

    const inputLuas = document.getElementById("luasLahan");
    const hasilKonversi = document.getElementById("hasilKonversi");

    inputLuas.addEventListener("input", function() {
      const meterPersegi = parseFloat(this.value);

      if (!isNaN(meterPersegi) && meterPersegi > 0) {
        const hektar = (meterPersegi / 10000).toFixed(2).replace('.', ',');
        hasilKonversi.innerText = hektar + " Hektar";
        hasilKonversi.classList.remove("hidden");
      } else {
        hasilKonversi.classList.add("hidden");
        hasilKonversi.innerText = "";
      }
    });
  </script>

</body>

</html>