<?php
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$machine = $_GET['machine'] ?? '';
$durationText = 'No Machine Selected';

if ($machine) {
    $table = "production_cycle_" . strtolower(str_replace(' ', '', $machine));
    $latest_sql = "SELECT cycle_status, timestamp FROM `$table` ORDER BY timestamp DESC LIMIT 1";
    $latest_result = $conn->query($latest_sql);

    if ($latest_result && $latest_result->num_rows > 0) {
        $latest = $latest_result->fetch_assoc();
        $cycle_status = (int) $latest['cycle_status'];
        $latestTimestampStr = $latest['timestamp'];

        $now = new DateTime();

        if ($cycle_status === 0 || $cycle_status === 1) {
            // ACTIVE
            $active_sql = "SELECT timestamp FROM `$table` WHERE cycle_time != 0 AND recycle_time = 0 AND timestamp <= '$latestTimestampStr' ORDER BY timestamp DESC LIMIT 1";
            $active_result = $conn->query($active_sql);

            if ($active_result && $active_result->num_rows > 0) {
                $active_row = $active_result->fetch_assoc();
                $active_start = new DateTime($active_row['timestamp']);
                $diff = $now->diff($active_start);
                $suffix = " active";
            } else {
                echo "Active time unknown";
                exit;
            }
        } else {
            // INACTIVE
            $inactive_start = new DateTime($latestTimestampStr);
            $diff = $now->diff($inactive_start);
            $suffix = " inactive";
        }

        // Handle "just active/inactive"
        if ($diff->days === 0 && $diff->h === 0 && $diff->i === 0) {
            echo "Just{$suffix}";
        } else {
            $dayPart = $diff->days > 0 ? "{$diff->days} days " : "";
            $hourPart = $diff->h > 0 ? "{$diff->h} hours " : "";
            $minuteLabel = $diff->i === 1 ? "minute" : "minutes";
            $minutePart = $diff->i > 0 ? "{$diff->i} {$minuteLabel}" : "";

            echo trim("{$dayPart}{$hourPart}{$minutePart}{$suffix}");
        }
    } else {
        echo "Machine data unavailable";
    }
}
?>
