<?php
// api/get_messages.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// CORRECTED PATH: Changed from ../../ to ../
require_once '../config.php';

try {
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
        exit;
    }

    $userId = (int)$_GET['user_id'];
    $adminId = 0;

    $sql = "
        SELECT id, sender_id, message, timestamp
        FROM chat_messages
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY timestamp ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $userId, $adminId, $adminId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'sender' => ($row['sender_id'] == $adminId) ? 'admin' : 'user',
            'text' => htmlspecialchars($row['message']),
            'timestamp' => date("g:i A", strtotime($row['timestamp']))
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($messages);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
