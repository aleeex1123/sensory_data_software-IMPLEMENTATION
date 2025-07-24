<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$scale_id = $_POST['scale_id'] ?? '';
$assigned_machine = $_POST['assigned_machine'] ?? '';

if ($scale_id !== '') {
    if ($assigned_machine === '') {
        // Unassign scale
        $sql = "UPDATE weighing_scale_controls SET assigned_machine = '', scale_status = 0 WHERE scale_id = ?";
    } else {
        // Assign new machine
        $sql = "UPDATE weighing_scale_controls SET assigned_machine = ?, scale_status = 1 WHERE scale_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($assigned_machine === '') {
        $stmt->bind_param("s", $scale_id);
    } else {
        $stmt->bind_param("ss", $assigned_machine, $scale_id);
    }

    if ($stmt->execute()) {
        header("Location: ../weights.php");
        exit;
    } else {
        echo "Error updating: " . $conn->error;
    }
}
$conn->close();
?>
