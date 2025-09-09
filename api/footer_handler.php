<?php
// api/footer_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Main Logic ---
switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM footer_items ORDER BY type ASC, display_order ASC, label ASC");
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $response = ['status' => 'success', 'data' => $items];
        break;

    case 'add':
        $label = trim($_POST['label'] ?? '');
        $value = trim($_POST['value'] ?? ''); // This will be the URL
        $icon_class = trim($_POST['icon_class'] ?? 'bi bi-link-45deg');

        if (empty($label) || empty($value)) {
            $response['message'] = 'Label and Link cannot be empty.';
            break;
        }

        $stmt = $conn->prepare("INSERT INTO footer_items (label, value, type, icon_class) VALUES (?, ?, 'social', ?)");
        $stmt->bind_param("sss", $label, $value, $icon_class);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Social link added successfully.'];
        } else {
            $response['message'] = 'Failed to add link: ' . $conn->error;
        }
        break;

    case 'edit':
        $id = $_POST['id'] ?? 0;
        $label = trim($_POST['label'] ?? '');
        $value = trim($_POST['value'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? '');
        
        if (empty($id) || empty($value)) {
            $response['message'] = 'ID or Value cannot be empty.';
            break;
        }

        // Fetch the item to check its type
        $stmt_check = $conn->prepare("SELECT type FROM footer_items WHERE id = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $item_type = $stmt_check->get_result()->fetch_assoc()['type'];

        if ($item_type === 'static') {
            // Static items can only have their value (description) updated
            $stmt = $conn->prepare("UPDATE footer_items SET value = ? WHERE id = ?");
            $stmt->bind_param("si", $value, $id);
        } else { // It's a 'social' item
            if (empty($label)) {
                 $response['message'] = 'Label cannot be empty for social links.';
                 break;
            }
            $stmt = $conn->prepare("UPDATE footer_items SET label = ?, value = ?, icon_class = ? WHERE id = ?");
            $stmt->bind_param("sssi", $label, $value, $icon_class, $id);
        }
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Item updated successfully.'];
        } else {
            $response['message'] = 'Failed to update item: ' . $conn->error;
        }
        break;
        
    case 'delete':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            $response['message'] = 'No ID specified for deletion.';
            break;
        }

        // Prevent deletion of static items for safety
        $stmt = $conn->prepare("DELETE FROM footer_items WHERE id = ? AND type = 'social'");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ['status' => 'success', 'message' => 'Social link deleted.'];
            } else {
                $response['message'] = 'Item not found or it is a static item that cannot be deleted.';
            }
        } else {
            $response['message'] = 'Failed to delete link.';
        }
        break;

    default:
        $response['message'] = 'Invalid action specified.';
        break;
}

echo json_encode($response);
?>