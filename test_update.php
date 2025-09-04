<?php
echo "<h1>Database Update Test</h1>";

// --- Step 1: Include your database connection file ---
// Make sure this path is correct.
require_once 'db_connect.php';

if ($conn->connect_error) {
    die("<p style='color:red;'><strong>Connection Failed:</strong> " . $conn->connect_error . "</p>");
}
echo "<p style='color:green;'><strong>Step 1: Database Connection Successful.</strong></p>";

// --- Step 2: Prepare the UPDATE command ---
echo "<p><strong>Step 2: Attempting to run an UPDATE command...</strong></p>";

// This is the same command your save script tries to run.
// We are trying to update the 'media_path' for the main 'about' section entry (id=1).
$sql = "UPDATE about_main SET media_path = ? WHERE id = 1";
$test_path = "test_value_".time(); // A unique value to see if it saves.

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("<p style='color:red;'><strong>Prepare Statement Failed:</strong> " . htmlspecialchars($conn->error) . "</p>");
}
echo "<p style='color:green;'>Prepare statement was successful.</p>";

$stmt->bind_param("s", $test_path);
if ($stmt->execute()) {
    echo "<p style='color:green;'><strong>Step 3: UPDATE Command Executed Successfully!</strong></p>";
    echo "<p>The 'media_path' in your 'about_main' table should now be set to: <strong>" . htmlspecialchars($test_path) . "</strong></p>";
} else {
    echo "<p style='color:red;'><strong>Step 3: UPDATE Command FAILED.</strong></p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($stmt->error) . "</p>";
    echo "<p>This is the root of the problem. The server is preventing the script from updating the database record.</p>";
}

$stmt->close();
$conn->close();

?>