<?php
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "sensory_data";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Validate inputs
$machine = isset($_GET['machine']) ? $_GET['machine'] : '';
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

if (!$machine) {
    echo json_encode(["status" => "error", "message" => "No machine specified"]);
    exit;
}

// Convert machine name to lowercase, remove spaces
$table = "production_cycle_" . strtolower(str_replace(' ', '', $machine));

// Check if table exists
$check = $conn->query("SHOW TABLES LIKE '$table'");
if (!$check || $check->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Machine table not found"]);
    exit;
}

// Determine full month date range
$year = date('Y');
$startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date("Y-m-t", strtotime($startDate));

$sql = "SELECT DATE(timestamp) as day,
               ROUND(AVG(cycle_time), 2) as avg_cycle_time,
               ROUND(MIN(cycle_time), 2) as min_cycle_time,
               ROUND(MAX(cycle_time), 2) as max_cycle_time
        FROM `$table`
        WHERE cycle_time > 0
          AND timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
          AND MONTH(timestamp) = $month AND YEAR(timestamp) = $year
        GROUP BY day
        ORDER BY day ASC";

$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["status" => "error", "message" => "Query failed"]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'date' => $row['day'],
        'average' => (float)$row['avg_cycle_time'],
        'min' => (float)$row['min_cycle_time'],
        'max' => (float)$row['max_cycle_time']
    ];
}

echo json_encode(["status" => "success", "data" => $data]);
$conn->close();
