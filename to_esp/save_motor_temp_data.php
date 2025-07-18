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

// Get data from GET request
$temp1 = isset($_GET['temp1']) ? floatval($_GET['temp1']) : null;
$temp2 = isset($_GET['temp2']) ? floatval($_GET['temp2']) : null;
$machine = isset($_GET['machine']) ? $_GET['machine'] : null;

// Calculate tempF_01 and tempF_02 if temp1 and temp2 are provided
$tempF_01 = ($temp1 !== null) ? ($temp1 * 9 / 5) + 32 : null;
$tempF_02 = ($temp2 !== null) ? ($temp2 * 9 / 5) + 32 : null;

// Format the timestamp to Y-m-d H:i:00 (round to start of the minute with 00 seconds)
$currentMinute = date('Y-m-d H:i:00'); 

// Only proceed if machine and temps are provided
if ($machine !== null && $temp1 !== null && $temp2 !== null) {
    // Check if an entry for the same machine and current minute exists
    $stmt = $conn->prepare("SELECT id FROM motor_temperatures WHERE timestamp = ? AND machine = ?");
    $stmt->bind_param("ss", $currentMinute, $machine);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Entry already exists, update it
        $stmt = $conn->prepare("UPDATE motor_temperatures SET motor_tempC_01 = ?, motor_tempF_01 = ?, motor_tempC_02 = ?, motor_tempF_02 = ? WHERE timestamp = ? AND machine = ?");
        $stmt->bind_param("ddddss", $temp1, $tempF_01, $temp2, $tempF_02, $currentMinute, $machine);
    } else {
        // No entry exists, insert a new row
        $stmt = $conn->prepare("INSERT INTO motor_temperatures (motor_tempC_01, motor_tempF_01, motor_tempC_02, motor_tempF_02, timestamp, machine) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ddddss", $temp1, $tempF_01, $temp2, $tempF_02, $currentMinute, $machine);
    }

    if ($stmt && $stmt->execute()) {
        echo "Data saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: Missing required parameters (machine, temp1, or temp2)";
}

$conn->close();
?>
