<?php
require_once __DIR__ . '/db_config.php';

$machine = isset($_GET['machine']) ? $_GET['machine'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$machine_safe = strtolower(preg_replace('/\s+/', '_', $machine));
$table = "production_cycle_" . $machine_safe;

$limit = 10;  
$offset = ($page - 1) * $limit;

// ✅ Query: skip newest, then paginate by 10
$sql = "SELECT cycle_time, tempC_01, tempC_02, timestamp
        FROM $table
        ORDER BY id DESC
        LIMIT " . ($limit + 1) . " OFFSET $offset";

$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // reverse for chronological order & drop the latest entry
    $rows = array_reverse($rows);
    array_pop($rows);

    foreach ($rows as $r) {
        $data[] = [
            'time' => date("m/d H:i", strtotime($r['timestamp'])),
            'cycle_time' => (int)$r['cycle_time'],
            'temp1' => (float)$r['tempC_01'],
            'temp2' => (float)$r['tempC_02']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
