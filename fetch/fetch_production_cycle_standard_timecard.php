<?php
// Step 1: Connect to first DB (sensory_data)
require_once __DIR__ . '/db_config.php';

$product = isset($_GET['product']) ? $conn->real_escape_string($_GET['product']) : '';
$machine = isset($_GET['machine']) ? $conn->real_escape_string($_GET['machine']) : '';
$mold_number = "";

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $machine);

// Step 2: Get mold_number from production cycle table
$sql = "SELECT mold_number FROM `$table` WHERE product = '$product' LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $mold_number = $row['mold_number'];
    $conn->close();

    // Step 3: Connect to second DB (dms)
    require_once __DIR__ . '/db_config_2.php';

    $sql2 = "SELECT cycle_time_target FROM product_parameters WHERE mold_code = $mold_number LIMIT 1";
    $result2 = $conn2->query($sql2);

    if ($result2 && $row2 = $result2->fetch_assoc()) {
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
            "error" => "Mold not found in DMS"
        ]);
    }

    $conn2->close();
} else {
    echo json_encode([
        "cycle" => 0,
        "processing" => 0,
        "recycle" => 0,
        "error" => "Mold number not found for product: $product | mold code: $mold_number"
    ]);
}
?>
