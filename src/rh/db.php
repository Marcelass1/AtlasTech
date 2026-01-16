<?php
$servername = "localhost";
$username = "root";
$password = "StrongRootPassword123!"; // Matching the setup_main_server.sh password
$dbname = "rh_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
