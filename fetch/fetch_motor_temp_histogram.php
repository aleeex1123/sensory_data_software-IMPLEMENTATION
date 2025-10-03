<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

require_once __DIR__ . '/db_config.php';

$machine = $_GET['machine'] ?? '';
$month   = isset($_GET['month']) ? (int)$_GET['month'] : 0;

if (!$machine) {
    http_response_code(400);
    echo json_encode(["error" => "Missing machine parameter"]);
    exit;
}

$safeMachine = $conn->real_escape_string($machine);
$currentMonth = (int)date('n'); // 1-12

// Decide which table(s) to use
if ($month === 0) {
    // Fetch from both tables
    $sql = "
        SELECT motor_tempC_01, motor_tempC_02 
        FROM motor_temperatures 
        WHERE machine='$safeMachine'
        UNION ALL
        SELECT motor_tempC_01, motor_tempC_02 
        FROM motor_temperatures_archive 
        WHERE machine='$safeMachine'
    ";
} elseif ($month === $currentMonth) {
    // Current month → live table
    $sql = "
        SELECT motor_tempC_01, motor_tempC_02 
        FROM motor_temperatures 
        WHERE machine='$safeMachine' 
          AND MONTH(`timestamp`) = $month
    ";
} else {
    // Past month → archive table
    $sql = "
        SELECT motor_tempC_01, motor_tempC_02 
        FROM motor_temperatures_archive 
        WHERE machine='$safeMachine' 
          AND MONTH(`timestamp`) = $month
    ";
}

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$temp1 = [];
$temp2 = [];

while ($row = $result->fetch_assoc()) {
    if ($row['motor_tempC_01'] !== null) $temp1[] = (float)$row['motor_tempC_01'];
    if ($row['motor_tempC_02'] !== null) $temp2[] = (float)$row['motor_tempC_02'];
}

// If no data
if (empty($temp1) && empty($temp2)) {
    echo json_encode([
        "labels" => [],
        "temp1Freq" => [],
        "temp2Freq" => []
    ]);
    exit;
}

// Combine for global min/max
$allTemps = array_merge($temp1, $temp2);
$minTemp = floor(min($allTemps));
$maxTemp = ceil(max($allTemps));

// Bin count (max 20)
$binCount = min(20, max(1, $maxTemp - $minTemp + 1));
$binSize = ($maxTemp - $minTemp) / $binCount;

$labels = [];
$temp1Freq = array_fill(0, $binCount, 0);
$temp2Freq = array_fill(0, $binCount, 0);

for ($i = 0; $i < $binCount; $i++) {
    $start = $minTemp + $i * $binSize;
    $end = $start + $binSize;
    $labels[] = round(($start + $end) / 2, 1); // midpoint label
}

// Fill bins
foreach ($temp1 as $t) {
    $index = floor(($t - $minTemp) / $binSize);
    if ($index >= $binCount) $index = $binCount - 1;
    $temp1Freq[$index]++;
}

foreach ($temp2 as $t) {
    $index = floor(($t - $minTemp) / $binSize);
    if ($index >= $binCount) $index = $binCount - 1;
    $temp2Freq[$index]++;
}

echo json_encode([
    "labels" => $labels,
    "temp1Freq" => $temp1Freq,
    "temp2Freq" => $temp2Freq
]);
?>
