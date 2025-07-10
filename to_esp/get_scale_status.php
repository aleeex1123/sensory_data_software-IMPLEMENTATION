<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "sensory_data"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = [
    "scale_id" => "",
    "scale_status" => "",
    "current_product" => ""
];

// Check if 'id' is passed via GET
if (isset($_GET['id'])) {
    $scale_id = $_GET['id'];

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT scale_id, scale_status, current_product FROM weighing_scale_controls WHERE scale_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $scale_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response["scale_id"] = $row["scale_id"];
        $response["scale_status"] = $row["scale_status"];
        $response["current_product"] = $row["current_product"];
    }

    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
