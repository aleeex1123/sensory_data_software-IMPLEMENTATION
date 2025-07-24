<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "DB connection failed"]));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Missing scale_id"]);
    exit;
}

$scale_id = $_GET['id'];
$sql = "SELECT assigned_machine FROM weighing_scale_controls WHERE scale_id = '$scale_id' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["assigned_machine" => $row['assigned_machine']]);
} else {
    echo json_encode(["error" => "Scale ID not found"]);
}

$conn->close();
?>
