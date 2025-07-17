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

  $lokasi = !empty($lokasi_otomatis) ? $lokasi_otomatis : $lokasi_manual;
  $lokasi_bersih = trim($lokasi, '()');
  $lat = null;
  $lng = null;

  if (strpos($lokasi_bersih, ',') !== false) {
    list($lat, $lng) = explode(',', $lokasi_bersih);
    $lat = floatval($lat);
    $lng = floatval($lng);
  }

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Upload foto
  $foto_path = "";
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_file = 'foto_' . time() . '.' . $ext;
    $upload_dir = 'uploads/';
    $lokasi_file = $upload_dir . $nama_file;

    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $lokasi_file)) {
      $foto_path = $lokasi_file;
    }
  }
  $query = "INSERT INTO user (nama, email, password, no_telepon, jenis_kelamin, foto, lokasi, lat, lng)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("sssssssdd", $nama, $email, $hashed_password, $no_telepon, $jenis_kelamin, $foto_path, $lokasi, $lat, $lng);

  if ($stmt->execute()) {
    header("Location: login.php?success=1");
    exit();
  } else {
    echo "❌ Gagal daftar: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Akun</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class=" min-h-screen flex justify-center px-4 py-12 sm:py-16 overflow-auto">
  <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl animate__animated animate__fadeIn">
    <div class="text-center mb-6">
      <img src="https://cdn-icons-png.flaticon.com/512/942/942748.png" class="w-16 h-16 mx-auto animate-bounce" alt="user icon">
      <h2 class="text-4xl font-extrabold text-transparent bg-clip-text  bg-[#2C8F53] from-indigo-600 via-blue-600 to-purple-600 mt-3">Buat Akun</h2>
      <p class="text-sm text-gray-500 mt-1">Isi data lengkap Anda untuk mendaftar</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
      <div class="relative">
        <label class="block mb-1 font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" name="nama" required placeholder="Masukkan nama lengkap"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="relative">
        <label class="block mb-1 font-medium text-gray-700">Email</label>
        <input type="email" name="email" required placeholder="Masukkan email"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <!-- No. Telepon -->
      <div class="relative">
        <label class="block mb-1 font-medium text-gray-700">No. Telepon</label>
        <input type="text" name="no_telepon" required
          maxlength="13"
          pattern="[0-9]+"
          oninput="this.value=this.value.replace(/[^0-9]/g,'');"
          placeholder="08xxxxxxxxxx"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Jenis Kelamin</label>
        <select name="jenis_kelamin" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="">-- Pilih --</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
      </div>

      <div class="relative group">
        <label class="block mb-1 font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="passwordInput" required placeholder="Masukkan password"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <button type="button" onclick="togglePassword()" class="absolute top-[38px] right-4 text-gray-500 hover:text-indigo-600">
          <i id="toggleIcon" class="ph ph-eye text-xl"></i>
        </button>
      </div>

      <div>

  <label class="block mb-1 font-medium text-gray-700">Foto Profil</label>
  <div class="flex items-center gap-3">
    <label for="uploadFoto"
           class="cursor-pointer bg-[#1D6034] hover:bg-[#006138] transition text-white px-4 py-2 rounded-md flex items-center gap-2 shadow">
      <i class="ph ph-folder text-lg"></i> Pilih Foto
    </label>
    <span id="fileName" class="text-sm text-gray-600">Belum ada file</span>
  </div>
  <input type="file" name="foto" id="uploadFoto" accept="image/*" class="hidden" />
</div>


      <input type="hidden" name="lokasi" id="lokasi">

      <div>
        <button type="submit"
                class="w-full bg-[#2C8F53] text-white font-bold py-3 rounded-xl hover:bg-[#006138] transition duration-200 shadow-lg">
          <i class="ph ph-user-plus mr-2"></i>Daftar
        </button>
      </div>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
      Sudah punya akun?
      <a href="login.php" class="text-indigo-500 hover:underline font-medium">Masuk di sini</a>
    </p>
  </div>

  <script>
    window.onload = function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
          document.getElementById('lokasi').value =
            pos.coords.latitude + ',' + pos.coords.longitude;
        });
      }
    };

    // Tampilkan nama file yang dipilih
    const uploadInput = document.getElementById('uploadFoto');
    const fileNameDisplay = document.getElementById('fileName');

    uploadInput.addEventListener('change', function() {
      const fileName = this.files.length > 0 ? this.files[0].name : "Belum ada file";
      fileNameDisplay.textContent = fileName;
      fileNameDisplay.classList.add("animate-pulse", "text-indigo-700");
      setTimeout(() => fileNameDisplay.classList.remove("animate-pulse", "text-indigo-700"), 600);
    });

    function togglePassword() {
      const input = document.getElementById("passwordInput");
      const icon = document.getElementById("toggleIcon");
      if (input.type === "password") {
        input.type = "text";
        icon.className = "ph ph-eye-slash text-xl";
      } else {
        input.type = "password";
        icon.className = "ph ph-eye text-xl";
      }
    }
  </script>
</body>

</html>