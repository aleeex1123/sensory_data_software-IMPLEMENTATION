<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "dms";

// $servername = "srv1518.hstgr.io";
// $username   = "u158529957_spmc_dmonitor";
// $password   = "RSzGvru!0S[m";
// $database   = "u158529957_spmc_dmonitor";

$conn2 = new mysqli($servername, $username, $password, $database);

// If connection fails, stop the script
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Optional: set charset for proper encoding
$conn2->set_charset("utf8mb4");
?>