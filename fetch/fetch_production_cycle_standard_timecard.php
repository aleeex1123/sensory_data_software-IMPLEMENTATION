<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'db_config.php'; // connects to sensory_data

$product = isset($_GET['product']) ? $conn->real_escape_string($_GET['product']) : '';
$machine = isset($_GET['machine']) ? $conn->real_escape_string($_GET['machine']) : '';

if (empty($product) || empty($machine)) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $machine);

// Step 1: Get mold_number from production_cycle_<machine>
$sql = "SELECT mold_number FROM `$table` WHERE product = '$product' LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode([
        "cycle" => 0,
        "processing" => 0,
        "recycle" => 0,
        "error" => "Mold number not found for product: $product"
    ]);
    exit;
}

$row = $result->fetch_assoc();
$mold_number = $conn->real_escape_string($row['mold_number']);

// Step 2: Get cycle_time_target from product_parameters
$sql2 = "SELECT cycle_time_target FROM product_parameters WHERE mold_code = '$mold_number' LIMIT 1";
$result2 = $conn->query($sql2);

if ($result2 && $result2->num_rows > 0) {
    $row2 = $result2->fetch_assoc();
    $cycle = floatval($row2['cycle_time_target']);
    $processing = round($cycle / 2, 2);
    $recycle = round($cycle / 2, 2);

    echo json_encode([
        "cycle" => $cycle,
        "processing" => $processing,
        "recycle" => $recycle
    ]);
} else {
    echo json_encode([
        "cycle" => 0,
        "processing" => 0,
        "recycle" => 0,
        "error" => "Mold code not found in product_parameters: $mold_number"
    ]);
}

$conn->close();
?>
