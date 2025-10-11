<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/db_config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // important for catching errors

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(["success" => false, "error" => "Invalid input format"]);
        exit;
    }

    $id          = trim($input['id'] ?? '');
    $moldName    = trim($input['moldName'] ?? '');
    $moldNumber  = trim($input['moldNumber'] ?? '');
    $thickness   = trim($input['thickness'] ?? '');

    if ($id === '' || $moldName === '' || $moldNumber === '' || $thickness === '') {
        echo json_encode(["success" => false, "error" => "All fields are required"]);
        exit;
    }

    // Check duplicates (excluding the current record)
    $checkStmt = $conn->prepare("SELECT id FROM mold_thickness WHERE mold_number = ? AND id != ?");
    $checkStmt->bind_param("si", $moldNumber, $id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Mold number already exists"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Update
    $stmt = $conn->prepare("UPDATE mold_thickness SET mold_name = ?, mold_number = ?, thickness = ? WHERE id = ?");
    $stmt->bind_param("ssii", $moldName, $moldNumber, $thickness, $id);
    $stmt->execute();

    echo json_encode(["success" => true]);
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
