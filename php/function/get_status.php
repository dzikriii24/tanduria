<?php
header('Content-Type: application/json');
require '../db.php';

$device_id = $_GET['device_id'] ?? '';

if (!$device_id) {
    echo json_encode(['status' => 'error', 'message' => 'Device ID required']);
    exit;
}

// Get latest logs
$stmt = $conn->prepare("
    SELECT action_type, duration, executed_time, status 
    FROM irrigation_logs 
    WHERE device_id = ? 
    ORDER BY id DESC 
    LIMIT 10
");
$stmt->bind_param("s", $device_id);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Simulate device status (in real implementation, this would come from Wokwi)
$status = [
    'device_id' => $device_id,
    'online' => true,
    'pump_status' => false,
    'moisture_level' => rand(30, 80),
    'last_irrigation' => $logs[0]['executed_time'] ?? null,
    'logs' => $logs
];

echo json_encode($status);
?>