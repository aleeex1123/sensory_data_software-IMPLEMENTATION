<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$show = isset($_GET['show']) ? intval($_GET['show']) : 10;
$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$machine = isset($_GET['machine']) ? $conn->real_escape_string($_GET['machine']) : '';

$sql = "SELECT id, motor_tempC_01, motor_tempF_01, motor_tempC_02, motor_tempF_02, timestamp 
        FROM motor_temperatures 
        WHERE 1";

if ($month > 0) {
    $sql .= " AND MONTH(timestamp) = $month";
}

if (!empty($machine)) {
    $sql .= " AND machine = '$machine'";
}

$sql .= " ORDER BY timestamp DESC LIMIT $show";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['motor_tempC_01']}</td>";
        echo "<td>{$row['motor_tempF_01']}</td>";
        echo "<td>{$row['motor_tempC_02']}</td>";
        echo "<td>{$row['motor_tempF_02']}</td>";
        echo "<td>{$row['timestamp']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No data found.</td></tr>";
}

$conn->close();
?>
