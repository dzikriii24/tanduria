<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email' LIMIT 1");

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login_success'] = true; // <-- tanda login sukses
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="  min-h-screen flex items-center justify-center px-4">

  <div class="bg-white w-full max-w-md p-8 rounded-3xl shadow-2xl">
    <div class="text-center mb-6">
      <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" class="w-16 h-16 mx-auto" alt="user icon">
      <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-[#2C8F53] from-indigo-600 via-blue-600 to-purple-600 mt-3">Selamat Datang</h2>
      <p class="text-sm text-[#4E4E4E] mt-1">Silakan masuk ke akun Anda</p>
    </div>

    <?php if (isset($error)): ?>
      <div id="alertBox" class="mb-4 text-red-600 text-center text-sm font-semibold bg-red-100 rounded-md py-2 px-4 transition-opacity duration-500">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <!-- Email -->
      <div class="group">
        <label class="block mb-1 font-medium text-gray-700">Email</label>
        <input type="email" name="email" required placeholder="Masukkan email"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <!-- Password -->
      <div class="relative group">
        <label class="block mb-1 font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="passwordInput" required placeholder="Masukkan password"
          class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <button type="button" onclick="togglePassword()" class="absolute top-[38px] right-4 text-gray-500 hover:text-indigo-600">
          <i id="toggleIcon" class="ph ph-eye text-xl"></i>
        </button>
      </div>

      <!-- Login Button -->
      <div>
        <button type="submit"
          class="w-full bg-[#009158] text-white font-bold py-3 rounded-xl hover:bg-[#1D6034] transition duration-200 shadow-lg">
          <i class="ph ph-sign-in mr-2"></i>  Masuk
        </button>
      </div>
    </form>

    <p class="text-center text-sm text-[#4E4E4E] mt-4">
      Belum punya akun?
      <a href="register.php" class="text-indigo-500 hover:underline font-medium">Daftar di sini</a>
    </p>
  </div>

  <script>
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

    <?php if (isset($_GET['success'])): ?>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: 'Akun berhasil dibuat. Silakan login.',
      confirmButtonColor: '#6366f1'
    });
    <?php endif; ?>

    const alertBox = document.getElementById("alertBox");
    if (alertBox) {
      setTimeout(() => {
        alertBox.classList.add("opacity-0");
      }, 1000);
    }

   
  </script>
</body>
</html>
