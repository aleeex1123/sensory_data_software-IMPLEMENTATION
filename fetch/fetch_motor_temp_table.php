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
        $tempC1 = floatval($row['motor_tempC_01']);
        $tempC2 = floatval($row['motor_tempC_02']);

        $tempF1 = round($tempC1 * 9 / 5 + 32, 2);
        $tempF2 = round($tempC2 * 9 / 5 + 32, 2);

        $remarks1 = $tempC1 >= 40 ? "<span style='color: #f59c2f;'>Overheat</span>" : "<span style='color: #417630;'>Normal</span>";
        $remarks2 = $tempC2 >= 40 ? "<span style='color: #f59c2f;'>Overheat</span>" : "<span style='color: #417630;'>Normal</span>";

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$tempC1}</td>
            <td>{$tempF1}</td>
            <td>{$remarks1}</td>
            <td>{$tempC2}</td>
            <td>{$tempF2}</td>
            <td>{$remarks2}</td>
            <td>{$row['timestamp']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No data found.</td></tr>";
}

$conn->close();
?>
