<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="../css/icon.css">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
  <title>Tambah Perencanaan</title>
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

<body class="">

  <div class="navbar shadow-sm">
    <div class="navbar-start">
      <a href="perencanaan.php" class="flex items-center space-x-2 bg-[#2C8F53] shadow-md rounded-full px-3 py-2 text-white hover:bg-[#1D6034] transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
      </a>
    </div>

    <div class="navbar-end">
      <h2 class="text-xl font-semibold text-[#4E4E4E]">Tambah Perencanaan</h2>
    </div>
  </div>

  <div class="bg-white lg:grid lg:grid-cols-2 px-4 mx-auto mt-5">
    <div class="relative block h-32 lg:h-full">
      <img
        src="../asset/icon/ftbg.png"
        alt=""
        class="absolute inset-0 h-full w-full object-cover" />
    </div>

    <div class="px-4 mb-5">

      <form action="" id="formRencana" class="mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <input type="hidden" name="hargaPerKarung" id="hiddenHargaPerKarung">
          <input type="hidden" name="hargaBibitPer100m" id="hiddenHargaBibitPer100m">
          <input type="hidden" name="jenisKegiatan" value="Penanaman">

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Nama Rencana</legend>
            <input type="text" class="input" name="namaRencana" placeholder="Nandur musim depan" required />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Tanggal Mulai</legend>
            <input type="date" name="tanggalMulai" id="tanggalMulai" class="input" placeholder="" required />
            <div class="label mt-1">
              <p>Prediksi Panen</p><input type="date" name="tanggalSelesai" id="tanggalPanen" class="border border-none" readonly>
            </div>
            <p></p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Luas Lahan m²</legend>
            <input type="number" name="luasLahan" id="luasLahan" class="input" placeholder="100" required />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Daerah</legend>
            <input type="text" name="daerah" class="input" placeholder="Bandung" required />
            <p class="label">Wajib diisi</p>
          </fieldset>


          <fieldset class="fieldset">
            <legend class="fieldset-legend">Harga per Karung Pupuk (Rp)</legend>
            <input type="number" id="hargaPerKarungInput" class="input" placeholder="1000" required />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Harga Bibit per 100 m² (Rp)</legend>
            <input type="number" id="hargaBibitPer100mInput" class="input" placeholder="1000" required />
            <p class="label">Wajib diisi</p>
          </fieldset>

          <fieldset class="fieldset">
            <legend class="fieldset-legend">Catatan</legend>
            <textarea class="textarea h-18" placeholder="...." name="catatan"></textarea>
            <div class="label">Optional</div>
          </fieldset>
        </div>


        <button type="submit" class="btn btn-success">Simpan Rencana</button>
      </form>
    </div>

  </div>


  <script>
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    document.getElementById("tanggalMulai").setAttribute("min", todayStr);

    document.getElementById("tanggalMulai").addEventListener("change", function() {
      const mulai = new Date(this.value);
      if (!isNaN(mulai)) {
        const panen = new Date(mulai);
        panen.setDate(panen.getDate() + 120);
        document.getElementById("tanggalPanen").valueAsDate = panen;
      }
    });

    // Salin harga ke hidden field saat input berubah
    document.getElementById("hargaPerKarungInput").addEventListener("input", function() {
      document.getElementById("hiddenHargaPerKarung").value = this.value;
    });
    document.getElementById("hargaBibitPer100mInput").addEventListener("input", function() {
      document.getElementById("hiddenHargaBibitPer100m").value = this.value;
    });

    document.getElementById("formRencana").addEventListener("submit", function(e) {
      e.preventDefault();

      const tanggalMulai = new Date(document.getElementById("tanggalMulai").value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (tanggalMulai < today) {
        Swal.fire({
          icon: 'error',
          title: 'Tanggal Tidak Valid',
          text: 'Tanggal mulai tanam tidak boleh sebelum hari ini.',
          confirmButtonColor: '#d33'
        });
        return;
      }

      const formData = new FormData(this);

      Swal.fire({
        title: "Simpan Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
        preConfirm: () => {
          Swal.showLoading();
          return fetch("simpanPerencanaan.php", {
              method: "POST",
              body: formData,
            })
            .then(res => res.text())
            .then(response => {
              if (response.trim() === "success") {
                Swal.fire("Berhasil!", "Data berhasil disimpan.", "success")
                  .then(() => window.location.href = "perencanaan.php");
              } else {
                throw new Error(response);
              }
            })
            .catch(error => {
              Swal.fire("Gagal!", error.message, "error");
            });
        }
      });
    });
  </script>
</body>

</html>