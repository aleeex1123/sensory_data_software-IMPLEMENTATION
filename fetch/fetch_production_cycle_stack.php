<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Sanitize machine name
$machine = isset($_GET['machine']) ? $_GET['machine'] : '';
$machine_safe = strtolower(str_replace(' ', '', $machine));
$tableName = "production_cycle_" . $machine_safe;

// Get latest row
$latestRow = $conn->query("SELECT * FROM `$tableName` ORDER BY id DESC LIMIT 1")->fetch_assoc();
if (!$latestRow) {
    echo json_encode(["error" => "No data found"]);
    exit;
}
$latestId = $latestRow['id'];

// Find the start row
$startRow = $conn->query("
    SELECT * FROM `$tableName`
    WHERE recycle_time = 0 
      AND processing_time IS NOT NULL
      AND id < $latestId
    ORDER BY id DESC
    LIMIT 1
")->fetch_assoc();

if (!$startRow) {
    echo json_encode(["error" => "No matching start row found"]);
    exit;
}

$startId = $startRow['id'];

// Fetch all rows between start and latest
$sql = "SELECT * FROM `$tableName` 
        WHERE id >= $startId AND id <= $latestId
        ORDER BY id ASC";
$result = $conn->query($sql);

$segments = [];
while ($row = $result->fetch_assoc()) {
    // Red segment – Mold Open
    if ($row['recycle_time'] > 0) {
        $segments[] = [
            'type' => 'mold-open_hourly',
            'size' => (int)$row['recycle_time'],
            'label' => "Mold open for {$row['recycle_time']}s",
            'product' => (explode('|', $row['product'])[0]),
            'temp1' => $row['tempC_01'],
            'temp2' => $row['tempC_02']
        ];
    }

    // Green segment – Mold Closed
    if ($row['processing_time'] > 0) {
        $segments[] = [
            'type' => 'mold-closed_hourly',
            'size' => (int)$row['processing_time'],
            'label' => "Mold closed for {$row['processing_time']}s",
            'product' => (explode('|', $row['product'])[0]),
            'temp1' => $row['tempC_01'],
            'temp2' => $row['tempC_02']
        ];
    }

    // Gray segment – Inactive
    $inactive = (int)$row['cycle_time'] - ((int)$row['recycle_time'] + (int)$row['processing_time']);
    if ($inactive > 0) {
        $segments[] = [
            'type' => 'inactive_hourly',
            'size' => $inactive,
            'label' => "Inactive for {$inactive}s",
            'product' => (explode('|', $row['product'])[0]),
            'temp1' => $row['tempC_01'],
            'temp2' => $row['tempC_02']
        ];
    }
}

echo json_encode(["segments" => $segments]);
