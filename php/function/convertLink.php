<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Halaman Pertanian Pintar</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
    }

    /* Loader overlay */
    #loader {
      position: fixed;
      z-index: 9999;
      width: 100vw;
      height: 100vh;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      transition: opacity 0.5s ease;
    }

    .loader-animation {
      font-size: 24px;
      margin-top: 20px;
      animation: bounce 1s infinite;
      color: #4CAF50;
    }

    .chicken {
      font-size: 60px;
      animation: run 1.2s linear infinite;
      display: inline-block;
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-10px);
      }
    }

    @keyframes run {
      0% {
        transform: translateX(-100%);
      }
      100% {
        transform: translateX(100vw);
      }
    }

    /* Konten utama */
    #content {
      padding: 40px;
    }
  </style>
</head>
<body>

  <!-- Loader -->
  <div id="loader">
    <div class="chicken">🐔</div>
    <div class="loader-animation">Lagi nyari cangkul di sawah...</div>
  </div>

  <!-- Konten halaman -->
  <div id="content">
    <h1>🌾 Selamat Datang di Halaman Pertanian Pintar</h1>
    <p>Di sini kamu bisa menambahkan lahan pintar, memantau suhu, kelembapan, dan banyak lagi.</p>
    <p>Kontennya tetap bisa kamu lihat, loader cuma muncul sebentar kok 😁</p>
  </div>

  <script>
    // Hilangkan loader setelah 2 detik
    window.addEventListener("load", () => {
      setTimeout(() => {
        document.getElementById("loader").style.opacity = "0";
        setTimeout(() => {
          document.getElementById("loader").style.display = "none";
        }, 500);
      }, 2000); // durasi animasi loading
    });
  </script>

</body>
</html>
