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
$waterFlow = isset($_GET['waterFlow']) ? floatval($_GET['waterFlow']) : null;
$airFlow = isset($_GET['airFlow']) ? floatval($_GET['airFlow']) : null;

// Calculate tempF_01 and tempF_02 if temp1 and temp2 are provided
$tempF_01 = ($temp1 !== null) ? ($temp1 * 9 / 5) + 32 : null;
$tempF_02 = ($temp2 !== null) ? ($temp2 * 9 / 5) + 32 : null;

$currentMinute = date('Y-m-d H:i:00'); // Round to start of the minute

// Check if an entry for the current minute already exists
$stmt = $conn->prepare("SELECT id FROM realtime_parameters WHERE timestamp = ?");
$stmt->bind_param("s", $currentMinute);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If an entry exists, update it
    if ($waterFlow !== null) { // WATER FLOW
        $stmt = $conn->prepare("UPDATE realtime_parameters SET water_flow = ? WHERE timestamp = ?");
        $stmt->bind_param("ds", $waterFlow, $currentMinute);
    }
    
    elseif ($airFlow !== null) { // AIR FLOW
        $stmt = $conn->prepare("UPDATE realtime_parameters SET air_flow = ? WHERE timestamp = ?");
        $stmt->bind_param("ds", $airFlow, $currentMinute);
    }
    
    elseif ($temp1 !== null && $temp2 !== null) { // TEMPERATURES
        $stmt = $conn->prepare("UPDATE realtime_parameters SET tempC_01 = ?, tempF_01 = ?, tempC_02 = ?, tempF_02 = ? WHERE timestamp = ?");
        $stmt->bind_param("dddss", $temp1, $tempF_01, $temp2, $tempF_02, $currentMinute);
    }
} else {
    // If no entry exists, insert a new one
    if ($waterFlow !== null) { // WATER FLOW
        $stmt = $conn->prepare("INSERT INTO realtime_parameters (water_flow, timestamp) VALUES (?, ?)");
        $stmt->bind_param("ds", $waterFlow, $currentMinute);
    }
    
    elseif ($airFlow !== null) { // AIR FLOW
        $stmt = $conn->prepare("INSERT INTO realtime_parameters (air_flow, timestamp) VALUES (?, ?)");
        $stmt->bind_param("ds", $airFlow, $currentMinute);
    }
    
    elseif ($temp1 !== null && $temp2 !== null) { // TEMPERATURES
        $stmt = $conn->prepare("INSERT INTO realtime_parameters (tempC_01, tempF_01, tempC_02, tempF_02, timestamp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("dddss", $temp1, $tempF_01, $temp2, $tempF_02, $currentMinute);
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
