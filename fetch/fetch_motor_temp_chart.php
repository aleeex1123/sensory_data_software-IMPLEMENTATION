<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = date('Y');

$sql = "SELECT DATE(timestamp) as date, 
            AVG(motor_tempC_01) as motor_tempC_01, 
            AVG(motor_tempC_02) as motor_tempC_02
        FROM motor_temperatures
        WHERE MONTH(timestamp) = $month AND YEAR(timestamp) = $year
        GROUP BY DATE(timestamp) 
        ORDER BY timestamp ASC";

$result = $conn->query($sql);

$data = [
    'days' => [],
    'motor_tempC_01' => [],
    'motor_tempC_02' => []
];

while ($row = $result->fetch_assoc()) {
    $data['days'][] = date('d', strtotime($row['date']));
    $data['motor_tempC_01'][] = round($row['motor_tempC_01'], 2);
    $data['motor_tempC_02'][] = round($row['motor_tempC_02'], 2);
}

echo json_encode($data);
?>
