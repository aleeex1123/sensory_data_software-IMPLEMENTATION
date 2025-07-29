<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

// Get machine name from URL query param
if (!isset($_GET['machine']) || $_GET['machine'] === "") {
    echo json_encode(["status" => "error", "message" => "No machine specified"]);
    exit;
}

$machine = $_GET['machine'];
$machine_safe = preg_replace('/[^a-zA-Z0-9]/', '', $machine); // remove any unsafe characters
$table = "production_cycle_" . strtolower($machine_safe);

$products = [];

// Check if the table exists
$check = $conn->query("SHOW TABLES LIKE '$table'");
if ($check && $check->num_rows > 0) {
    try {
        $res = $conn->query("SELECT DISTINCT product, mold_number FROM `$table` WHERE product IS NOT NULL AND mold_number IS NOT NULL");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $product = $r['product'];
                $mold = $r['mold_number'];

                // Avoid duplicates
                $exists = false;
                foreach ($products as $p) {
                    if ($p['mold'] === $mold) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists && $product !== "" && $mold !== "") {
                    $products[] = [
                        "name" => $product,
                        "mold" => $mold
                    ];
                }
            }
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Query failed"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Machine table not found"]);
    exit;
}

usort($products, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

echo json_encode($products);
$conn->close();
