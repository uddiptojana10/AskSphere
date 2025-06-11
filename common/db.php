<?php 
// Detect if running locally (XAMPP) or on InfinityFree
$is_local = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'));

if ($is_local) {
    // Local (XAMPP) database credentials
    $host = "localhost";
    $username = "root";
    $password = ""; // Default XAMPP password is empty; update if you've set a password
    $database = "asksphere";
} else {
    // InfinityFree database credentials
    $host = "";
    $username = "";
    $password = "";
    $database = "";
}

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Not connected with DB: " . $conn->connect_error);
}
?>