<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

require_once __DIR__ . '/db_config.php';

if (!isset($_GET['machine']) || empty($_GET['machine'])) {
    echo json_encode(["error" => "Missing machine parameter"]);
    exit;
}

$machine = urldecode($_GET['machine']);
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($machine));

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(["error" => "Table $table_name does not exist"]);
    exit;
}

// Fetch latest product
$sql = "SELECT product FROM `$table_name` ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$data = ["product" => ""];
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data["product"] = $row["product"];
}

$conn->close();

// Save to cache file
$cacheFile = __DIR__ . "/cache/product_" . preg_replace('/\s+/', '_', strtolower($machine)) . ".json";
file_put_contents($cacheFile, json_encode($data));

echo json_encode(["status" => "cache updated", "data" => $data]);
