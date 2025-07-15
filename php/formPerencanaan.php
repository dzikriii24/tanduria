<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Perencanaan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-4xl mx-auto p-8 mt-10 bg-white shadow-lg rounded-2xl">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Perencanaan</h2>

    <form id="formRencana" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <input type="hidden" name="hargaPerKarung" id="hiddenHargaPerKarung">
      <input type="hidden" name="hargaBibitPer100m" id="hiddenHargaBibitPer100m">
      <input type="hidden" name="jenisKegiatan" value="Penanaman">

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Nama Rencana</label>
        <input type="text" name="namaRencana" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
        <input type="date" name="tanggalMulai" id="tanggalMulai" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Prediksi Panen</label>
        <input type="date" name="tanggalSelesai" id="tanggalPanen" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
        <textarea name="catatan" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Luas Lahan (m²)</label>
        <input type="number" name="luasLahan" id="luasLahan" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Daerah</label>
        <input type="text" name="daerah" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga per Karung Pupuk (Rp)</label>
        <input type="number" id="hargaPerKarungInput" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga Bibit per 100 m² (Rp)</label>
        <input type="number" id="hargaBibitPer100mInput" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div class="md:col-span-2 flex justify-between mt-4">
        <a href="perencanaan.php" class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
          ← Kembali
        </a>
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
          ✔ Simpan Rencana
        </button>
      </div>
    </form>
  </div>

  <script>
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    document.getElementById("tanggalMulai").setAttribute("min", todayStr);

    document.getElementById("tanggalMulai").addEventListener("change", function () {
      const mulai = new Date(this.value);
      if (!isNaN(mulai)) {
        const panen = new Date(mulai);
        panen.setDate(panen.getDate() + 120);
        document.getElementById("tanggalPanen").valueAsDate = panen;
      }
    });

    // Salin harga ke hidden field saat input berubah
    document.getElementById("hargaPerKarungInput").addEventListener("input", function () {
      document.getElementById("hiddenHargaPerKarung").value = this.value;
    });
    document.getElementById("hargaBibitPer100mInput").addEventListener("input", function () {
      document.getElementById("hiddenHargaBibitPer100m").value = this.value;
    });

    document.getElementById("formRencana").addEventListener("submit", function (e) {
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
