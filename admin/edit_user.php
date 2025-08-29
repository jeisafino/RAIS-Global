<?php
require_once '../config.php';
$data = json_decode(file_get_contents('php://input'), true);

$response = ['status' => 'error', 'message' => 'Invalid data.'];

// MODIFIED: Removed 'status' from the check, as it's no longer sent.
if (isset($data['userId'], $data['firstName'], $data['lastName'], $data['email'])) {
    
    // MODIFIED: The "status = ?" part has been removed from the SQL query.
    $sql = "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // MODIFIED: The type string is now "sssi" (instead of "ssssi") 
        // and the status variable is removed.
        $stmt->bind_param("sssi", $data['firstName'], $data['lastName'], $data['email'], $data['userId']);
        
        if ($stmt->execute()) { 
            $response['status'] = 'success'; 
        } else {
            $response['message'] = 'Database execute failed.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Database prepare failed.';
    }
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>