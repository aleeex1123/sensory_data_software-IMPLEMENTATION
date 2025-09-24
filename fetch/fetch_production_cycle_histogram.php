<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

$machine = $_GET['machine'] ?? '';
if (!$machine) {
    http_response_code(400);
    echo json_encode(["error" => "Missing machine parameter"]);
    exit;
}

$table = "production_cycle_" . $conn->real_escape_string($machine);

$sql = "SELECT cycle_time, tempC_01, tempC_02 FROM $table";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$cycleTimes = [];
$temp1 = [];
$temp2 = [];

while ($row = $result->fetch_assoc()) {
    $cycleTimes[] = (int)$row['cycle_time'];
    $temp1[] = (float)$row['tempC_01'];
    $temp2[] = (float)$row['tempC_02'];
}

$labels = [];
$counts = [];
$avgTemp1 = [];
$avgTemp2 = [];

if (count($cycleTimes) > 0) {
    $binCount = 15;
    $minVal = min($cycleTimes);
    $maxVal = max($cycleTimes);

    if ($minVal === $maxVal) {
        $minVal = max(0, $minVal - 1);
        $maxVal = $maxVal + 1;
    }

    $binSize = ceil(($maxVal - $minVal) / $binCount);

    $counts = array_fill(0, $binCount, 0);
    $sumTemp1 = array_fill(0, $binCount, 0);
    $sumTemp2 = array_fill(0, $binCount, 0);

    for ($i = 0; $i < $binCount; $i++) {
        $start = $minVal + $i * $binSize;
        $end   = $start + $binSize - 1;
        $labels[] = "$start-$end";
    }

    foreach ($cycleTimes as $idx => $ct) {
        $index = floor(($ct - $minVal) / $binSize);
        if ($index >= $binCount) $index = $binCount - 1;

        $counts[$index]++;
        $sumTemp1[$index] += $temp1[$idx];
        $sumTemp2[$index] += $temp2[$idx];
    }

    for ($i = 0; $i < $binCount; $i++) {
        $avgTemp1[$i] = $counts[$i] > 0 ? $sumTemp1[$i] / $counts[$i] : null;
        $avgTemp2[$i] = $counts[$i] > 0 ? $sumTemp2[$i] / $counts[$i] : null;
    }
}

echo json_encode([
    "labels" => $labels,
    "cycleTimeData" => $counts,
    "temp1Data" => $avgTemp1,
    "temp2Data" => $avgTemp2
]);
