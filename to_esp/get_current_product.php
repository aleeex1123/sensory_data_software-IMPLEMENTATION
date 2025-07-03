<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "sensory_data"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product FROM production_cycle ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["product" => $row['product']]);
} else {
    echo json_encode(["product" => ""]);
}

$conn->close();
?>
