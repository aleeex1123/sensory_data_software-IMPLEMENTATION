<?php
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$waterFlow = isset($_GET['waterFlow']) ? floatval($_GET['waterFlow']) : null;

$currentMinute = date('Y-m-d H:i:00'); // Round to start of the minute

// Check if an entry for the current minute already exists
$stmt = $conn->prepare("SELECT id FROM water_flow WHERE timestamp = ?");
$stmt->bind_param("s", $currentMinute);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If an entry exists, update it
    if ($waterFlow !== null) { // WATER FLOW
        $stmt = $conn->prepare("UPDATE water_flow SET hose_01 = ? WHERE timestamp = ?");
        $stmt->bind_param("ds", $waterFlow, $currentMinute);
    }
} else {
    // If no entry exists, insert a new one
    if ($waterFlow !== null) { // WATER FLOW
        $stmt = $conn->prepare("INSERT INTO water_flow (hose_01, timestamp) VALUES (?, ?)");
        $stmt->bind_param("ds", $waterFlow, $currentMinute);
    }
}

if ($stmt->execute()) {
    echo "Data saved successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
