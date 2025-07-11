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
      <!-- Nama Rencana -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Nama Rencana</label>
        <input type="text" name="namaRencana" placeholder="Contoh: Penanaman Musim Hujan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Jenis Kegiatan -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Jenis Kegiatan</label>
        <select name="jenisKegiatan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
          <option value="">Pilih Kegiatan</option>
          <option value="Penanaman">Penanaman</option>
          <option value="Pemanenan">Pemanenan</option>
          <option value="Pemupukan">Pemupukan</option>
        </select>
      </div>

      <!-- Tanggal Mulai -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
        <input type="date" name="tanggalMulai" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Tanggal Selesai -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
        <input type="date" name="tanggalSelesai" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Catatan Tambahan -->
      <div class="md:col-span-2">
        <label class="block text-gray-700 font-semibold mb-2">Catatan Tambahan</label>
        <textarea name="catatan" placeholder="Tulis catatan penting di sini…" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
      </div>

      <!-- Luas Lahan -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Luas Lahan (m²)</label>
        <input type="number" name="luasLahan" placeholder="Contoh: 1000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Daerah -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Daerah</label>
        <input type="text" name="daerah" placeholder="Contoh: Cicalengka" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Harga Pupuk -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga Pupuk (1 karung)</label>
        <input type="number" name="hargaPupuk" placeholder="Contoh: 50000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Harga Bibit -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Harga Bibit (per m²)</label>
        <input type="number" name="hargaBibit" placeholder="Contoh: 1000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Modal Tanam -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Modal Tanam (Rp)</label>
        <input type="number" name="modal" placeholder="Contoh: 1500000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Hasil Panen -->
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Hasil Panen (Rp)</label>
        <input type="number" name="hasilPanen" placeholder="Contoh: 3000000" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500" />
      </div>

      <!-- Tombol Simpan -->
      <div class="md:col-span-2 flex justify-start mt-4">
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">
          ✔ Simpan Rencana
        </button>
      </div>
    </form>
  </div>

  <!-- SweetAlert Handler -->
  <script>
    document.getElementById("formRencana").addEventListener("submit", function (e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const emptyFields = [];

      form.querySelectorAll("[name]").forEach(input => {
        if (!input.value.trim()) {
          const label = input.closest("div").querySelector("label").innerText;
          emptyFields.push(label);
        }
      });

      if (emptyFields.length > 0) {
        Swal.fire({
          icon: "error",
          title: "Form Belum Lengkap!",
          html: "Harap isi data berikut:<br><b>" + emptyFields.join("</b><br><b>") + "</b>",
        });
        return;
      }

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
            body: formData
          })
          .then(res => {
            if (!res.ok) throw new Error("Gagal menyimpan!");
            return res.text();
          })
          .then(() => {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: "Data perencanaan berhasil disimpan.",
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              window.location.href = "perencanaan.php";
            });
          })
          .catch(err => {
            Swal.fire("Gagal!", err.message, "error");
          });
        }
      });
    });
  </script>
</body>
</html>
