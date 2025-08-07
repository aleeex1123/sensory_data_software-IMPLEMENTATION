<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$machine = $_POST['machine'] ?? '';

if (!$machine) {
    echo json_encode(["status" => "error", "message" => "Missing machine parameter"]);
    exit;
}

$table = 'production_cycle_' . strtolower(str_replace(' ', '', $machine));

// Update cycle_status and timestamp
$sql = "UPDATE $table SET cycle_status = 0, timestamp = NOW() ORDER BY id DESC LIMIT 1";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Cycle reset"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
