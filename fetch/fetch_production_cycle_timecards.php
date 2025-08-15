<?php
// Step 1: Connect to product_parameters DB (DMS) - conn2
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/db_config_2.php';

$product = isset($_GET['product']) ? $conn->real_escape_string($_GET['product']) : '';
$machine = isset($_GET['machine']) ? $conn->real_escape_string($_GET['machine']) : '';
$limit   = isset($_GET['show']) ? intval($_GET['show']) : 20;
$month   = isset($_GET['month']) ? intval($_GET['month']) : date('n');

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($machine));

// Step 3: Get mold number from production cycle table (sensory_data)
$moldQuery = "SELECT mold_number FROM `$table` WHERE product LIKE '%" . $conn->real_escape_string($product) . "%' LIMIT 1";
$moldResult = $conn->query($moldQuery);
$mold_number = "";

if ($moldResult && $row = $moldResult->fetch_assoc()) {
    $mold_number = $row['mold_number'];
}

// Step 4: Get cycle_time_target from product_parameters (dms)
$standard = 0;
if ($mold_number !== "") {
    $paramQuery = "SELECT cycle_time_target FROM product_parameters WHERE mold_code = '$mold_number'";
    $paramResult = $conn2->query($paramQuery);
    if ($paramResult && $param = $paramResult->fetch_assoc()) {
        $standard = floatval($param['cycle_time_target']);
    }
}

// Step 5: Fetch past cycle data from sensory_data
$entries = [];

$dataQuery = "
    SELECT cycle_time, processing_time, recycle_time, timestamp
    FROM `$table`
    WHERE 1
      " . ($product ? " AND product LIKE '%$product%'" : "") . "
      " . ($month > 0 ? " AND MONTH(timestamp) = $month" : "") . "
      AND cycle_time IS NOT NULL
    ORDER BY timestamp DESC
    LIMIT $limit
";

$dataResult = $conn->query($dataQuery);

if ($dataResult && $dataResult->num_rows > 0) {
    $cycleTimes = [];
    $processingTimes = [];
    $recycleTimes = [];

    while ($row = $dataResult->fetch_assoc()) {
        $entries[] = $row;
        $cycleTimes[] = floatval($row['cycle_time']);
        $processingTimes[] = floatval($row['processing_time']);
        $recycleTimes[] = floatval($row['recycle_time']);
    }

    $avgCycleTime = round(array_sum($cycleTimes) / count($cycleTimes), 2);
    $maxCycleTime = round(max($cycleTimes), 2);

    $avgProcessingTime = round(array_sum($processingTimes) / count($processingTimes), 2);
    $maxProcessingTime = round(max($processingTimes), 2);

    $avgRecycleTime = round(array_sum($recycleTimes) / count($recycleTimes), 2);
    $maxRecycleTime = round(max($recycleTimes), 2);

    function getMinNonZero($array) {
        $nonZero = array_filter($array, fn($v) => $v > 0);
        return count($nonZero) > 0 ? round(min($nonZero), 2) : 0;
    }

    $minCycleTime = getMinNonZero($cycleTimes);
    $minProcessingTime = getMinNonZero($processingTimes);
    $minRecycleTime = getMinNonZero($recycleTimes);
} else {
    $avgCycleTime = $minCycleTime = $maxCycleTime = 0;
    $avgProcessingTime = $minProcessingTime = $maxProcessingTime = 0;
    $avgRecycleTime = $minRecycleTime = $maxRecycleTime = 0;
}

$response = [
    'standard' => [
        'cycle' => $standard,
        'processing' => $standard / 2,
        'recycle' => $standard / 2
    ],
    'average' => [
        'cycle' => $avgCycleTime,
        'processing' => $avgProcessingTime,
        'recycle' => $avgRecycleTime
    ],
    'minimum' => [
        'cycle' => $minCycleTime,
        'processing' => $minProcessingTime,
        'recycle' => $minRecycleTime
    ],
    'maximum' => [
        'cycle' => $maxCycleTime,
        'processing' => $maxProcessingTime,
        'recycle' => $maxRecycleTime
    ],
    'entries' => $entries
];

echo json_encode($response);

$conn2->close();
$conn->close();
?>
