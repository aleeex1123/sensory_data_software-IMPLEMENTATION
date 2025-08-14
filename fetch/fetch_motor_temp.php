<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once 'db_config.php';

$type = $_GET['type'] ?? '';
$machine = $_GET['machine'] ?? '';

if ($type === 'realtime') {
    $stmt = $conn->prepare("SELECT motor_tempC_01, motor_tempC_02, DATE_FORMAT(timestamp, '%H:%i') as time FROM motor_temperatures WHERE machine = ? ORDER BY timestamp DESC LIMIT 10");
    $stmt->bind_param("s", $machine);
    $stmt->execute();
    $result = $stmt->get_result();

    $temp1 = [];
    $temp2 = [];
    $timestamps = [];

    while ($row = $result->fetch_assoc()) {
        $temp1[] = floatval($row['motor_tempC_01']);
        $temp2[] = floatval($row['motor_tempC_02']);
        $timestamps[] = $row['time'];
    }

    echo json_encode([
        "motor_tempC_01" => $temp1,
        "motor_tempC_02" => $temp2,
        "timestamps" => $timestamps
    ]);
}
else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
