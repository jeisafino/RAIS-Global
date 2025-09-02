<?php
// api/hero_media_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

$physical_upload_dir = '../uploads/hero/';
$db_path_prefix = 'uploads/hero/';

if (!is_dir($physical_upload_dir)) {
    mkdir($physical_upload_dir, 0755, true);
}

// --- Handle GET request ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // This part is fine, no changes needed.
    $result = $conn->query("SELECT id, media_name, uploader, DATE_FORMAT(upload_date, '%Y-%m-%d') as date, file_path, is_active FROM hero_media ORDER BY upload_date DESC");
    $media_list = [];
    while ($row = $result->fetch_assoc()) {
        $media_list[] = $row;
    }
    $response = ['status' => 'success', 'data' => $media_list];
    echo json_encode($response);
    exit;
}

// --- Handle POST request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action: 'add' (was 'create' in your JS, now 'add') or 'update'
    // I noticed your JS uses 'add' but the PHP comment mentioned 'create'. Using 'add' as it matches the JS.
    if ($action === 'add' || $action === 'update') { // Renamed from 'update' to 'add'
        $media_name = $_POST['mediaName'] ?? '';
        $uploader = $_POST['uploaderName'] ?? '';
        $media_id = $_POST['mediaId'] ?? null;
        
        // --- FIX #1: Validate required fields at the start ---
        if (empty($media_name) || empty($uploader)) {
            $response = ['status' => 'error', 'message' => 'Media Name and Uploader fields cannot be empty.'];
            echo json_encode($response);
            exit;
        }

        $file_path_for_db = '';

        // Check for an uploaded file
        if (isset($_FILES['mediaFile']) && $_FILES['mediaFile']['error'] === UPLOAD_ERR_OK) {
            // ... (your file deletion logic for updates is fine) ...

            $file_extension = pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('hero_', true) . '.' . $file_extension;
            $physical_file_path = $physical_upload_dir . $unique_filename;

            // Attempt to move the file
            if (move_uploaded_file($_FILES['mediaFile']['tmp_name'], $physical_file_path)) {
                // If successful, set the path for the database
                $file_path_for_db = $db_path_prefix . $unique_filename;
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to move uploaded file. Check folder permissions or file size limits.'];
                echo json_encode($response);
                exit;
            }
        }

        // --- FIX #2: Critical logic change for the 'add' action ---
        if ($action === 'add') {
            // If we are ADDING a new record, the file MUST exist.
            if (empty($file_path_for_db)) {
                $response = ['status' => 'error', 'message' => 'A media file is required to add a new item.'];
                echo json_encode($response);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO hero_media (media_name, uploader, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $media_name, $uploader, $file_path_for_db);
        } else { // This is an 'update' action
            if (!empty($file_path_for_db)) { // A new file was uploaded for the update
                $stmt = $conn->prepare("UPDATE hero_media SET media_name = ?, uploader = ?, file_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $media_name, $uploader, $file_path_for_db, $media_id);
            } else { // No new file, just updating text fields
                $stmt = $conn->prepare("UPDATE hero_media SET media_name = ?, uploader = ? WHERE id = ?");
                $stmt->bind_param("ssi", $media_name, $uploader, $media_id);
            }
        }

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Media ' . ($action === 'add' ? 'added' : 'updated') . ' successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Database error: ' . $stmt->error];
        }

    // --- Other actions (delete, set_active) ---
    } elseif ($action === 'delete') {
        // Your delete logic is fine
        $media_id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("SELECT file_path FROM hero_media WHERE id = ?");
        $stmt->bind_param("i", $media_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (file_exists('../' . $row['file_path'])) {
                unlink('../' . $row['file_path']);
            }
        }
        $stmt = $conn->prepare("DELETE FROM hero_media WHERE id = ?");
        $stmt->bind_param("i", $media_id);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Media deleted successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Database error.'];
        }
    } elseif ($action === 'set_active') {
        // Your set_active logic is fine
        $media_id = $_POST['id'] ?? 0;
        $conn->begin_transaction();
        try {
            $conn->query("UPDATE hero_media SET is_active = 0");
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

$conn->close();
echo json_encode($response);
?>