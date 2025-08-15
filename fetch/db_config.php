<?php
// $servername = "localhost";
// $username   = "root";
// $password   = "";
// $database   = "sensory_data";

$servername = "srv1518.hstgr.io";
$username   = "u158529957_spmc_sensory";
$password   = "e3Y0@1#U^[N";
$database   = "u158529957_spmc_sensory";

$conn = new mysqli($servername, $username, $password, $database);

// If connection fails, stop the script
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset for proper encoding
$conn->set_charset("utf8mb4");
?>