<?php
$servername = "localhost";
$username = "root";
$password = "";

// Connect to first DB (sensory_data)
$dbname1 = "sensory_data";
$conn1 = new mysqli($servername, $username, $password, $dbname1);
if ($conn1->connect_error) {
    die(json_encode(["error" => "DB1 connection failed"]));
}

$product = isset($_GET['product']) ? $conn1->real_escape_string($_GET['product']) : '';
$machine = isset($_GET['machine']) ? $conn1->real_escape_string($_GET['machine']) : '';
$mold_number = "";

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $machine);

// Step 1: Get mold_number from production cycle table
$sql = "SELECT mold_number FROM `$table` WHERE product = '$product' LIMIT 1";
$result = $conn1->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $mold_number = $row['mold_number'];
    $conn1->close();

    // Step 2: Connect to second DB (dms) to get cycle_time_target
    $dbname2 = "dms";
    $conn2 = new mysqli($servername, $username, $password, $dbname2);
    if ($conn2->connect_error) {
        die(json_encode(["error" => "DB2 connection failed"]));
    }

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
        echo json_encode(["cycle" => 0, "processing" => 0, "recycle" => 0, "error" => "Mold not found in DMS"]);
    }

    $conn2->close();
} else {
    echo json_encode(["cycle" => 0, "processing" => 0, "recycle" => 0, "error" => "Mold number not found for product: $product | mold code: $mold_number"]);
}
?>
