<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

require_once __DIR__ . '/db_config.php';

// Read machine from POST or GET
$machine = $_POST['machine'] ?? $_GET['machine'] ?? '';

if (empty($machine)) {
    echo json_encode(["found" => false, "error" => "Missing machine parameter"]);
    exit;
}

$table = 'production_cycle_' . strtolower(str_replace(' ', '', $machine));

$sql = "UPDATE $table SET cycle_status = 0, timestamp = NOW() ORDER BY id DESC LIMIT 1";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Cycle reset for $machine"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>
