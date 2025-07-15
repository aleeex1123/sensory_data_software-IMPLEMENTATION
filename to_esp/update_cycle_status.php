<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sensory_data";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['cycle_status']) && isset($_GET['machine'])) {
    $cycle_status = intval($_GET['cycle_status']);
    $cycle_time = isset($_GET['cycle_time']) ? intval($_GET['cycle_time']) : 0;
    $recycle_time = isset($_GET['recycle_time']) ? intval($_GET['recycle_time']) : 0;
    $machine = strtolower(preg_replace('/\s+/', '', $_GET['machine']));
    $targetTable = "production_cycle_" . $conn->real_escape_string($machine);

    // Fetch the last row from main table
    $lastRowQuery = "SELECT * FROM production_cycle ORDER BY id DESC LIMIT 1";
    $lastRowResult = $conn->query($lastRowQuery);

    if ($lastRowResult && $lastRowResult->num_rows > 0) {
        $lastRow = $lastRowResult->fetch_assoc();
        $lastProduct = $lastRow['product'];
        $lastId = $lastRow['id'];

        if (empty($lastProduct)) {
            echo "Ignored: Last row product is empty.";
            exit;
        }

        if ($cycle_status == 1) {
            // Start of cycle
            $stmt = $conn->prepare("UPDATE production_cycle 
                                    SET cycle_status = 1, recycle_time = ?, timestamp = NOW()
                                    WHERE id = ?");
            $stmt->bind_param("ii", $recycle_time, $lastId);
            $stmt->execute();
            $stmt->close();

            $conn->query("UPDATE `$targetTable` 
                          SET cycle_status = 1, recycle_time = $recycle_time, timestamp = NOW() 
                          ORDER BY id DESC LIMIT 1");

            echo "Cycle START updated.";

        } elseif ($cycle_status == 0) {
            // If too short, clear row
            if ($cycle_time < 10) {
                $stmt_clear = $conn->prepare("UPDATE production_cycle 
                                              SET cycle_status = 0, cycle_time = 0, recycle_time = 0, timestamp = NOW()
                                              WHERE id = ?");
                $stmt_clear->bind_param("i", $lastId);
                $stmt_clear->execute();
                $stmt_clear->close();

                $conn->query("UPDATE `$targetTable` 
                              SET cycle_status = 0, cycle_time = 0, recycle_time = 0, timestamp = NOW() 
                              ORDER BY id DESC LIMIT 1");

                echo "Ignored: cycle_time < 10 sec — latest row cleared.";
                exit;
            }

            // End of cycle
            $stmt1 = $conn->prepare("UPDATE production_cycle 
                                     SET cycle_status = 0, cycle_time = ?, timestamp = NOW()
                                     WHERE id = ?");
            $stmt1->bind_param("ii", $cycle_time, $lastId);
            $stmt1->execute();
            $stmt1->close();

            $conn->query("UPDATE `$targetTable` 
                          SET cycle_status = 0, cycle_time = $cycle_time, timestamp = NOW() 
                          ORDER BY id DESC LIMIT 1");

            // Insert new row with same product
            $stmt2 = $conn->prepare("INSERT INTO production_cycle 
                                     (product, cycle_status, cycle_time, recycle_time, timestamp) 
                                     VALUES (?, 0, 0, 0, NOW())");
            $stmt2->bind_param("s", $lastProduct);
            $stmt2->execute();
            $stmt2->close();

            $conn->query("INSERT INTO `$targetTable` 
                          (product, cycle_status, cycle_time, recycle_time, timestamp) 
                          VALUES ('$lastProduct', 0, 0, 0, NOW())");

            echo "Cycle ENDED and new row created.";
        }

    } else {
        echo "No rows found in production_cycle.";
    }

} else {
    echo "Missing required parameters.";
}

$conn->close();
