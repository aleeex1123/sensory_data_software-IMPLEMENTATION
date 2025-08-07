<?php
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensory_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

if (!isset($_POST['machine']) || !isset($_POST['product'])) {
    echo json_encode(["error" => "Missing machine or product"]);
    exit;
}

$machine = $conn->real_escape_string($_POST['machine']);
$product = $conn->real_escape_string($_POST['product']);
$gross = isset($_POST['gross_weight']) ? floatval($_POST['gross_weight']) : null;
$net = isset($_POST['net_weight']) ? floatval($_POST['net_weight']) : null;
$diff = isset($_POST['difference']) ? floatval($_POST['difference']) : null;

if ($gross !== null && $net === null) {
    // INSERT gross only
    $sql = "INSERT INTO weight_data (machine, product, gross_weight, timestamp)
            VALUES ('$machine', '$product', $gross, NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "gross_inserted", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => $conn->error]);
    }

} elseif ($net !== null && $gross === null) {
    // Get the latest row (highest ID) for this machine and product
    $sql = "SELECT id, gross_weight, net_weight FROM weight_data 
            WHERE machine = '$machine' AND product = '$product' 
            ORDER BY id DESC LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (floatval($row['gross_weight']) != 0 && floatval($row['net_weight']) == 0) {
            $id = $row['id'];

            $update = "UPDATE weight_data 
                       SET net_weight = $net, difference = $diff 
                       WHERE id = $id";

            if ($conn->query($update) === TRUE) {
                echo json_encode(["status" => "net_updated", "id" => $id]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }

        } else {
            // Latest row has already a net or no gross — insert new
            $insert = "INSERT INTO weight_data (machine, product, net_weight, difference, timestamp)
                       VALUES ('$machine', '$product', $net, 0, NOW())";

            if ($conn->query($insert) === TRUE) {
                echo json_encode(["status" => "net_inserted_new", "id" => $conn->insert_id]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        }

    } else {
        // No previous row found, insert new
        $insert = "INSERT INTO weight_data (machine, product, net_weight, difference, timestamp)
                   VALUES ('$machine', '$product', $net, 0, NOW())";

        if ($conn->query($insert) === TRUE) {
            echo json_encode(["status" => "net_inserted_new", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
    }

}

$conn->close();
?>
