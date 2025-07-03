<?php
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

// Check if product parameter is set
if (!isset($_POST['product'])) {
    echo json_encode(["status" => "error", "message" => "No product specified"]);
    exit;
}

// Check if machine parameter is set
if (!isset($_POST['machine'])) {
    echo json_encode(["status" => "error", "message" => "No machine specified"]);
    exit;
}

$product = $_POST['product'];
$machine = $_POST['machine'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch the latest row
$sql_latest = "SELECT id FROM production_cycle ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql_latest);
if ($result === false || $result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "No rows found to update"]);
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$lastId = $row['id'];

// Update the product and machine fields of the latest row
$stmt = $conn->prepare("UPDATE production_cycle SET product = ?, machine = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("ssi", $product, $machine, $lastId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product and machine updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
}

$conn->close();
?>
