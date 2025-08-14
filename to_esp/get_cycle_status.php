<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Check if 'machine' parameter is present
if (!isset($_GET['machine']) || empty($_GET['machine'])) {
    echo json_encode(["error" => "Missing machine parameter"]);
    exit;
}

// Use machine name directly
$machine = $_GET['machine'];
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($_GET['machine']));

// Check if the table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(["error" => "Table $table_name does not exist"]);
    exit;
}

$sql = "SELECT cycle_status FROM `$table_name` ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["cycle_status" => (int)$row['cycle_status']]);
} else {
    echo json_encode(["cycle_status" => 0]);
}

$conn->close();
?>
