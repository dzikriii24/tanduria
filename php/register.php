<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama          = $_POST['nama'];
    $email         = $_POST['email'];
    $no_telepon    = $_POST['no_telepon'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $password      = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi

    // Inisialisasi nama file (default kosong)
    $foto = '';

    // Proses upload foto jika ada
    if ($_FILES['foto']['name']) {
        $ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file = 'foto_' . time() . '.' . $ext;
        $lokasi = 'uploads/' . $nama_file;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $lokasi)) {
            $foto = $lokasi;
        }
    }

    // Simpan ke database
    $query = "INSERT INTO user (nama, email, no_telepon, jenis_kelamin, password, foto)
              VALUES ('$nama', '$email', '$no_telepon', '$jenis_kelamin', '$password', '$foto')";

    $simpan = mysqli_query($conn, $query);

   if ($simpan) {
    echo "<script>
        alert('Pendaftaran berhasil! Silakan login.');
        window.location='login.php';
    </script>";
    exit();
}

}
?>

<!-- UI Daftar (HTML sama seperti sebelumnya) -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Daftar Akun</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold text-center mb-6">Daftar Akun</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
      <!-- Nama -->
      <div>
        <label class="block mb-1 font-medium">Nama Lengkap</label>
        <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- Email -->
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- No. Telepon -->
      <div>
        <label class="block mb-1 font-medium">No. Telepon</label>
        <input type="text" name="no_telepon" required class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- Jenis Kelamin -->
      <div>
        <label class="block mb-1 font-medium">Jenis Kelamin</label>
        <select name="jenis_kelamin" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
          <option value="">-- Pilih --</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
      </div>

      <!-- Password -->
      <div>
        <label class="block mb-1 font-medium">Password</label>
        <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- Upload Foto -->
      <div>
        <label class="block mb-1 font-medium">Foto Profil</label>
        <input type="file" name="foto" accept="image/*"
               class="w-full border border-gray-300 rounded-lg px-4 py-2 file:bg-[#2129B3] file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 hover:file:bg-blue-900" />
      </div>

      <!-- Tombol Daftar -->
      <div>
        <button type="submit"
                class="w-full bg-[#2129B3] text-white font-medium py-3 rounded-lg text-base hover:bg-blue-900 transition">
          Daftar
        </button>
      </div>
    </form>

    <!-- Link ke Login -->
    <p class="text-center text-sm text-gray-600 mt-4">
      Sudah punya akun?
      <a href="login.php" class="text-blue-600 hover:underline">Masuk di sini</a>
    </p>
  </div>

</body>
</html>
