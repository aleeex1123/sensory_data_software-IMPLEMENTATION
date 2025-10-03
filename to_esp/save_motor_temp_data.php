<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Sanitize and validate GET inputs
$machine = isset($_GET['machine']) ? trim($_GET['machine']) : null;
$temp1   = isset($_GET['temp1']) ? floatval($_GET['temp1']) : null;
$temp2   = isset($_GET['temp2']) ? floatval($_GET['temp2']) : null;

// Adjust temp1 by adding 25
$temp1 = $temp1 + 25; 

// Only proceed if all required params are provided
if (!$machine || $temp1 === null || $temp2 === null) {
    echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
    exit;
}

// Pre-calculate Fahrenheit
$tempF_01 = ($temp1 * 9 / 5) + 32;
$tempF_02 = ($temp2 * 9 / 5) + 32;

// ✅ Timestamp rounded to the current minute (00 seconds)
$timestamp = date('Y-m-d H:i:00');

// Use INSERT ... ON DUPLICATE KEY UPDATE to avoid double queries
// Ensure that (`timestamp`, machine) is set as a UNIQUE key in your DB
$sql = "
INSERT INTO motor_temperatures 
    (motor_tempC_01, motor_tempF_01, motor_tempC_02, motor_tempF_02, `timestamp`, machine) 
VALUES (?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
    motor_tempC_01 = VALUES(motor_tempC_01),
    motor_tempF_01 = VALUES(motor_tempF_01),
    motor_tempC_02 = VALUES(motor_tempC_02),
    motor_tempF_02 = VALUES(motor_tempF_02)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ddddss", $temp1, $tempF_01, $temp2, $tempF_02, $timestamp, $machine);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data saved"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
