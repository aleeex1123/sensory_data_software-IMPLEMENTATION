<?php
header('Content-Type: application/json');

// DB connection
$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$availableMachines = [
    "ARB 50", "SUM 260C", "SUM 650", "MIT 650D", "TOS 650A", "TOS 850A", "TOS 850B",
    "TOS 850C","CLF 750A", "CLF 750B", "CLF 750C", "CLF 950A", "CLF 950B", "MIT 1050B"
];

$data = [];

foreach ($availableMachines as $machine) {
    $table = "production_cycle_" . strtolower(str_replace(' ', '', $machine));

    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {

        // Try to fetch the 2nd-to-last entry
        $res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1,1");

        // Fallback: use latest row if only one exists
        if (!$res || $res->num_rows == 0) {
            $res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
        }

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();

            // Query for latest gross/net weight for this machine
            $stmt = $conn->prepare("SELECT gross_weight, net_weight FROM weight_data WHERE machine = ? ORDER BY timestamp DESC LIMIT 1");
            $stmt->bind_param("s", $machine);
            $stmt->execute();
            $weightResult = $stmt->get_result();

            $gross = "0.00";
            $net = "0.00";
            if ($weightResult && $weightResult->num_rows > 0) {
                $weightRow = $weightResult->fetch_assoc();
                $gross = $weightRow['gross_weight'];
                $net = $weightRow['net_weight'];
            }

            $data[] = [
                "machine" => $machine,
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

echo json_encode($data);
$conn->close();
?>