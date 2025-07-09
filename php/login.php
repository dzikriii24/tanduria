<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email ada di database
    $result = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email' LIMIT 1");

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan data user ke session
            $_SESSION['user_id'] = $user['id'];

            // Redirect ke profile
            header("Location: profile.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold text-center mb-6">Masuk</h2>

    <?php if (isset($error)): ?>
      <div class="mb-4 text-red-600 text-center text-sm"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <!-- Email -->
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" required
               class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- Password -->
      <div>
        <label class="block mb-1 font-medium">Password</label>
        <input type="password" name="password" required
               class="w-full border border-gray-300 rounded-lg px-4 py-2" />
      </div>

      <!-- Tombol Login -->
      <div>
        <button type="submit"
                class="w-full bg-[#2129B3] text-white font-medium py-3 rounded-lg text-base hover:bg-blue-900 transition">
          Masuk
        </button>
      </div>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
      Belum punya akun?
      <a href="register.php" class="text-blue-600 hover:underline">Daftar di sini</a>
    </p>
  </div>

</body>
</html>
