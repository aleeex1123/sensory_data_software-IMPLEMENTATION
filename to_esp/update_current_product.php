<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Validate inputs
if (!isset($_POST['product']) || !isset($_POST['machine']) || !isset($_POST['thickness']) || !isset($_POST['mold_num'])) {
    echo json_encode(["status" => "error", "message" => "Missing one or more required parameters"]);
    exit;
}

$product   = $_POST['product'];
$machine   = $_POST['machine'];
$thickness = $_POST['thickness'];
$mold_num  = $_POST['mold_num']; // this is the actual mold code

// Build the correct table name
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($machine));

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Table $table_name does not exist"]);
    exit;
}

// Get latest row with cycle_status
$sql_latest = "SELECT id, cycle_status FROM `$table_name` ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql_latest);
if ($result === false || $result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "No rows found to update"]);
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$lastId = $row['id'];
$cycle_status = $row['cycle_status'];

// If cycle_status == 1, prevent update
if ($cycle_status == 1) {
    echo json_encode(["status" => "error", "message" => "Cannot update mold. Cycle is closed (status = 1)"]);
    $conn->close();
    exit;
}

// Otherwise (status 0 or 2), update the row
$stmt = $conn->prepare("UPDATE `$table_name` SET product = ?, mold_number = ? WHERE id = ?");
if ($stmt) {
    $product_with_thickness = $product . " | " . $thickness;
    $stmt->bind_param("ssi", $product_with_thickness, $mold_num, $lastId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product and mold number updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
}

$conn->close();
?>
