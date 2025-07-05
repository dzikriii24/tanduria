<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Perencanaan - Tanduria</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <div class="max-w-4xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Perencanaan</h2>

    <form id="formPerencanaan" onsubmit="return handlePerencanaan(event)" class="bg-white p-6 rounded-2xl shadow-md space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Nama Rencana -->
        <div>
          <label for="namaRencana" class="text-sm font-medium text-gray-700 mb-1">Nama Rencana</label>
          <input type="text" id="namaRencana" name="namaRencana" placeholder="Contoh: Penanaman Musim Hujan"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Jenis Kegiatan -->
        <div>
          <label for="jenisKegiatan" class="text-sm font-medium text-gray-700 mb-1">Jenis Kegiatan</label>
          <select id="jenisKegiatan" name="jenisKegiatan"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
            <option value="">Pilih Kegiatan</option>
            <option value="Penanaman">Penanaman</option>
            <option value="Pemupukan">Pemupukan</option>
            <option value="Penyemprotan">Penyemprotan</option>
            <option value="Panen">Panen</option>
          </select>
        </div>

        <!-- Tanggal Mulai -->
        <div>
          <label for="tanggalMulai" class="text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
          <input type="date" id="tanggalMulai" name="tanggalMulai"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Tanggal Selesai -->
        <div>
          <label for="tanggalSelesai" class="text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
          <input type="date" id="tanggalSelesai" name="tanggalSelesai"
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none">
        </div>

        <!-- Catatan -->
        <div class="md:col-span-2">
          <label for="catatan" class="text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
          <textarea id="catatan" name="catatan" rows="4" placeholder="Tulis catatan penting di sini..."
            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:border-green-500 focus:ring-green-500 focus:outline-none"></textarea>
        </div>
      </div>

      <!-- Tombol -->
      <div class="pt-4">
        <button type="submit"
          class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition duration-300">
          <svg class="h-5 w-5 mr-2 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          Simpan Rencana
        </button>
      </div>
    </form>
  </div>

  <!-- SCRIPT -->
  <script>
    function handlePerencanaan(event) {
      event.preventDefault();

      const nama = document.getElementById('namaRencana').value.trim();
      const jenis = document.getElementById('jenisKegiatan').value;

      // Validasi seperti versi tambah lahan
      if (!nama && !jenis) {
        Swal.fire({
          icon: 'error',
          title: 'Data Belum Lengkap',
          text: 'Nama rencana dan jenis kegiatan wajib diisi.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      if (!nama) {
        Swal.fire({
          icon: 'error',
          title: 'Nama Rencana Kosong',
          text: 'Nama rencana wajib diisi.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      if (!jenis) {
        Swal.fire({
          icon: 'error',
          title: 'Jenis Kegiatan Belum Dipilih',
          text: 'Silakan pilih jenis kegiatan.',
          confirmButtonColor: '#d33'
        });
        return false;
      }

      // Konfirmasi
      Swal.fire({
        title: 'Simpan Perencanaan?',
        text: 'Apakah kamu yakin ingin menyimpan perencanaan ini?',
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
              text: 'Perencanaan berhasil disimpan.',
              confirmButtonColor: '#10b981'
            });
            document.getElementById('formPerencanaan').reset();
          }, 2000);
        }
      });

      return false;
    }
  </script>

</body>
</html>
