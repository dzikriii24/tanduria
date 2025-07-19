<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Konfigurasi Wokwi
$WOKWI_PROJECT_ID = "436893272835873793";
$COMMANDS_FILE = 'json/wokwi_commands.json';
$STATUS_FILE = 'json/wokwi_status.json';

// Pastikan direktori json exists
if (!is_dir('json')) {
    mkdir('json', 0755, true);
}

switch ($method) {
    case 'POST':
        if (isset($_GET['action'])) {
            handleWebCommand($_GET['action'], $input);
        } else {
            handleCommand($input);
        }
        break;
    case 'GET':
        if (isset($_GET['commands'])) {
            getCommandsForWokwi();
        } elseif (isset($_GET['status'])) {
            handleStatusRequest();
        } else {
            getAllDevicesStatus();
        }
        break;
    case 'PUT':
        handleStatusUpdate($input);
        break;
    case 'DELETE':
        if (isset($_GET['command_id'])) {
            markCommandAsProcessed($_GET['command_id']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function handleWebCommand($action, $input) {
    global $conn, $COMMANDS_FILE;
    
    $device_id = $input['device_id'] ?? 'DEVICE_001';
    
    // Create command for Wokwi
    $command = [
        'id' => uniqid(),
        'device_id' => $device_id,
        'action' => $action,
        'timestamp' => time(),
        'status' => 'pending'
    ];
    
    // Add action-specific parameters
    switch ($action) {
        case 'start_irrigation':
            $command['duration'] = $input['duration'] ?? 10;
            logAction($device_id, 'web', $command['duration']);
            break;
        case 'stop_irrigation':
            logAction($device_id, 'stop', 0);
            break;
        case 'set_timer':
            $command['interval'] = $input['interval'] ?? 3600;
            logAction($device_id, 'timer_set', $command['interval']);
            break;
        case 'set_auto_irrigation':
            $command['enabled'] = $input['enabled'] ?? true;
            $command['threshold'] = $input['threshold'] ?? 30;
            logAction($device_id, 'auto_config', 0);
            break;
        case 'get_status':
            // No logging needed for status requests
            break;
    }
    
    // Save command to file that Wokwi can read
    $commands = loadJsonFile($COMMANDS_FILE);
    $commands[] = $command;
    saveJsonFile($COMMANDS_FILE, $commands);
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Command sent to Wokwi device',
        'command_id' => $command['id'],
        'action' => $action
    ]);
}

function getCommandsForWokwi() {
    global $COMMANDS_FILE;
    
    $device_id = $_GET['device_id'] ?? '';
    $commands = loadJsonFile($COMMANDS_FILE);
    
    if ($device_id) {
        // Filter commands for specific device
        $device_commands = array_filter($commands, function($cmd) use ($device_id) {
            return $cmd['device_id'] === $device_id && $cmd['status'] === 'pending';
        });
        echo json_encode(array_values($device_commands));
    } else {
        // Return all pending commands
        $pending_commands = array_filter($commands, function($cmd) {
            return $cmd['status'] === 'pending';
        });
        echo json_encode(array_values($pending_commands));
    }
}

function markCommandAsProcessed($command_id) {
    global $COMMANDS_FILE;
    
    $commands = loadJsonFile($COMMANDS_FILE);
    
    for ($i = 0; $i < count($commands); $i++) {
        if ($commands[$i]['id'] === $command_id) {
            $commands[$i]['status'] = 'processed';
            $commands[$i]['processed_at'] = time();
            break;
        }
    }
    
    saveJsonFile($COMMANDS_FILE, $commands);
    echo json_encode(['status' => 'success', 'message' => 'Command marked as processed']);
}

function handleCommand($input) {
    // Legacy method - redirect to new structure
    handleWebCommand($input['action'] ?? 'get_status', $input);
}

function handleStatusRequest() {
    global $STATUS_FILE;
    
    $device_id = $_GET['device_id'] ?? 'DEVICE_001';
    $status_data = loadJsonFile($STATUS_FILE);
    
    // Find status for this device
    $device_status = null;
    foreach ($status_data as $status) {
        if ($status['device_id'] === $device_id) {
            $device_status = $status;
            break;
        }
    }
    
    if (!$device_status) {
        $device_status = [
            'device_id' => $device_id,
            'online' => false,
            'pump_status' => false,
            'moisture_level' => 0,
            'temperature' => 20,
            'light_level' => 0,
            'auto_irrigation_enabled' => true,
            'moisture_threshold' => 30,
            'timer_enabled' => false,
            'timer_interval' => 3600,
            'pump_remaining' => 0,
            'last_update' => null,
            'timestamp' => time()
        ];
    }
    
    // Check if device is online (updated within last 30 seconds)
    $device_status['online'] = ($device_status['last_update'] && 
                               (time() - $device_status['last_update']) < 30);
    
    echo json_encode($device_status);
}

function getAllDevicesStatus() {
    global $STATUS_FILE, $conn;
    
    $status_data = loadJsonFile($STATUS_FILE);
    $devices = [];
    
    // Get registered devices from database
    $result = $conn->query("SELECT DISTINCT device_id FROM lahan_pintar");
    while ($row = $result->fetch_assoc()) {
        $device_id = $row['device_id'];
        
        // Find status for this device
        $device_status = null;
        foreach ($status_data as $status) {
            if ($status['device_id'] === $device_id) {
                $device_status = $status;
                break;
            }
        }
        
        if (!$device_status) {
            $device_status = [
                'device_id' => $device_id,
                'online' => false,
                'pump_status' => false,
                'moisture_level' => 0,
                'temperature' => 20,
                'light_level' => 0,
                'last_update' => null
            ];
        }
        
        $device_status['online'] = ($device_status['last_update'] && 
                                   (time() - $device_status['last_update']) < 30);
        
        $devices[] = $device_status;
    }
    
    echo json_encode(['devices' => $devices]);
}

function handleStatusUpdate($input) {
    global $STATUS_FILE;
    
    // This endpoint is called by Wokwi to update device status
    if (!$input || !isset($input['device_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input - device_id required']);
        return;
    }
    
    $status_data = loadJsonFile($STATUS_FILE);
    $input['last_update'] = time();
    $input['online'] = true;
    
    // Update or add device status
    $found = false;
    for ($i = 0; $i < count($status_data); $i++) {
        if ($status_data[$i]['device_id'] === $input['device_id']) {
            // Merge with existing data, preserving important fields
            $status_data[$i] = array_merge($status_data[$i], $input);
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $status_data[] = $input;
    }
    
    saveJsonFile($STATUS_FILE, $status_data);
    
    // Log sensor data to database (optional)
    logSensorData($input);
    
    echo json_encode(['status' => 'success', 'message' => 'Status updated']);
}

function logAction($device_id, $action_type, $duration) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO irrigation_logs (device_id, action_type, duration, status, timestamp) VALUES (?, ?, ?, 'executed', NOW())");
    $stmt->bind_param("ssi", $device_id, $action_type, $duration);
    $stmt->execute();
}

function logSensorData($data) {
    global $conn;
    
    if (isset($data['moisture_level'], $data['temperature'], $data['light_level'])) {
        $stmt = $conn->prepare("INSERT INTO sensor_logs (device_id, moisture_level, temperature, light_level, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("siii", 
            $data['device_id'], 
            $data['moisture_level'], 
            $data['temperature'], 
            $data['light_level']
        );
        $stmt->execute();
    }
}

function loadJsonFile($filename) {
    if (!file_exists($filename)) {
        return [];
    }
    
    $content = file_get_contents($filename);
    $decoded = json_decode($content, true);
    return $decoded ?: [];
}

function saveJsonFile($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filename, $json);
}

// Cleanup old commands and status
function cleanupOldData() {
    global $COMMANDS_FILE, $STATUS_FILE;
    
    // Cleanup commands older than 2 hours
    $commands = loadJsonFile($COMMANDS_FILE);
    $current_time = time();
    
    $commands = array_filter($commands, function($cmd) use ($current_time) {
        return ($current_time - $cmd['timestamp']) < 7200; // 2 hours
    });
    
    saveJsonFile($COMMANDS_FILE, array_values($commands));
    
    // Mark devices as offline if not updated in 5 minutes
    $status_data = loadJsonFile($STATUS_FILE);
    $updated = false;
    
    for ($i = 0; $i < count($status_data); $i++) {
        if ($status_data[$i]['last_update'] && 
            ($current_time - $status_data[$i]['last_update']) > 300) { // 5 minutes
            $status_data[$i]['online'] = false;
            $updated = true;
        }
    }
    
    if ($updated) {
        saveJsonFile($STATUS_FILE, $status_data);
    }
}

// Run cleanup
cleanupOldData();

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>