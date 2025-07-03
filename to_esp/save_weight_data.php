<?php
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gross = isset($_POST['gross_weight']) ? floatval($_POST['gross_weight']) : 0;
    $net = isset($_POST['net_weight']) ? floatval($_POST['net_weight']) : 0;
    $diff = isset($_POST['difference']) ? floatval($_POST['difference']) : 0;
    $product = isset($_POST['product']) ? $_POST['product'] : 'unknown';
    $timestamp = date("Y-m-d H:i:s");

    // Convert to kilograms
    $gross_kg = $gross / 1000.0;
    $net_kg = $net / 1000.0;
    $diff_kg = $diff / 1000.0;

    // Replace with your DB credentials
    $conn = new mysqli("localhost", "root", "", "sensory_data");

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO weight_data (gross_weight, net_weight, difference, product, timestamp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dddss", $gross_kg, $net_kg, $diff_kg, $product, $timestamp);

    if ($stmt->execute()) {
        echo "✅ Data inserted successfully!";
    } else {
        echo "❌ Insert error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "⚠️ Invalid request method.";
}
?>
