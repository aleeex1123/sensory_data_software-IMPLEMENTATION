<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sensory_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'cycle_status' parameter is received
if (isset($_GET['cycle_status'])) {
    $cycle_status = intval($_GET['cycle_status']);
    $cycle_time = isset($_GET['cycle_time']) ? intval($_GET['cycle_time']) : 0;
    $recycle_time = isset($_GET['recycle_time']) ? intval($_GET['recycle_time']) : 0;
    $machine = isset($_GET['machine']) ? $conn->real_escape_string($_GET['machine']) : '';

    // Get the last row from production_cycle
    $lastRowQuery = "SELECT * FROM production_cycle ORDER BY id DESC LIMIT 1";
    $lastRowResult = $conn->query($lastRowQuery);

    if ($lastRowResult && $lastRowResult->num_rows > 0) {
        $lastRow = $lastRowResult->fetch_assoc();
        $lastProduct = $lastRow['product'];
        $lastId = $lastRow['id'];

        // Rule 1: If last product is empty, reject any update
        if (empty($lastProduct)) {
            echo "Ignored: Last row product is empty.";
            exit;
        }

        if ($cycle_status == 1) {
            // Rule 2: Update the last row with cycle_status = 1
            $stmt = $conn->prepare("UPDATE production_cycle 
                                    SET cycle_status = 1, recycle_time = ?, machine = ?, timestamp = NOW()
                                    WHERE id = ?");
            $stmt->bind_param("isi", $recycle_time, $machine, $lastId);
            if ($stmt->execute()) {
                echo "Cycle START updated.";
            } else {
                echo "Error updating start: " . $stmt->error;
            }
            $stmt->close();

        } elseif ($cycle_status == 0) {

            // 👇 NEW FILTER: If cycle_time < 10, clear last row
            if ($cycle_time < 10) {
                $stmt_clear = $conn->prepare("UPDATE production_cycle 
                                              SET cycle_status = 0, cycle_time = 0, recycle_time = 0, machine = '', timestamp = NOW()
                                              WHERE id = ?");
                $stmt_clear->bind_param("i", $lastId);
                if ($stmt_clear->execute()) {
                    echo "Ignored: cycle_time < 10 sec — latest row cleared.";
                } else {
                    echo "Error clearing row: " . $stmt_clear->error;
                }
                $stmt_clear->close();
                exit;
            }

            // Rule 3: Close the current cycle and insert a new row with same product
            $stmt1 = $conn->prepare("UPDATE production_cycle 
                                     SET cycle_status = 0, cycle_time = ?, timestamp = NOW()
                                     WHERE id = ?");
            $stmt1->bind_param("ii", $cycle_time, $lastId);

            if ($stmt1->execute()) {
                $stmt1->close();

                // Insert a new row to continue cycle with same product
                $stmt2 = $conn->prepare("INSERT INTO production_cycle 
                                         (product, cycle_status, cycle_time, recycle_time, machine, timestamp) 
                                         VALUES (?, 0, 0, 0, '', NOW())");
                $stmt2->bind_param("s", $lastProduct);

                if ($stmt2->execute()) {
                    echo "Cycle ENDED and new row created.";
                } else {
                    echo "Error inserting new row: " . $stmt2->error;
                }
                $stmt2->close();

            } else {
                echo "Error updating end: " . $stmt1->error;
            }

        }

    } else {
        echo "No rows found in production_cycle.";
    }

} else {
    echo "No cycle_status received.";
}

$conn->close();
?>
