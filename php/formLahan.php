<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Lahan - Tanduria</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <div class="max-w-4xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Lahan</h2>

    <form id="formLahan" onsubmit="return handleSubmit(event)" class="bg-white p-6 rounded-2xl shadow-md space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Nama Lahan -->
        <div>
          <label for="namaLahan" class="block text-sm font-medium text-gray-700 mb-1">Nama Lahan</label>
          <input type="text" id="namaLahan" name="namaLahan" placeholder="Contoh: Lahan Padi Utara"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Luas Lahan -->
        <div>
          <label for="luasLahan" class="block text-sm font-medium text-gray-700 mb-1">Luas Lahan (m²)</label>
          <input type="number" id="luasLahan" name="luasLahan" placeholder="Contoh: 1500"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Tempat Lahan -->
        <div>
          <label for="tempatLahan" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahan</label>
          <input type="text" id="tempatLahan" name="tempatLahan" placeholder="Contoh: Desa Mekarsari"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Jenis Padi -->
        <div>
          <label for="jenisPadi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Padi</label>
          <select id="jenisPadi" name="jenisPadi"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
            <option value="">Pilih Jenis Padi</option>
            <option value="IR64">IR64</option>
            <option value="Ciherang">Ciherang</option>
            <option value="Inpari 32">Inpari 32</option>
            <option value="Pandan Wangi">Pandan Wangi</option>
          </select>
        </div>

        <!-- Mulai Tanam -->
        <div>
          <label for="mulaiTanam" class="block text-sm font-medium text-gray-700 mb-1">Mulai Tanam</label>
          <input type="date" id="mulaiTanam" name="mulaiTanam"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Upload Foto -->
        <div>
          <label for="fotoLahan" class="block text-sm font-medium text-gray-700 mb-1">Foto Lahan</label>
          <input type="file" id="fotoLahan" name="fotoLahan" accept="image/*"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm bg-white focus:border-green-500 focus:ring-green-500 focus:outline-none"
            onchange="previewImage(event)">
          <img id="preview" class="mt-3 w-full rounded-xl shadow-sm hidden max-h-64 object-cover" alt="Preview Foto Lahan">
        </div>
      </div>

      <!-- Tombol -->
      <div class="pt-4">
        <button type="submit"
          class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-all duration-300">
          <svg class="h-5 w-5 mr-2 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          Simpan Lahan
        </button>
      </div>
    </form>
  </div>

  <!-- SCRIPT VALIDASI & PREVIEW -->
  <script>
    function previewImage(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('preview');

      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      } else {
        preview.classList.add('hidden');
      }
    }

    function handleSubmit(event) {
      event.preventDefault();

      const namaLahan = document.getElementById('namaLahan').value.trim();
      const luasLahan = document.getElementById('luasLahan').value.trim();
      const tempatLahan = document.getElementById('tempatLahan').value.trim();
      const jenisPadi = document.getElementById('jenisPadi').value;
      const mulaiTanam = document.getElementById('mulaiTanam').value;
      const fotoLahan = document.getElementById('fotoLahan').files[0];

      let message = "";
      if (!namaLahan) message += "- Nama lahan\n";
      if (!luasLahan) message += "- Luas lahan\n";
      if (!tempatLahan) message += "- Tempat lahan\n";
      if (!jenisPadi) message += "- Jenis padi\n";
      if (!mulaiTanam) message += "- Tanggal mulai tanam\n";
      if (!fotoLahan) message += "- Foto lahan\n";

      if (message) {
        Swal.fire({
          icon: 'error',
          title: 'Data Belum Lengkap',
          text: "Mohon isi:\n" + message,
          confirmButtonColor: '#d33'
        });
        return false;
      }

      if (isNaN(luasLahan) || parseFloat(luasLahan) <= 0) {
        Swal.fire({
          icon: 'error',
          title: 'Luas Tidak Valid',
          text: 'Luas lahan harus berupa angka dan lebih dari 0.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const inputDate = new Date(mulaiTanam);
      if (inputDate < today) {
        Swal.fire({
          icon: 'error',
          title: 'Tanggal Tanam Tidak Valid',
          text: 'Tanggal tanam tidak boleh di masa lalu.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      Swal.fire({
        title: 'Simpan Data?',
        text: 'Apakah kamu yakin ingin menyimpan data lahan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
          });

          setTimeout(() => {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: 'Data lahan berhasil disimpan.',
              confirmButtonColor: '#10b981'
            });
            document.getElementById('formLahan').reset();
            document.getElementById('preview').classList.add('hidden');
          }, 2000);
        }
      });

      return false;
    }
  </script>

</body>
</html>
