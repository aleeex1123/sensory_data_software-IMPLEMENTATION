<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// Get data from GET request
$temp1 = isset($_GET['temp1']) ? floatval($_GET['temp1']) : null;
$temp2 = isset($_GET['temp2']) ? floatval($_GET['temp2']) : null;

// Calculate tempF_01 and tempF_02 if temp1 and temp2 are provided
$tempF_01 = ($temp1 !== null) ? ($temp1 * 9 / 5) + 32 : null;
$tempF_02 = ($temp2 !== null) ? ($temp2 * 9 / 5) + 32 : null;

// Use machine name directly
$machine = $_GET['machine'];
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($_GET['machine']));

// Check if the table exists
$tableCheck = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($tableCheck->num_rows == 0) {
    echo json_encode(["error" => "Table $table_name does not exist"]);
    exit;
}

$currentTime = date('Y-m-d H:i:s');

// ✅ Create temporary variables for binding
$tempC1 = $temp1 ?? null;
$tempF1 = $tempF_01 ?? null;
$tempC2 = $temp2 ?? null;
$tempF2 = $tempF_02 ?? null;

// ✅ Update the latest row
$query = "UPDATE `$table_name` 
          SET tempC_01 = IFNULL(?, tempC_01), 
              tempF_01 = IFNULL(?, tempF_01), 
              tempC_02 = IFNULL(?, tempC_02), 
              tempF_02 = IFNULL(?, tempF_02), 
              timestamp = NOW()
          ORDER BY timestamp DESC 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("dddd", $tempC1, $tempF1, $tempC2, $tempF2);

if ($stmt->execute()) {
    echo "Latest row updated in production_cycle";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
