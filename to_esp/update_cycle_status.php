<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

if (isset($_GET['cycle_status']) && isset($_GET['machine'])) {
    $cycle_status = intval($_GET['cycle_status']);
    $processing_time = isset($_GET['cycle_time']) ? intval($_GET['cycle_time']) : 0;
    $recycle_time = isset($_GET['recycle_time']) ? intval($_GET['recycle_time']) : 0;
    $machine = strtolower(preg_replace('/\s+/', '', $_GET['machine']));
    $targetTable = "production_cycle_" . $conn->real_escape_string($machine);

    // Fetch the last row from the machine-specific table
    $lastRowQuery = "SELECT * FROM `$targetTable` ORDER BY id DESC LIMIT 1";
    $lastRowResult = $conn->query($lastRowQuery);

    if ($lastRowResult && $lastRowResult->num_rows > 0) {
        $lastRow = $lastRowResult->fetch_assoc();
        $lastProduct = $lastRow['product'];
        $lastCycleStatus = intval($lastRow['cycle_status']);
        $lastId = $lastRow['id'];

        if (empty($lastProduct)) {
            echo "Ignored: Last row product is empty.";
            exit;
        }

        // Check if the new cycle_status is the same as current one
        if ($lastCycleStatus == $cycle_status) {
            echo "Ignored: Same cycle_status as current — no update needed.";
            exit;
        }

        if ($cycle_status == 1) {
            // Start of cycle
            if ($lastCycleStatus == 2) {
                $recycle_time = 0; // Force recycle_time to 0 if coming from alert state
            }

            $stmt = $conn->prepare("UPDATE `$targetTable` 
                                    SET cycle_status = 1, recycle_time = ?, timestamp = NOW()
                                    WHERE id = ?");
            $stmt->bind_param("ii", $recycle_time, $lastId);

            $stmt->execute();
            $stmt->close();

            echo "Cycle START updated.";

        } elseif ($cycle_status == 0) {
            // If too short, clear row
            if ($processing_time < 10) {
                $stmt_clear = $conn->prepare("UPDATE `$targetTable` 
                                              SET cycle_status = 0, cycle_time = 0, processing_time = 0, recycle_time = 0, timestamp = NOW()
                                              WHERE id = ?");
                $stmt_clear->bind_param("i", $lastId);
                $stmt_clear->execute();
                $stmt_clear->close();

                echo "Ignored: cycle_time < 10 sec — latest row cleared.";
                exit;
            }

            // End of cycle
            $lastRecycleTimeQuery = "SELECT recycle_time FROM `$targetTable` ORDER BY id DESC LIMIT 1";
            $lastRecycleTimeResult = $conn->query($lastRecycleTimeQuery);

            if ($lastRecycleTimeResult && $lastRecycleTimeResult->num_rows > 0) {
                $row = $lastRecycleTimeResult->fetch_assoc();
                $recycle_time = intval($row['recycle_time']);  // Ensure integer
                $cycle_time = $processing_time + $recycle_time;
            } else {
                $recycle_time = 0;
                $cycle_time = $processing_time;
            }

            $stmt1 = $conn->prepare("UPDATE `$targetTable` 
                                     SET cycle_status = 0, processing_time = ?, cycle_time = ?, timestamp = NOW()
                                     WHERE id = ?");
            $stmt1->bind_param("iii", $processing_time, $cycle_time, $lastId);
            $stmt1->execute();
            $stmt1->close();

            // Insert new row with same product
            $stmt2 = $conn->prepare("INSERT INTO `$targetTable` 
                                     (product, mold_number, cycle_status, cycle_time, processing_time, recycle_time, timestamp) 
                                     VALUES (?, ?, 0, 0, 0, 0, NOW())");
            $stmt2->bind_param("ss", $lastProduct, $lastRow['mold_number']);
            $stmt2->execute();
            $stmt2->close();

            echo "Cycle ENDED and new row created.";

        } elseif ($cycle_status == 2) {
            // Alert/idle state
            $stmt_alert = $conn->prepare("UPDATE `$targetTable` 
                                          SET cycle_status = 2, timestamp = NOW()
                                          WHERE id = ?");
            $stmt_alert->bind_param("i", $lastId);
            $stmt_alert->execute();
            $stmt_alert->close();

            echo "Cycle ALERT (status 2) recorded.";
        }

    } else {
        echo "No rows found in $targetTable table.";
    }

} else {
    echo "Missing required parameters.";
}

$conn->close();
