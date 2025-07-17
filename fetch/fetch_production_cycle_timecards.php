<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "DB connection failed"]));
}

$show = isset($_GET['show']) ? intval($_GET['show']) : 10;
$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$product = isset($_GET['product']) ? $_GET['product'] : '';
$machine = isset($_GET['machine']) ? $_GET['machine'] : 'CLF 750A';

$table = "production_cycle_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $machine);

// Check if table exists
$check = $conn->query("SHOW TABLES LIKE '$table'");
if ($check->num_rows == 0) {
    echo json_encode(["error" => "Table not found"]);
    exit;
}

$sql = "SELECT cycle_time, processing_time, recycle_time FROM `$table`";
$conditions = [];
$params = [];
$types = "";

// Filters
if ($month > 0) {
    $conditions[] = "MONTH(timestamp) = ?";
    $params[] = $month;
    $types .= "i";
}
if (!empty($product)) {
    $conditions[] = "product = ?";
    $params[] = $product;
    $types .= "s";
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY timestamp DESC LIMIT ?";
$params[] = $show + 1; // Fetch one extra row
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

if (count($rows) === 0) {
    echo json_encode(["error" => "No data"]);
    exit;
}

$cycle_values = array_column($rows, 'cycle_time');
$processing_values = array_column($rows, 'processing_time');
$recycle_values = array_column($rows, 'recycle_time');

$response = [
    "average" => [
        "cycle" => round(array_sum($cycle_values) / (count($cycle_values)-1), 2),
        "processing" => round(array_sum($processing_values) / (count($processing_values)-1), 2),
        "recycle" => round(array_sum($recycle_values) / (count($recycle_values)-1), 2)
    ],
    "minimum" => [
        "cycle" => min(array_filter($cycle_values, fn($v) => $v > 0)) ?: 0,
        "processing" => min(array_filter($processing_values, fn($v) => $v > 0)) ?: 0,
        "recycle" => min(array_filter($recycle_values, fn($v) => $v > 0)) ?: 0
    ],
    "maximum" => [
        "cycle" => max($cycle_values),
        "processing" => max($processing_values),
        "recycle" => max($recycle_values)
    ]
];

echo json_encode($response);
?>
