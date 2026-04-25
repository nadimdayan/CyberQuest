<?php
// config/db.php
// Database connection for CyberQuest

$host     = "localhost";
$db_name  = "cyberquest";
$username = "root";
$password = "";   // XAMPP default has no password

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>
