<?php
require 'db.php';
session_start();

// [Kode yang sudah ada untuk proses form...]
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
// [Kode yang sudah ada untuk proses form...]

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lahan Pintar - Tanduria</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-base-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-8 text-primary">🌱 Lahan Pintar Tanduria</h1>

        <!-- Form Tambah Lahan -->
        <div class="card bg-base-200 shadow-xl mb-8">
            <div class="card-body">
                <h2 class="card-title text-secondary">Tambah Lahan Pintar</h2>
                <div class="max-w-md">
                    <p class="mb-5 text-base-content/70">
                        Mulai tambahkan lahan pintar anda dibawah ini, silahkan pilih lahan pintar anda dan masukan nomer mesin. Jika anda bingung, boleh tanya ke AI Agent kami yaa!!
                    </p>
                    <?php if (count($lahanAvailable) > 0): ?>
                        <form method="post" class="flex flex-col gap-4 sm:flex-row">
                            <select class="select select-bordered flex-1" name="lahan_id" required>
                                <option disabled selected>Pilih Lahan Pintar Anda</option>
                                <?php foreach ($lahanAvailable as $lahan): ?>
                                    <option value="<?= $lahan['id'] ?>">
                                        <?= htmlspecialchars($lahan['nama_lahan']) ?> - <?= htmlspecialchars($lahan['tempat_lahan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="device_id" placeholder="Masukan ID Device IoT" class="input input-bordered flex-1" required />
                            <button class="btn btn-success" type="submit">
                                <span class="loading loading-spinner loading-sm hidden"></span>
                                Tambah Lahan
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <span>Semua lahan Anda sudah ditambahkan sebagai lahan pintar.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success mb-6">
                <span>✓ Lahan pintar berhasil ditambahkan!</span>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-error mb-6">
                <span>✗ <?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Daftar Lahan Pintar -->
        <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-4">🏡 Daftar Lahan Pintar Anda</h2>

                <?php if (count($lahanPintar) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($lahanPintar as $lahan): ?>
                            <div class="card bg-base-100 shadow-lg border border-base-300" id="lahan-<?= $lahan['device_id'] ?>">
                                <div class="card-body p-4">
                                    <h3 class="card-title text-lg text-primary">
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
    </div>

    <!-- Modal Timer -->
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

    <!-- Modal Schedule -->
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
</body>

</html>