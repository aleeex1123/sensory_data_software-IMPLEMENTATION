<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Use machine name directly
$machine = $_GET['machine'];
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($_GET['machine']));

// Check if the table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(["error" => "Table $table_name does not exist"]);
    exit;
}

// Fetch latest production cycle data
$sql = "SELECT tempC_01, tempC_02, cycle_status, product FROM `$table_name` ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$data = $result->fetch_assoc() ?: ["error" => "No data found"];

$conn->close();
echo json_encode($data);
?>
