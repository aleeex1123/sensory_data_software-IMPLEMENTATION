<?php
$servername = "localhost";
$username = "root";
$password = "";

// Connect to both databases
$conn1 = new mysqli($servername, $username, $password, "dms");            // for product_parameters
$conn2 = new mysqli($servername, $username, $password, "sensory_data");   // for production_cycle_<machine>

if ($conn1->connect_error || $conn2->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$product = isset($_GET['product']) ? $conn2->real_escape_string($_GET['product']) : '';
$machine = isset($_GET['machine']) ? $conn2->real_escape_string($_GET['machine']) : '';
$limit   = isset($_GET['show']) ? intval($_GET['show']) : 20;
$month   = isset($_GET['month']) ? intval($_GET['month']) : date('n');

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($machine));

// Step 1: Get mold number from production cycle table (sensory_data)
$moldQuery = "SELECT mold_number FROM `$table` WHERE product LIKE '%" . $conn2->real_escape_string($product) . "%' LIMIT 1";
$moldResult = $conn2->query($moldQuery);
$mold_number = "";

if ($moldResult && $row = $moldResult->fetch_assoc()) {
    $mold_number = $row['mold_number'];
}

// Step 2: Get cycle_time_target from product_parameters (dms)
$standard = 0;
if ($mold_number !== "") {
    $paramQuery = "SELECT cycle_time_target FROM product_parameters WHERE mold_code = '$mold_number'";
    $paramResult = $conn1->query($paramQuery);
    if ($paramResult && $param = $paramResult->fetch_assoc()) {
        $standard = floatval($param['cycle_time_target']);
    }
}

// Step 3: Fetch past cycle data from sensory_data
$entries = [];
$average = 0;
$min = 0;
$max = 0;

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

$dataResult = $conn2->query($dataQuery);

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

    // Calculate cycle
    $avgCycleTime = round(array_sum($cycleTimes) / count($cycleTimes), 2);
    $maxCycleTime = round(max($cycleTimes), 2);

    // Calculate processing
    $avgProcessingTime = round(array_sum($processingTimes) / count($processingTimes), 2);
    $maxProcessingTime = round(max($processingTimes), 2);

    // Calculate recycle
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
    // Defaults when no data found
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

$conn1->close();
$conn2->close();
?>
