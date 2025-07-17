<?php
$conn = new mysqli("localhost","root","","sensory_data");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$tables = [];
$rs = $conn->query("SHOW TABLES LIKE 'production_cycle_%'");
while ($r = $rs->fetch_array()) $tables[] = $r[0];

$data = [];

foreach ($tables as $table) {
    // Primary query: attempt to fetch 2nd-to-last row
    $q = "SELECT * FROM `$table` ORDER BY id DESC LIMIT 1,1";
    $res = $conn->query($q);
    
    // If not found, fallback to most recent row
    if (!$res || $res->num_rows == 0) {
        $res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
    }
    
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $machineName = strtoupper(str_replace("production_cycle_", "", $table));
        $data[] = [
            "machine" => $machineName,
            "cycle_time" => $row["cycle_time"],
            "processing_time" => $row["processing_time"],
            "recycle_time" => $row["recycle_time"],
            "timestamp" => $row["timestamp"]
        ];
    }
}

echo json_encode($data);
?>
