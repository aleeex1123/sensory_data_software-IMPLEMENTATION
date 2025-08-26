<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

require_once __DIR__ . '/db_config.php';

$timestamp = date("Y-m-d H:i:s");

// Read machine from POST or GET
$machine = $_POST['machine'] ?? $_GET['machine'] ?? '';

if (empty($machine)) {
    echo json_encode(["found" => false, "error" => "Missing machine parameter"]);
    exit;
}

$table = 'production_cycle_' . strtolower(str_replace(' ', '', $machine));

// ✅ Use prepared statement with timestamp variable
$stmt = $conn->prepare("UPDATE `$table` 
                        SET cycle_status = 0, `timestamp` = ? 
                        ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $timestamp);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Cycle reset for $machine"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
