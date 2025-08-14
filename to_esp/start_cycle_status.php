<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

$table = 'production_cycle_' . strtolower(str_replace(' ', '', $machine));

// Update cycle_status and timestamp
$sql = "UPDATE $table SET cycle_status = 0, timestamp = NOW() ORDER BY id DESC LIMIT 1";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Cycle reset"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>