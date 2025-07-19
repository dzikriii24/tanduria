<?php
header('Content-Type: application/json');
require '../db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['device_id'], $input['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$device_id = $input['device_id'];
$action = $input['action'];

// Validate device exists
$stmt = $conn->prepare("SELECT id FROM lahan_pintar WHERE device_id = ?");
$stmt->bind_param("s", $device_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Device not found']);
    exit;
}

// Create command
$command = [
    'device_id' => $device_id,
    'action' => $action,
    'timestamp' => time()
];

// Add action-specific parameters
switch ($action) {
    case 'start_irrigation':
        $command['duration'] = $input['duration'] ?? 5;
        break;
    case 'set_timer':
        $command['interval'] = $input['interval'] ?? 3600;
        break;
}

// Save command to file
$commands = [];
if (file_exists('commands.json')) {
    $commands = json_decode(file_get_contents('commands.json'), true) ?: [];
}

$commands[] = $command;
file_put_contents('commands.json', json_encode($commands, JSON_PRETTY_PRINT));

// Log to database
$action_type = ($action === 'start_irrigation') ? 'manual' : $action;
$duration = $command['duration'] ?? 0;

$stmt = $conn->prepare("INSERT INTO irrigation_logs (device_id, action_type, duration) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $device_id, $action_type, $duration);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Command sent']);
?>