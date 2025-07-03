<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch latest production cycle data
$sql = "SELECT tempC_01, tempC_02, pressure, cycle_status, product FROM production_cycle ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$data = $result->fetch_assoc() ?: ["error" => "No data found"];

$conn->close();
echo json_encode($data);
?>
