<?php
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// Include DB config
require_once __DIR__ . '/db_config.php';

// === Input Parameters ===
$machine = isset($_GET['machine']) ? $_GET['machine'] : '';
$month   = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// === Section 1: Last Cycle Data for All Machines ===
$availableMachines = [
    "ARB 50", "SUM 260C", "SUM 650", "MIT 650D", "TOS 650A", "TOS 850A", "TOS 850B",
    "TOS 850C","CLF 750A", "CLF 750B", "CLF 750C", "CLF 950A", "CLF 950B", "MIT 1050B"
];

$lastCycleData = [];

foreach ($availableMachines as $m) {
    $table = "production_cycle_" . strtolower(str_replace(' ', '', $m));

    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {

        // Try to fetch the 2nd-to-last entry
        $res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1,1");

        // Fallback: latest row if only one exists
        if (!$res || $res->num_rows == 0) {
            $res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
        }

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();

            // Get latest gross/net weight
            $stmt = $conn->prepare("SELECT gross_weight, net_weight FROM weight_data WHERE machine = ? ORDER BY timestamp DESC LIMIT 1");
            $stmt->bind_param("s", $m);
            $stmt->execute();
            $weightResult = $stmt->get_result();

            $gross = "0.00";
            $net   = "0.00";
            if ($weightResult && $weightResult->num_rows > 0) {
                $weightRow = $weightResult->fetch_assoc();
                $gross = $weightRow['gross_weight'];
                $net   = $weightRow['net_weight'];
            }

            $lastCycleData[] = [
                "machine" => $m,
                "cycle_time" => $row['cycle_time'],
                "processing_time" => $row['processing_time'],
                "recycle_time" => $row['recycle_time'],
                "timestamp" => $row['timestamp'],
                "gross_weight" => $gross,
                "net_weight" => $net
            ];
        }
    }
}

// === Section 2: Daily Average Cycle Times for Selected Machine ===
$dailyCycleData = [];
if ($machine) {
    $table = "production_cycle_" . strtolower(str_replace(' ', '', $machine));

    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        $year = date('Y');
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        $sql = "SELECT DATE(timestamp) as day,
                       ROUND(AVG(cycle_time), 2) as avg_cycle_time,
                       ROUND(MIN(cycle_time), 2) as min_cycle_time,
                       ROUND(MAX(cycle_time), 2) as max_cycle_time
                FROM `$table`
                WHERE cycle_time > 0
                  AND timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
                  AND MONTH(timestamp) = $month AND YEAR(timestamp) = $year
                GROUP BY day
                ORDER BY day ASC";

        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $dailyCycleData[] = [
                    'date'    => $row['day'],
                    'average' => (float)$row['avg_cycle_time'],
                    'min'     => (float)$row['min_cycle_time'],
                    'max'     => (float)$row['max_cycle_time']
                ];
            }
        }
    }
}

// === Output JSON ===
echo json_encode([
    "status" => "success",
    "lastCycle" => $lastCycleData,
    "dailyCycle" => $dailyCycleData
]);

$conn->close();
?>
