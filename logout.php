<?php
session_start();

// *** THIS IS THE NEW LOGIC ***
// Before logging out, update the user's status to 'Inactive'
if (isset($_SESSION['user_id'])) {
    require_once 'config.php'; // Connect to the database
    
    $userId = $_SESSION['user_id'];
     $update_sql = "UPDATE users SET status = 'Inactive' WHERE id = ?";
    if ($update_stmt = $conn->prepare($update_sql)) {
        $update_stmt->bind_param("i", $id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $conn->close();
}

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to the login page
header("location: index.php");
exit;
?>