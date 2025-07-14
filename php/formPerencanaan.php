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
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Nama Rencana</label>
        <input type="text" name="namaRencana" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Jenis Kegiatan</label>
        <select name="jenisKegiatan" required class="w-full px-4 py-2 border rounded-lg">
          <option value="">Pilih Kegiatan</option>
          <option value="Penanaman">Penanaman</option>
          <option value="Pemanenan">Pemanenan</option>
          <option value="Pemupukan">Pemupukan</option>
        </select>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
        <input type="date" name="tanggalMulai" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
        <input type="date" name="tanggalSelesai" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-gray-700 font-semibold mb-2">Catatan</label>
        <textarea name="catatan" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Luas Lahan (m²)</label>
        <input type="number" name="luasLahan" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Daerah</label>
        <input type="text" name="daerah" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga Pupuk (1 karung)</label>
        <input type="number" name="hargaPupuk" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Jumlah Karung Pupuk</label>
        <input type="number" name="jumlahKarungPupuk" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga Bibit (per m²)</label>
        <input type="number" name="hargaBibit" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div class="md:col-span-2 flex justify-between mt-2">
      <a href="perencanaan.php" class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
      ← Kembali
    </a>
      <div class="md:col-span-2">
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
          ✔ Simpan Rencana
        </button>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("formRencana").addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);

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
