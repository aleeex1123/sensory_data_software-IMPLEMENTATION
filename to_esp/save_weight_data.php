<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

$timestamp = date("Y-m-d H:i:s");

$machine = $conn->real_escape_string($_POST['machine']);
$product = $conn->real_escape_string($_POST['product']);
$gross = isset($_POST['gross_weight']) ? floatval($_POST['gross_weight']) : null;
$net   = isset($_POST['net_weight']) ? floatval($_POST['net_weight']) : null;
$diff  = isset($_POST['difference']) ? floatval($_POST['difference']) : null;

// Prepare the table name dynamically
$table_name = "production_cycle_" . str_replace(' ', '', strtolower($machine));

// Query to select mold_number from the dynamic table
$sql_mold = "SELECT mold_number FROM `$table_name` ORDER BY id DESC LIMIT 1";
$result_mold = $conn->query($sql_mold);

$mold_number = null;
if ($result_mold && $result_mold->num_rows > 0) {
    $row_mold = $result_mold->fetch_assoc();
    $mold_number = $row_mold['mold_number'];
}

if ($gross !== null && $net === null) {
    // INSERT gross only
    $sql = "INSERT INTO weight_data (machine, product, gross_weight, mold_number, `timestamp`)
            VALUES ('$machine', '$product', $gross, " . 
            ($mold_number !== null ? "'$mold_number'" : "NULL") . ", '$timestamp')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "gross_inserted", "id" => $conn->insert_id, "mold_number" => $mold_number]);
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
                       SET net_weight = $net, difference = $diff, mold_number = " . 
                       ($mold_number !== null ? "'$mold_number'" : "NULL") . ",
                       `timestamp` = '$timestamp'
                       WHERE id = $id";

            if ($conn->query($update) === TRUE) {
                echo json_encode(["status" => "net_updated", "id" => $id, "mold_number" => $mold_number]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }

        } else {
            // Latest row has already a net or no gross — insert new
            $insert = "INSERT INTO weight_data (machine, product, net_weight, difference, mold_number, `timestamp`)
                       VALUES ('$machine', '$product', $net, $diff, " . 
                       ($mold_number !== null ? "'$mold_number'" : "NULL") . ", '$timestamp')";

            if ($conn->query($insert) === TRUE) {
                echo json_encode(["status" => "net_inserted_new", "id" => $conn->insert_id, "mold_number" => $mold_number]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        }

    } else {
        // No previous row found, insert new
        $insert = "INSERT INTO weight_data (machine, product, net_weight, difference, mold_number, `timestamp`)
                   VALUES ('$machine', '$product', $net, $diff, " . 
                   ($mold_number !== null ? "'$mold_number'" : "NULL") . ", '$timestamp')";

        if ($conn->query($insert) === TRUE) {
            echo json_encode(["status" => "net_inserted_new", "id" => $conn->insert_id, "mold_number" => $mold_number]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
    }
}

$conn->close();
?>
