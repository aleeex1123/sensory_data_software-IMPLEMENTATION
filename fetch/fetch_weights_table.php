<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once 'db_config.php';

// Get parameters
$show = isset($_GET['show']) ? $_GET['show'] : '10';  // Keep as string for now
$month = isset($_GET['month']) ? intval($_GET['month']) : 0;

// Base query
$sql = "SELECT id, machine, product, mold_number, gross_weight, net_weight, difference, timestamp FROM weight_data";
$conditions = [];
$params = [];
$paramTypes = "";

// Add month filter
if ($month > 0) {
    $conditions[] = "MONTH(timestamp) = ?";
    $params[] = $month;
    $paramTypes .= "i";
}

// Apply WHERE if there are any conditions
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Add ORDER BY
$sql .= " ORDER BY id DESC";

// Add LIMIT if show is not "all"
if ($show !== "all") {
    $sql .= " LIMIT ?";
    $params[] = intval($show);
    $paramTypes .= "i";
}

// Prepare and bind
$stmt = $conn->prepare($sql);
if ($paramTypes !== "") {
    $stmt->bind_param($paramTypes, ...$params);
}

// Execute
$stmt->execute();
$result = $stmt->get_result();

// Build table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['machine']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mold_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gross_weight']) . "</td>";
        echo "<td>" . htmlspecialchars($row['net_weight']) . "</td>";
        echo "<td>" . htmlspecialchars($row['difference']) . "</td>";
        echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
