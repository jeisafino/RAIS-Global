<?php
// api/get_conversations.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config.php'; // Corrected path

try {
    $adminId = 0;

    // MODIFIED SQL: This query now EXCLUDES users if their conversation thread
    // has been marked as archived by the admin.
    $sql = "
        SELECT u.id, u.firstName, u.lastName, u.profileImage
        FROM users u
        JOIN (
            SELECT DISTINCT sender_id AS user_id FROM chat_messages 
            WHERE receiver_id = ? AND is_archived_by_admin = FALSE
            UNION
            SELECT DISTINCT receiver_id AS user_id FROM chat_messages 
            WHERE sender_id = ? AND receiver_id != ? AND is_archived_by_admin = FALSE
        ) AS conversations ON u.id = conversations.user_id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $adminId, $adminId, $adminId);
    $stmt->execute();
    $result = $stmt->get_result();

    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[$row['id']] = [
            'name' => $row['firstName'] . ' ' . $row['lastName'],
            'avatar' => $row['profileImage'] ? '../' . $row['profileImage'] : 'https://placehold.co/40x40/D9D9D9/525252?text=' . strtoupper(substr($row['firstName'], 0, 1) . substr($row['lastName'], 0, 1)),
            'messages' => []
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($conversations);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
