<?php
// -----------------------------------------------------------------
// File: db_connect.php
// Handles the connection to your database and defines the base URL.
// Note: This is based on your uploaded config.php file.
// -----------------------------------------------------------------

// --- Define Base URL for web links ---
// This creates a full, absolute URL to your project folder (e.g., http://localhost/raisbe)
define('BASE_URL', sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    // This part gets the subdirectory if your project is not in the web root
    rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
));

// --- Database Configuration ---
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "raisdb";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_errno) {
    die("Database connection failed: " . $conn->connect_error);
}

?>
