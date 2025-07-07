<?php
include 'db.php';

// Ambil data user ID 1
$query = mysqli_query($conn, "SELECT * FROM user WHERE id = 1");
$user = mysqli_fetch_assoc($query);

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama         = $_POST['nama'];
    $email        = $_POST['email'];
    $no_telepon   = $_POST['no_telepon'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    $update = mysqli_query($conn, "UPDATE user SET 
        nama = '$nama', 
        email = '$email', 
        no_telepon = '$no_telepon', 
        jenis_kelamin = '$jenis_kelamin' 
        WHERE id = 1
    ");

    if ($update) {
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('Gagal update data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Update Profil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex flex-col">

  <div class="w-full px-4 py-10 sm:px-6 md:px-10 lg:px-20 xl:px-40">
    <div class="text-center mb-8">
      <h1 class="text-3xl md:text-4xl font-semibold">Update Profil</h1>
    </div>

    <form method="POST" class="space-y-6">
      <!-- Nama -->
      <div>
        <label class="block mb-1 font-medium">Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>

      <!-- Email -->
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>

      <!-- Nomor Telepon -->
      <div>
        <label class="block mb-1 font-medium">Nomor Telepon</label>
        <input type="text" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon']) ?>" required
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>

      <!-- Jenis Kelamin -->
      <div>
        <label class="block mb-1 font-medium">Jenis Kelamin</label>
        <select name="jenis_kelamin" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          <option value="">-- Pilih --</option>
          <option value="Laki-laki" <?= $user['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
          <option value="Perempuan" <?= $user['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
        </select>
      </div>

      <!-- Tombol Simpan -->
      <div class="pt-4">
        <button type="submit"
                class="w-full bg-[#2129B3] text-white font-medium py-3 rounded-lg text-base md:text-lg hover:bg-blue-900 transition">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>

</body>
</html>
