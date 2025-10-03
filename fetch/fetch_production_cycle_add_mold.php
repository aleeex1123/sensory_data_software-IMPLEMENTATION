<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

// DB connection
require_once __DIR__ . '/db_config.php';

// Read incoming JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$moldName   = trim($input['moldName'] ?? '');
$moldNumber = trim($input['moldNumber'] ?? '');
$thickness  = trim($input['thickness'] ?? '');

// Validate required fields
if ($moldName === '' || $moldNumber === '' || $thickness === '') {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit;
}

try {
    // Check for duplicate mold_number
    $checkStmt = $conn->prepare("SELECT id FROM mold_thickness WHERE mold_number = ?");
    $checkStmt->bind_param("s", $moldNumber);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Mold number already exists"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Insert new mold
    $stmt = $conn->prepare("INSERT INTO mold_thickness (mold_name, mold_number, thickness) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $moldName, $moldNumber, $thickness);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
