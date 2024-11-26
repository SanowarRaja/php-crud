<?php
$servername = "localhost";
$username = "root";
$password = ""; // Adjust if needed
$database = "php_crud";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
