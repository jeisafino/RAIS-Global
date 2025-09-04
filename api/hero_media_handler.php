<?php
// api/hero_media_handler.php

header('Content-Type: application/json');
// Path goes up one level to the root folder to find db_connect.php
require_once '../db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Define paths relative to the root folder
$physical_upload_dir = '../uploads/hero/'; // Physical path for moving the file
$db_path_prefix = 'uploads/hero/';      // Clean path to store in the database

// Ensure the physical upload directory exists
if (!is_dir($physical_upload_dir)) {
    mkdir($physical_upload_dir, 0755, true);
}

// --- Handle GET request to fetch all media ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT id, media_name, uploader, DATE_FORMAT(upload_date, '%Y-%m-%d') as date, file_path, is_active FROM hero_media ORDER BY upload_date DESC");
    $media_list = [];
    while ($row = $result->fetch_assoc()) {
        $media_list[] = $row;
    }
    $response = ['status' => 'success', 'data' => $media_list];
}

// --- Handle POST request for Create, Update, Delete, and Set Active ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Create a new media item or Update an existing one ---
    if ($action === 'add' || $action === 'update') {
        $media_name = $_POST['mediaName'] ?? '';
        $uploader = $_POST['uploaderName'] ?? '';
        $media_id = $_POST['mediaId'] ?? null;
        
        $file_path_for_db = ''; // This will hold the clean path for the database

        if (isset($_FILES['mediaFile']) && $_FILES['mediaFile']['error'] === 0) {
            // If updating, delete the old file first
            if ($action === 'update' && $media_id) {
                $stmt = $conn->prepare("SELECT file_path FROM hero_media WHERE id = ?");
                $stmt->bind_param("i", $media_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    // Prepend `../` to the DB path to find the physical file
                    if (!empty($row['file_path']) && file_exists('../' . $row['file_path'])) {
                        unlink('../' . $row['file_path']);
                    }
                }
            }

            // Create a unique filename and move the uploaded file
            $file_extension = pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('hero_', true) . '.' . $file_extension;
            
            $file_path_for_db = $db_path_prefix . $unique_filename;
            $physical_file_path = $physical_upload_dir . $unique_filename;

            if (!move_uploaded_file($_FILES['mediaFile']['tmp_name'], $physical_file_path)) {
                 $response = ['status' => 'error', 'message' => 'Failed to move uploaded file. Check folder permissions.'];
                 echo json_encode($response);
                 exit;
            }
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO hero_media (media_name, uploader, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $media_name, $uploader, $file_path_for_db);
        } else { // Update
             if (!empty($file_path_for_db)) { // If a new file was uploaded
                $stmt = $conn->prepare("UPDATE hero_media SET media_name = ?, uploader = ?, file_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $media_name, $uploader, $file_path_for_db, $media_id);
            } else { // If only text was updated
                $stmt = $conn->prepare("UPDATE hero_media SET media_name = ?, uploader = ? WHERE id = ?");
                $stmt->bind_param("ssi", $media_name, $uploader, $media_id);
            }
        }

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Media ' . ($action === 'add' ? 'added' : 'updated') . ' successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }

    // --- Delete a media item ---
    } elseif ($action === 'delete') {
        $media_id = $_POST['id'] ?? 0;
        
        if ($media_id > 0) {
            // First, get the file path to delete the physical file
            $stmt = $conn->prepare("SELECT file_path FROM hero_media WHERE id = ?");
            $stmt->bind_param("i", $media_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (!empty($row['file_path']) && file_exists('../' . $row['file_path'])) {
                    unlink('../' . $row['file_path']);
                }
            }

            // Then, delete the record from the database
            $stmt = $conn->prepare("DELETE FROM hero_media WHERE id = ?");
            $stmt->bind_param("i", $media_id);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Media deleted successfully.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Database error.'];
            }
        }
        
    // --- Set a media item as active ---
    } elseif ($action === 'set_active') {
        $media_id = $_POST['id'] ?? 0;

        if ($media_id > 0) {
            // Use a transaction to ensure data integrity
            $conn->begin_transaction();
            try {
                // First, deactivate all other items
                $conn->query("UPDATE hero_media SET is_active = 0");
                // Then, activate the selected one
                $stmt = $conn->prepare("UPDATE hero_media SET is_active = 1 WHERE id = ?");
                $stmt->bind_param("i", $media_id);
                $stmt->execute();
                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Active hero media updated.'];
            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => 'Transaction failed.'];
            }
        }
    }
}

$conn->close();
echo json_encode($response);
?>