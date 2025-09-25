<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Get filters from request
$month   = isset($_GET['month']) ? intval($_GET['month']) : 0;
$product = isset($_GET['product']) ? trim($_GET['product']) : "";

// Build WHERE conditions
$where = [];
$params = [];
$types  = "";

// Month filter
if ($month > 0) {
    $where[] = "MONTH(`timestamp`) = ?";
    $params[] = $month;
    $types   .= "i";
}

// Product filter
if ($product !== "") {
    $where[] = "product = ?";
    $params[] = $product;
    $types   .= "s";
}

$whereSQL = "";
if (!empty($where)) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

// Query weights
$sql = "SELECT gross_weight, net_weight, difference 
        FROM weight_data 
        $whereSQL";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$gross = [];
$net   = [];
$diff  = [];

while ($row = $result->fetch_assoc()) {
    $gross[] = (float) $row['gross_weight'];
    $net[]   = (float) $row['net_weight'];
    $diff[]  = (float) $row['difference'];
}

$stmt->close();
$conn->close();

// Helper to build histogram bins
function buildHistogram(array $values, int $binCount = 10): array {
    if (empty($values)) {
        return [
            "labels" => [],
            "freq"   => []
        ];
    }

    $min = min($values);
    $max = max($values);

    if ($min == $max) {
        // single bin case
        return [
            "labels" => [round($min, 2)],
            "freq"   => [count($values)]
        ];
    }

    $binSize = ($max - $min) / $binCount;
    $bins   = [];
    $labels = [];

    for ($i = 0; $i < $binCount; $i++) {
        $bins[$i] = 0;
        $rangeMin = $min + $i * $binSize;
        $rangeMax = $rangeMin + $binSize;
        $labels[$i] = round($rangeMin, 2) . " - " . round($rangeMax, 2);
    }

    foreach ($values as $v) {
        $index = (int) floor(($v - $min) / $binSize);
        if ($index >= $binCount) $index = $binCount - 1;
        $bins[$index]++;
    }

    return [
        "labels" => $labels,
        "freq"   => $bins
    ];
}


// Build histograms
$grossHist = buildHistogram($gross);
$netHist   = buildHistogram($net);
$diffHist  = buildHistogram($diff);

$labels = $grossHist["labels"] ?: $netHist["labels"] ?: $diffHist["labels"];

echo json_encode([
    "labels"   => $labels,
    "grossFreq"=> $grossHist["freq"],
    "netFreq"  => $netHist["freq"],
    "diffFreq" => $diffHist["freq"]
]);
