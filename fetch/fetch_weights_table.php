<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";  // change to your DB

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Get the parameters safely
$show = isset($_GET['show']) ? intval($_GET['show']) : 10;
$month = isset($_GET['month']) ? intval($_GET['month']) : 0; // 0 means all months

// Base query
$sql = "SELECT id, product, gross_weight, net_weight, difference, timestamp
        FROM weight_data";

// Filter by month if needed
if ($month > 0) {
    $sql .= " WHERE MONTH(timestamp) = ?";
}

// Add ordering and limit
$sql .= " ORDER BY timestamp DESC LIMIT ?";

// Prepare statement
$stmt = $conn->prepare($sql);
if ($month > 0) {
    $stmt->bind_param("ii", $month, $show);
} else {
    $stmt->bind_param("i", $show);
}

// Execute
$stmt->execute();
$result = $stmt->get_result();

// Build table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id'])."</td>";
        echo "<td>".htmlspecialchars($row['product'])."</td>";
        echo "<td>".htmlspecialchars($row['gross_weight'])."</td>";
        echo "<td>".htmlspecialchars($row['net_weight'])."</td>";
        echo "<td>".htmlspecialchars($row['difference'])."</td>";
        echo "<td>".htmlspecialchars($row['timestamp'])."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
