<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama          = $_POST['nama'];
    $email         = $_POST['email'];
    $password      = $_POST['password'];
    $no_telepon    = $_POST['no_telepon'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $lokasi_otomatis = $_POST['lokasi'];
    $lokasi_manual   = $_POST['lokasi_manual'];

    // Lokasi yang dipilih: otomatis jika ada, manual kalau tidak
    $lokasi = !empty($lokasi_otomatis) ? $lokasi_otomatis : $lokasi_manual;

    // Hash password (bisa diganti dengan bcrypt atau lainnya)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle upload foto
    $foto_path = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file = 'foto_' . time() . '.' . $ext;
        $upload_dir = 'uploads/';
        $lokasi_file = $upload_dir . $nama_file;

        // Pastikan folder upload tersedia
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $lokasi_file)) {
            $foto_path = $lokasi_file;
        }
    }

    // Query INSERT dengan kolom dan nilai sesuai
    $query = "INSERT INTO user (nama, email, password, no_telepon, jenis_kelamin, foto, lokasi)
              VALUES ('$nama', '$email', '$hashed_password', '$no_telepon', '$jenis_kelamin', '$foto_path', '$lokasi')";

    if (mysqli_query($conn, $query)) {
        header("Location: login.php");
        exit();
    } else {
        echo "Gagal daftar: " . mysqli_error($conn);
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
      <!-- Lokasi otomatis (hidden) -->
<input type="hidden" name="lokasi" id="lokasi">

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
<script>
  window.onload = function () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (pos) {
        document.getElementById('lokasi').value =
          pos.coords.latitude + ',' + pos.coords.longitude;
      });
    }
  };
</script>

</html>
