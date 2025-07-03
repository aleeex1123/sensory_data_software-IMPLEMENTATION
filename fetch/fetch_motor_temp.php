<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check request type
$type = isset($_GET['type']) ? $_GET['type'] : 'cycle'; 

if ($type === 'realtime') {
    $sql = "SELECT motor_tempC_01, motor_tempC_02 FROM motor_temperatures ORDER BY timestamp DESC LIMIT 10";
    $result = $conn->query($sql);

    $data = ["motor_tempC_01" => [], "motor_tempC_02" => []];

    while ($row = $result->fetch_assoc()) {
        $data["motor_tempC_01"][] = $row["motor_tempC_01"];
        $data["motor_tempC_02"][] = $row["motor_tempC_02"];
    }
}

$conn->close();
echo json_encode($data);
?>

