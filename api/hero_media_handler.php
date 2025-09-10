<?php
// api/hero_media_handler.php
session_start();

header('Content-Type: application/json');
require_once '../db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

$physical_upload_dir = '../uploads/hero/';
$db_path_prefix = 'uploads/hero/';

if (!is_dir($physical_upload_dir)) {
    mkdir($physical_upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT id, media_name, uploader, DATE_FORMAT(upload_date, '%Y-%m-%d') as date, file_path FROM hero_media ORDER BY upload_date DESC");
    $media_list = $result->fetch_all(MYSQLI_ASSOC);
    $response = ['status' => 'success', 'data' => $media_list];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $media_name = $_POST['mediaName'] ?? '';
        $uploader = $_SESSION['admin_name'] ?? 'Admin';
        $media_id = $_POST['mediaId'] ?? null;
        
        $final_file_path = '';

        if ($action === 'update') {
            $stmt_select = $conn->prepare("SELECT file_path FROM hero_media WHERE id = ?");
            $stmt_select->bind_param("i", $media_id);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            if ($row = $result->fetch_assoc()) {
                $final_file_path = $row['file_path'];
            }
            $stmt_select->close();
        }

        if (isset($_FILES['mediaFile']) && $_FILES['mediaFile']['error'] === UPLOAD_ERR_OK) {
            if ($action === 'update' && !empty($final_file_path) && file_exists('../' . $final_file_path)) {
                unlink('../' . $final_file_path);
            }

            $file_extension = pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('hero_', true) . '.' . $file_extension;
            $physical_file_path = $physical_upload_dir . $unique_filename;

            if (move_uploaded_file($_FILES['mediaFile']['tmp_name'], $physical_file_path)) {
                $final_file_path = $db_path_prefix . $unique_filename;
            } else {
                 $response['message'] = 'Failed to move uploaded file. Check folder permissions.';
                 echo json_encode($response);
                 exit;
            }
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO hero_media (media_name, uploader, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $media_name, $uploader, $final_file_path);
        } else { // Update
            $stmt = $conn->prepare("UPDATE hero_media SET media_name = ?, uploader = ?, file_path = ? WHERE id = ?");
            $stmt->bind_param("sssi", $media_name, $uploader, $final_file_path, $media_id);
        }

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Media ' . ($action === 'add' ? 'added' : 'updated') . ' successfully.'];
        } else {
            $response['message'] = 'Database error: ' . $stmt->error;
        }
        $stmt->close();

        } elseif ($action === 'delete') {
        // 1. Get the ID of the media item to delete from the frontend.
        $media_id = $_POST['id'] ?? 0;
        
        if ($media_id > 0) {
            // 2. SAFETY FIRST: Find the file path from the database BEFORE deleting the record.
            $stmt = $conn->prepare("SELECT file_path FROM hero_media WHERE id = ?");
            $stmt->bind_param("i", $media_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // 3. Construct the physical path and delete the actual file from the server.
                $physical_file_path = '../' . $row['file_path'];
                if (!empty($row['file_path']) && file_exists($physical_file_path)) {
                    unlink($physical_file_path); // This deletes the file.
                }
            }
            $stmt->close();
        
            // 4. NOW, it is safe to delete the record from the database.
            $stmt = $conn->prepare("DELETE FROM hero_media WHERE id = ?");
            $stmt->bind_param("i", $media_id);
            
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Media deleted successfully.'];
            } else {
                $response['message'] = 'Database error on delete.';
            }
            $stmt->close();
        }
    }
}

$conn->close();
echo json_encode($response);