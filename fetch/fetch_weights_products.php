<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once 'db_config.php';


$sql = "SELECT DISTINCT product FROM weight_data ORDER BY product ASC";
$result = $conn->query($sql);

$options = "<option value='' selected>All</option>";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $product = htmlspecialchars($row['product']);
        $options .= "<option value=\"$product\">$product</option>";
    }
}
echo $options;
$conn->close();
