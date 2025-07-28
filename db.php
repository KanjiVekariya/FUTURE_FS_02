<?php
$host = "localhost";      // or 127.0.0.1
$user = "root";           // default XAMPP username
$pass = "";               // default XAMPP password (empty)
$dbname = "ecommerce";    // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
