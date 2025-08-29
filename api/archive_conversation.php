<?php
// api/archive_conversation.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (!isset($data['user_id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
        exit;
    }

    $userIdToArchive = (int)$data['user_id'];
    $adminId = 0;

    // SQL to mark all messages in a conversation as archived by the admin
    $sql = "UPDATE chat_messages 
            SET is_archived_by_admin = TRUE 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (receiver_id = ? AND sender_id = ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $userIdToArchive, $adminId, $userIdToArchive, $adminId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Conversation archived successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to archive conversation.']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
