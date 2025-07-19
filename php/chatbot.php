<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Proses tambah lahan pintar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lahan_id'], $_POST['device_id'])) {
    $lahan_id = (int) $_POST['lahan_id'];
    $device_id = trim($_POST['device_id']);

    if ($lahan_id > 0 && $device_id !== '') {
        $cek = $conn->query("SELECT id FROM lahan_pintar WHERE lahan_id = $lahan_id");
        if ($cek->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO lahan_pintar (lahan_id, device_id) VALUES (?, ?)");
            $stmt->bind_param("is", $lahan_id, $device_id);
            $stmt->execute();
            $success = true;
        } else {
            $error = "Lahan ini sudah didaftarkan sebelumnya.";
        }
    } else {
        $error = "Semua input wajib diisi.";
    }
}

// Ambil semua lahan_pintar user
$lahanPintar = $conn->query("
    SELECT lp.id, l.nama_lahan, l.tempat_lahan, lp.device_id
    FROM lahan_pintar lp
    JOIN lahan l ON lp.lahan_id = l.id
    WHERE l.user_id = $user_id
")->fetch_all(MYSQLI_ASSOC);

// Ambil lahan yang belum jadi lahan pintar
$lahanAvailable = $conn->query("
    SELECT l.id, l.nama_lahan, l.tempat_lahan
    FROM lahan l
    LEFT JOIN lahan_pintar lp ON l.id = lp.lahan_id
    WHERE l.user_id = $user_id AND lp.lahan_id IS NULL
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/icon.css">
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="icon" href="../asset/icon/logo.svg" type="image/svg+xml">
    <title>Chatbot & Pertanian Pintar</title>
    <style type="text/tailwind">
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/hover.css">
    <link rel="stylesheet" href="../css/icon.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>


    <div
        class="hero min-h-screen"
        style="background-image: url('../asset/icon/modernfarm.svg');">
        <div class="hero-overlay">
            <a href="../index.php" class=" w-18 mt-2 flex items-center space-x-2 bg-[#2C8F53] shadow-md rounded-full px-3 py-2 text-white hover:bg-[#1D6034] transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
      </a>
        </div>
        <div class="flex-col hero-content text-neutral-content text-center">
            <h1 class="mb-5 text-5xl font-bold selamat absolute top-40"></h1>
            <div class="max-w-md">
                <p class="mb-5">
                    Mulai tambahkan lahan pintar anda dibawah ini, silahkan pilih lahan pintar anda dan masukan nomer mesin. Jika anda bingung, boleh tanya ke AI Agent kami yaa!!
                </p>
                <?php if (count($lahanAvailable) > 0): ?>
                    <form method="post" class="flex gap-4">
                        <select class="select select-ghost" name="lahan_id" required>
                            <option disabled selected>Pilih Lahan Pintar Anda</option>
                            <?php foreach ($lahanAvailable as $lahan): ?>
                                <option value="<?= $lahan['id'] ?>">
                                    <?= htmlspecialchars($lahan['nama_lahan']) ?> - <?= htmlspecialchars($lahan['tempat_lahan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" name="device_id" placeholder="Masukan ID Device IoT" class="input input-ghost" required />


                        <button class="btn btn-soft btn-success" type="submit">Tambah Lahan</button>

                    </form>
                <?php else: ?>
                    <p>Semua lahan Anda sudah ditambahkan sebagai lahan pintar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <?php if (!empty($success)): ?>
        <div class="toast toast-top toast-end">
            <div class="alert alert-success">
                <span>Lahan Berhasil .</span>
            </div>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-error mb-6">
            <span>✗ <?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>


    <!-- Daftar Lahan Pintar -->
    <div class="card bg-white mb-30">
        <div class="card-body">
            <h2 class="card-title text-[#4E4E4E] mb-4"> Daftar Lahan Pintar Anda</h2>

            <?php if (count($lahanPintar) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($lahanPintar as $lahan): ?>
                        <div class="card bg-base-100 shadow-lg border border-base-300" id="lahan-<?= $lahan['device_id'] ?>">
                            <div class="card-body p-4">
                                <h3 class="card-title text-lg text-[#4E4E4E]">
                                    🌾 <?= htmlspecialchars($lahan['nama_lahan']) ?>
                                </h3>
                                <p class="text-sm text-base-content/70">
                                    📍 <?= htmlspecialchars($lahan['tempat_lahan']) ?>
                                </p>
                                <div class="badge badge-outline">
                                    🔧 <?= htmlspecialchars($lahan['device_id']) ?>
                                </div>

                                <!-- Status Section -->
                                <div class="mt-4 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Status:</span>
                                        <span class="loading loading-ring loading-sm" id="status-<?= $lahan['device_id'] ?>"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Kelembaban:</span>
                                        <span id="moisture-<?= $lahan['device_id'] ?>">--%</span>
                                    </div>
                                </div>

                                <!-- Control Buttons -->
                                <div class="card-actions justify-center mt-4 space-y-2">
                                    <div class="join join-horizontal w-full">
                                        <button class="btn btn-primary btn-sm join-item flex-1" onclick="manualIrrigation('<?= $lahan['device_id'] ?>')">
                                            💧 Siram Manual
                                        </button>
                                        <button class="btn btn-secondary btn-sm join-item flex-1" onclick="openTimerModal('<?= $lahan['device_id'] ?>')">
                                            ⏰ Set Timer
                                        </button>
                                    </div>
                                    <button class="btn btn-accent btn-sm w-full" onclick="openScheduleModal('<?= $lahan['device_id'] ?>')">
                                        📅 Jadwal Otomatis
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">🌱</div>
                    <p class="text-lg text-base-content/50">Belum ada lahan pintar.</p>
                    <p class="text-sm text-base-content/30">Tambahkan lahan pertama Anda di atas!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <dialog id="timerModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">⏰ Set Timer Penyiraman</h3>
            <div class="py-4">
                <label class="label">Interval (menit):</label>
                <input type="number" id="timerInterval" class="input input-bordered w-full" value="60" min="1">
                <label class="label">Durasi Siram (detik):</label>
                <input type="number" id="timerDuration" class="input input-bordered w-full" value="10" min="1">
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" onclick="setTimer()">Set Timer</button>
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
            </div>
        </div>
    </dialog>


    <dialog id="scheduleModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">📅 Jadwal Penyiraman Otomatis</h3>
            <div class="py-4">
                <label class="label">Waktu Siram:</label>
                <input type="time" id="scheduleTime" class="input input-bordered w-full" value="06:00">
                <label class="label">Durasi (detik):</label>
                <input type="number" id="scheduleDuration" class="input input-bordered w-full" value="30" min="1">
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" onclick="setSchedule()">Set Jadwal</button>
                <form method="dialog">
                    <button class="btn">Cancel</button>
                </form>
            </div>
        </div>
    </dialog>



    <script>
        let currentDeviceId = '';

        // Load status for all devices when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const deviceElements = document.querySelectorAll('[id^="lahan-"]');
            deviceElements.forEach(element => {
                const deviceId = element.id.replace('lahan-', '');
                loadDeviceStatus(deviceId);
            });

            // Auto refresh every 30 seconds
            setInterval(() => {
                deviceElements.forEach(element => {
                    const deviceId = element.id.replace('lahan-', '');
                    loadDeviceStatus(deviceId);
                });
            }, 30000);
        });

        async function loadDeviceStatus(deviceId) {
            try {
                const response = await fetch(`function/get_status.php?device_id=${deviceId}`);
                const data = await response.json();

                const statusElement = document.getElementById(`status-${deviceId}`);
                const moistureElement = document.getElementById(`moisture-${deviceId}`);

                if (data.online) {
                    statusElement.innerHTML = `<span class="badge badge-success">Online</span>`;
                    moistureElement.textContent = `${data.moisture_level}%`;
                } else {
                    statusElement.innerHTML = `<span class="badge badge-error">Offline</span>`;
                    moistureElement.textContent = '--';
                }
            } catch (error) {
                console.error('Error loading status:', error);
            }
        }

        async function manualIrrigation(deviceId) {
            const duration = prompt('Durasi penyiraman (detik):', '10');
            if (!duration) return;

            try {
                const response = await fetch('function/send_command.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        device_id: deviceId,
                        action: 'start_irrigation',
                        duration: parseInt(duration)
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    showToast('Perintah siram berhasil dikirim!', 'success');
                } else {
                    showToast('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        function openTimerModal(deviceId) {
            currentDeviceId = deviceId;
            document.getElementById('timerModal').showModal();
        }

        function openScheduleModal(deviceId) {
            currentDeviceId = deviceId;
            document.getElementById('scheduleModal').showModal();
        }

        async function setTimer() {
            const interval = document.getElementById('timerInterval').value;
            const duration = document.getElementById('timerDuration').value;

            try {
                const response = await fetch('function/send_command.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        device_id: currentDeviceId,
                        action: 'set_timer',
                        interval: parseInt(interval) * 60, // convert to seconds
                        duration: parseInt(duration)
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    showToast('Timer berhasil diset!', 'success');
                    document.getElementById('timerModal').close();
                } else {
                    showToast('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        }

        async function setSchedule() {
            const time = document.getElementById('scheduleTime').value;
            const duration = document.getElementById('scheduleDuration').value;

            // This would need more complex scheduling logic
            showToast('Fitur jadwal akan segera hadir!', 'info');
            document.getElementById('scheduleModal').close();
        }

        function showToast(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} fixed top-4 right-4 w-auto max-w-sm z-50`;
            toast.innerHTML = `<span>${message}</span>`;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>


    <script>
        var typed = new Typed('.selamat', {
            strings: [
                "Selamat Datang di Pertanian Pintar",
                "Pantau Lahan Anda Secara Cerdas",
                "Integrasi IoT untuk Pertanian Modern"
            ],
            typeSpeed: 50,
            backSpeed: 30,
            backDelay: 1500,
            loop: true
        });
    </script>
    <div class="fixed bottom-10 right-2 ">
        <script
            src='https://cdn.jotfor.ms/agent/embedjs/0197d993e310730e9d3145fd98455ecb6d21/embed.js?skipWelcome=1&maximizable=1'>
        </script>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                window.AgentInitializer.init({
                    rootId: "JotformAgent-0197d993e310730e9d3145fd98455ecb6d21",
                    formID: "0197d993e310730e9d3145fd98455ecb6d21",
                    queryParams: ["skipWelcome=1", "maximizable=1"],
                    domain: "https://www.jotform.com",
                    isInitialOpen: false,
                    isDraggable: false,
                    background: "linear-gradient(180deg, #6C73A8 0%, #6C73A8 100%)",
                    buttonBackgroundColor: "#0066C3",
                    buttonIconColor: "#FFFFFF",
                    variant: false,
                    customizations: {
                        greeting: "Yes",
                        greetingMessage: "Halo! Ada yang bisa saya bantu?",
                        pulse: "Yes",
                        position: "right"
                    }
                });
            });
        </script>

    </div>



</body>

</html>