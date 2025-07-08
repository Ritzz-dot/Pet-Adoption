<?php
$host = "localhost";
$user = "root";
$pass = "root"; // default MAMP password
$db = "user_db"; // must match your actual database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
