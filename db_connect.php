<?php
$servername = "localhost"; // Or your database host
$username = "root";
$password = "password";
$dbname = "main_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully (Procedural)";

// Perform database operations here

// Close connection
mysqli_close($conn);
?>