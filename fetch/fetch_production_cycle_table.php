<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Get parameters
$show = isset($_GET['show']) ? intval($_GET['show']) : 10;
$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$machine = isset($_GET['machine']) ? $_GET['machine'] : 'CLF 750A';

// Sanitize and convert machine name to table name
$machine_safe = preg_replace('/[^a-zA-Z0-9_]/', '_', $machine);
$tableName = "production_cycle_" . $machine_safe;

// Validate table existence (optional but recommended)
$checkTable = $conn->query("SHOW TABLES LIKE '$tableName'");
if ($checkTable->num_rows == 0) {
    echo "<tr><td colspan='8'>Table for selected machine does not exist.</td></tr>";
    exit;
}

// Build query
$sql = "SELECT id, cycle_time, recycle_time, tempC_01, tempC_02, product, timestamp FROM `$tableName`";
$where = [];
$params = [];
$types = "";

// Month filter
if ($month > 0) {
    $where[] = "MONTH(timestamp) = ?";
    $params[] = $month;
    $types .= "i";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY timestamp DESC LIMIT ?";
$params[] = $show;
$types .= "i";

// Prepare and execute
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<tr><td colspan='8'>Error: " . $conn->error . "</td></tr>";
    exit;
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Display data
if ($result->num_rows > 1) {
    while ($row = $result->fetch_assoc()) {
        if (floatval($row['cycle_time']) == 0 && floatval($row['recycle_time']) == 0) {
            continue;
        }
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id'])."</td>";
        echo "<td>".htmlspecialchars($row['cycle_time'])."</td>";
        echo "<td>".htmlspecialchars($row['recycle_time'])."</td>";
        echo "<td>".htmlspecialchars($row['tempC_01'])."</td>";
        echo "<td>".htmlspecialchars($row['tempC_02'])."</td>";
        echo "<td>".htmlspecialchars($row['product'])."</td>";
        echo "<td>".htmlspecialchars($row['timestamp'])."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
