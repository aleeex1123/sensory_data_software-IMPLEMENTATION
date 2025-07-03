<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check request type
$type = isset($_GET['type']) ? $_GET['type'] : 'cycle'; 

if ($type === 'realtime') {
    $sql = "SELECT hose_01, hose_02, hose_03, hose_04, hose_05 FROM water_flow ORDER BY timestamp DESC LIMIT 10";
    $result = $conn->query($sql);

    $data = ["hose_01" => [], "hose_02" => [], "hose_03" => [], "hose_04" => [], "hose_05" => []];

    while ($row = $result->fetch_assoc()) {
        $data["hose_01"][] = $row["hose_01"];
        $data["hose_02"][] = $row["hose_02"];
        $data["hose_03"][] = $row["hose_03"];
        $data["hose_04"][] = $row["hose_04"];
        $data["hose_05"][] = $row["hose_05"];
    }
}

$conn->close();
echo json_encode($data);
?>

