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

    <form id="formLahan" action="lahan.php" method="POST" enctype="multipart/form-data" onsubmit="return handleSubmit(event)" class="bg-white p-6 rounded-2xl shadow-md space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nama -->
        <div>
          <label for="namaLahan" class="block text-sm font-medium text-gray-700 mb-1">Nama Lahan</label>
          <input type="text" id="namaLahan" name="namaLahan" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Luas -->
        <div>
          <label for="luasLahan" class="block text-sm font-medium text-gray-700 mb-1">Luas Lahan (m²)</label>
          <input type="number" id="luasLahan" name="luasLahan" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Tempat -->
        <div>
          <label for="tempatLahan" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahan</label>
          <input type="text" id="tempatLahan" name="tempatLahan" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Jenis Padi -->
        <div>
          <label for="jenisPadi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Padi</label>
          <select id="jenisPadi" name="jenisPadi" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
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
          <input type="date" id="mulaiTanam" name="mulaiTanam" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Upload Foto -->
        <div>
          <label for="fotoLahan" class="block text-sm font-medium text-gray-700 mb-1">Foto Lahan</label>
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

        <!-- Deskripsi -->
        <div class="md:col-span-2">
          <label for="deskripsiLahan" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea id="deskripsiLahan" name="deskripsiLahan" rows="3" class="w-full rounded-xl border px-4 py-2 text-sm resize-none focus:ring-green-500 focus:outline-none"></textarea>
        </div>

        <!-- Link Maps -->
        <div class="md:col-span-2">
          <label for="linkMaps" class="block text-sm font-medium text-gray-700 mb-1">Link Google Maps</label>
          <input type="url" id="linkMaps" name="linkMaps" placeholder="https://maps.google.com/..." class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Pestisida -->
        <div>
          <label for="pestisida" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pestisida</label>
          <select id="pestisida" name="pestisida" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
            <option value="">Pilih Pestisida</option>
            <option value="Organik">Organik</option>
            <option value="Kimia Sistemik">Kimia Sistemik</option>
            <option value="Kimia Kontak">Kimia Kontak</option>
            <option value="Hayati">Hayati</option>
          </select>
        </div>

        <!-- Modal -->
        <div>
          <label for="modalTanam" class="block text-sm font-medium text-gray-700 mb-1">Modal Tanam (Rp)</label>
          <input type="number" id="modalTanam" name="modalTanam" class="w-full rounded-xl border px-4 py-2 text-sm focus:ring-green-500 focus:outline-none">
        </div>
      </div>

      <!-- Hidden Fields for Coordinates -->
      <input type="hidden" id="koordinatLat" name="koordinatLat">
      <input type="hidden" id="koordinatLng" name="koordinatLng">

      <!-- Tombol -->
      <div class="pt-2 flex justify-between">
        <button type="button" onclick="window.history.back()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-semibold">
          ← Kembali
        </button>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-semibold flex items-center">
          <svg class="h-5 w-5 mr-2 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          Simpan Lahan
        </button>
      </div>
    </form>
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
    document.getElementById('linkMaps').addEventListener('input', function () {
      const link = this.value;
      const latField = document.getElementById('koordinatLat');
      const lngField = document.getElementById('koordinatLng');
      const match = link.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
      if (match) {
        latField.value = match[1];
        lngField.value = match[2];
      } else {
        latField.value = "";
        lngField.value = "";
      }
    });

    function handleSubmit(event) {
      event.preventDefault();
      const form = document.getElementById('formLahan');
      const required = ['namaLahan', 'luasLahan', 'tempatLahan', 'jenisPadi', 'mulaiTanam', 'fotoLahan', 'deskripsiLahan', 'linkMaps', 'pestisida', 'modalTanam'];
      let pesan = "";

      required.forEach(id => {
        const val = document.getElementById(id).value.trim();
        if (!val) pesan += `- ${id}\n`;
      });

      if (pesan !== "") {
        Swal.fire({ icon: 'error', title: 'Data Belum Lengkap', text: "Mohon isi:\n" + pesan, confirmButtonColor: '#d33' });
        return false;
      }

      const inputDate = new Date(document.getElementById('mulaiTanam').value);
      const today = new Date(); today.setHours(0,0,0,0);
      if (inputDate < today) {
        Swal.fire({ icon: 'error', title: 'Tanggal Tanam Tidak Valid', text: 'Minimal hari ini.', confirmButtonColor: '#d33' });
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
  </script>

</body>
</html>
