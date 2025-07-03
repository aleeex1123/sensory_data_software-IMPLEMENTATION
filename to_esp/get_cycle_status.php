<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "sensory_data"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT cycle_status FROM production_cycle ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["cycle_status" => (int)$row['cycle_status']]);
} else {
    echo json_encode(["cycle_status" => 0]);
}

$conn->close();
?>
