<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

// Ambil data user sekarang
$query = mysqli_query($conn, "SELECT * FROM user WHERE id = $id_user");
$user = mysqli_fetch_assoc($query);

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $no_telepon = $_POST['no_telepon'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Cek apakah ada file foto diupload
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file = 'foto_' . time() . '.' . $ext;
        $lokasi = 'uploads/' . $nama_file;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $lokasi)) {
            // Hapus foto lama kalau ada
            if (!empty($user['foto']) && file_exists($user['foto'])) {
                unlink($user['foto']);
            }

            // Simpan juga foto
            $query_update = "UPDATE user SET 
                                nama='$nama', 
                                no_telepon='$no_telepon', 
                                jenis_kelamin='$jenis_kelamin',
                                foto='$lokasi' 
                            WHERE id=$id_user";
        }
    }

    // Jika tidak upload foto, update data lain saja
    if (!isset($query_update)) {
        $query_update = "UPDATE user SET 
                            nama='$nama', 
                            no_telepon='$no_telepon', 
                            jenis_kelamin='$jenis_kelamin' 
                        WHERE id=$id_user";
    }

    // Jalankan query
    if (mysqli_query($conn, $query_update)) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Query error: " . mysqli_error($conn);
        exit();
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

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
  <!-- Nama -->
  <div>
    <label class="block mb-1 font-medium">Nama Lengkap</label>
    <input type="text" name="nama" value="<?= $user['nama'] ?>" required class="w-full border rounded-lg px-4 py-2" />
  </div>

  <!-- No. Telepon -->
  <div>
    <label class="block mb-1 font-medium">No. Telepon</label>
    <input type="text" name="no_telepon" value="<?= $user['no_telepon'] ?>" required class="w-full border rounded-lg px-4 py-2" />
  </div>

  <!-- Jenis Kelamin -->
  <div>
    <label class="block mb-1 font-medium">Jenis Kelamin</label>
    <select name="jenis_kelamin" required class="w-full border rounded-lg px-4 py-2">
      <option value="Laki-laki" <?= $user['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
      <option value="Perempuan" <?= $user['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
    </select>
  </div>

  <!-- Upload Foto -->
  <div>
    <label class="block mb-1 font-medium">Foto Baru</label>
    <input type="file" name="foto" accept="image/*" class="w-full border rounded-lg px-4 py-2" />
  </div>

  <!-- Tombol -->
  <div class="flex justify-between mt-6 gap-4">
    <a href="profile.php" class="w-1/2 text-center bg-gray-200 py-3 rounded-lg hover:bg-[#4E4E4E]">Kembali</a>
    <button type="submit" class="w-1/2 bg-[#006138] text-white py-3 rounded-lg hover:bg-[#009158]">
      Simpan Perubahan
    </button>
  </div>
</form>

  </div>

</body>
</html>
