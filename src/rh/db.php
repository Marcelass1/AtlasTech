<?php
$servername = "localhost";
$username = "rh_user"; 
$password = "rh_app_password";
$dbname = "rh_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
