<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = date('Y');

$sql = "SELECT DATE(timestamp) as date, 
            AVG(hose_01) as hose_01, 
            AVG(hose_02) as hose_02, 
            AVG(hose_03) as hose_03, 
            AVG(hose_04) as hose_04,
            AVG(hose_05) as hose_05 
        FROM water_flow
        WHERE MONTH(timestamp) = $month AND YEAR(timestamp) = $year
        GROUP BY DATE(timestamp) 
        ORDER BY timestamp ASC";

$result = $conn->query($sql);

$data = [
    'days' => [],
    'hose_01' => [],
    'hose_02' => [],
    'hose_03' => [],
    'hose_04' => [],
    'hose_05' => []
];

while ($row = $result->fetch_assoc()) {
    $data['days'][] = date('d', strtotime($row['date']));
    $data['hose_01'][] = round($row['hose_01'], 2);
    $data['hose_02'][] = round($row['hose_02'], 2);
    $data['hose_03'][] = round($row['hose_03'], 2);
    $data['hose_04'][] = round($row['hose_04'], 2);
    $data['hose_05'][] = round($row['hose_05'], 2);
}

echo json_encode($data);
?>
