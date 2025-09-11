<?php
session_start();
header('Content-Type: application/json');

require_once '../config.php';

// --- Helper function to send a JSON response ---
function send_json_response($status, $message, $data = []) {
    $response = ['status' => $status, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit;
}

// --- Helper function to get all admin accounts for refreshing the table ---
function get_all_admins($db_conn) {
    $admins = [];
    $sql = "SELECT id, firstName, lastName, email, role as type, status 
            FROM users 
            WHERE role LIKE '%Admin%' 
            ORDER BY id ASC";
    if ($result = $db_conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            // Always mark the logged-in account as Active
            if ($row['id'] == $_SESSION['admin_id']) {
                $row['status'] = 'Active';
            }
            $admins[] = $row;
        }
        $result->free();
    }
    return $admins;
}


// --- Main Logic ---
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'], $input['verificationPassword'])) {
    send_json_response('error', 'Invalid request.');
}

if (!isset($_SESSION['admin_id'])) {
    send_json_response('error', 'Authentication session has expired. Please log in again.');
}

$current_admin_id = $_SESSION['admin_id'];
$verification_password = $input['verificationPassword'];

// --- Password Verification based on Recent Activity ---
$session_timeout_minutes = 15;
$sql_verify = "SELECT password, status, role FROM users WHERE id = ?";


$stmt_verify = $conn->prepare($sql_verify);
$stmt_verify->bind_param("i", $current_admin_id);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result();

if ($admin = $result_verify->fetch_assoc()) {
    if ($admin['role'] !== 'Super Admin' && $admin['status'] !== 'Active') {
    send_json_response('error', 'Your account is inactive. Contact a Super Admin.');
    }
    if (!password_verify($verification_password, $admin['password'])) {
        send_json_response('error', 'Incorrect password. Authorization failed.');
    }
    // ✅ Success
} else {
    send_json_response('error', 'Authorization failed. User not found.');
}



// --- Perform the Requested Action ---
$action = $input['action'];
$action_data = $input['data'] ?? [];

switch ($action) {
    case 'add':
        $hashed_password = password_hash($action_data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (firstName, lastName, email, role, password, status) VALUES (?, ?, ?, ?, ?, 'Active')";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $action_data['firstName'], $action_data['lastName'], $action_data['email'], $action_data['type'], $hashed_password);
            if ($stmt->execute()) {
                send_json_response('success', 'Admin added successfully.', ['admins' => get_all_admins($conn)]);
            } else {
                send_json_response('error', 'Failed to add admin. The email may already be in use.');
            }
            $stmt->close();
        }
        break;

    case 'update':
        $sql = "UPDATE users SET firstName = ?, lastName = ?, email = ?, role = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $action_data['firstName'], $action_data['lastName'], $action_data['email'], $action_data['type'], $action_data['id']);
            if ($stmt->execute()) {
                send_json_response('success', 'Admin updated successfully.', ['admins' => get_all_admins($conn)]);
            } else {
                send_json_response('error', 'Failed to update admin.');
            }
            $stmt->close();
        }
        break;

    case 'delete':
        if ($action_data['id'] == $current_admin_id) {
            send_json_response('error', 'You cannot delete your own account.');
        }
        $sql = "DELETE FROM users WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $action_data['id']);
            if ($stmt->execute()) {
                send_json_response('success', 'Admin deleted successfully.', ['admins' => get_all_admins($conn)]);
            } else {
                send_json_response('error', 'Failed to delete admin.');
            }
            $stmt->close();
        }
        break;

    case 'reset_password':
        $hashed_password = password_hash($action_data['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $hashed_password, $action_data['id']);
            if ($stmt->execute()) {
                send_json_response('success', 'Password has been reset successfully.', ['admins' => get_all_admins($conn)]);
            } else {
                send_json_response('error', 'Failed to reset password.');
            }
            $stmt->close();
        }
        break;

    default:
        send_json_response('error', 'Invalid action specified.');
        break;
}

$conn->close();
?>