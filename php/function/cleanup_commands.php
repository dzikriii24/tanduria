<?php
// File: cleanup_commands.php
header('Content-Type: application/json');

$commands_file = 'json/wokwi_command.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'status';
    
    switch ($action) {
        case 'status':
            if (file_exists($commands_file)) {
                $commands = json_decode(file_get_contents($commands_file), true) ?: [];
                
                $stats = [
                    'total' => count($commands),
                    'executed' => 0,
                    'pending' => 0,
                    'by_device' => [],
                    'by_action' => [],
                    'oldest' => null,
                    'newest' => null
                ];
                
                foreach ($commands as $cmd) {
                    // Count executed vs pending
                    if ($cmd['executed']) {
                        $stats['executed']++;
                    } else {
                        $stats['pending']++;
                    }
                    
                    // Group by device
                    $device = $cmd['device_id'];
                    $stats['by_device'][$device] = ($stats['by_device'][$device] ?? 0) + 1;
                    
                    // Group by action
                    $action = $cmd['action'];
                    $stats['by_action'][$action] = ($stats['by_action'][$action] ?? 0) + 1;
                    
                    // Track oldest and newest
                    if ($stats['oldest'] === null || $cmd['timestamp'] < $stats['oldest']['timestamp']) {
                        $stats['oldest'] = $cmd;
                        $stats['oldest']['human_time'] = date('Y-m-d H:i:s', $cmd['timestamp']);
                    }
                    if ($stats['newest'] === null || $cmd['timestamp'] > $stats['newest']['timestamp']) {
                        $stats['newest'] = $cmd;
                        $stats['newest']['human_time'] = date('Y-m-d H:i:s', $cmd['timestamp']);
                    }
                }
                
                echo json_encode([
                    'status' => 'success',
                    'file_exists' => true,
                    'current_server_time' => [
                        'timestamp' => time(),
                        'human' => date('Y-m-d H:i:s'),
                        'timezone' => date_default_timezone_get()
                    ],
                    'statistics' => $stats
                ], JSON_PRETTY_PRINT);
            } else {
                echo json_encode([
                    'status' => 'info',
                    'message' => 'Commands file does not exist yet'
                ]);
            }
            break;
            
        case 'cleanup':
            if (file_exists($commands_file)) {
                $commands = json_decode(file_get_contents($commands_file), true) ?: [];
                $original_count = count($commands);
                
                // Remove executed commands older than 1 hour
                $one_hour_ago = time() - 3600;
                $commands = array_filter($commands, function($cmd) use ($one_hour_ago) {
                    return !($cmd['executed'] && $cmd['timestamp'] < $one_hour_ago);
                });
                
                // Re-index array
                $commands = array_values($commands);
                
                file_put_contents($commands_file, json_encode($commands, JSON_PRETTY_PRINT));
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cleanup completed',
                    'removed' => $original_count - count($commands),
                    'remaining' => count($commands)
                ]);
            } else {
                echo json_encode([
                    'status' => 'info',
                    'message' => 'No commands file to cleanup'
                ]);
            }
            break;
            
        case 'clear_all':
            if (file_exists($commands_file)) {
                unlink($commands_file);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'All commands cleared'
                ]);
            } else {
                echo json_encode([
                    'status' => 'info',
                    'message' => 'Commands file already empty'
                ]);
            }
            break;
            
        case 'fix_timestamp':
            if (file_exists($commands_file)) {
                $commands = json_decode(file_get_contents($commands_file), true) ?: [];
                $current_time = time();
                
                foreach ($commands as &$cmd) {
                    // If timestamp is in future, fix it
                    if ($cmd['timestamp'] > $current_time) {
                        $cmd['timestamp'] = $current_time - 60; // Set to 1 minute ago
                        $cmd['timestamp_fixed'] = true;
                    }
                }
                
                file_put_contents($commands_file, json_encode($commands, JSON_PRETTY_PRINT));
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Timestamps fixed',
                    'current_server_time' => date('Y-m-d H:i:s', $current_time)
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'status' => 'info',
                'available_actions' => [
                    'status' => 'Show commands statistics',
                    'cleanup' => 'Remove old executed commands',
                    'clear_all' => 'Clear all commands',
                    'fix_timestamp' => 'Fix future timestamps'
                ],
                'usage' => 'Add ?action=status to URL'
            ]);
    }
}
?>