<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';

if (empty($id)) {
    echo json_encode(['success' => false, 'error' => 'Missing mold ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM mold_thickness WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
