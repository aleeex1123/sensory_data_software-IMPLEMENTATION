<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$scale_id = isset($_POST['scale_id']) ? trim($_POST['scale_id']) : '';
$action = $_POST['action'] ?? '';

if ($scale_id === '' || ($action !== 'start' && $action !== 'stop')) {
    die("Invalid request.");
}

if ($action === 'start') {
    $product = trim($_POST['product'] ?? '');
    if ($product === '') {
        die("Product name is required.");
    }

    $stmt = $conn->prepare("UPDATE weighing_scale_controls SET scale_status = 1, current_product = ? WHERE scale_id = ?");
    $stmt->bind_param("ss", $product, $scale_id);
    $stmt->execute();
    $stmt->close();

} elseif ($action === 'stop') {
    $stmt = $conn->prepare("UPDATE weighing_scale_controls SET scale_status = 0, current_product = '' WHERE scale_id = ?");
    $stmt->bind_param("s", $scale_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
