<?php
// api/partners_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Function for File Uploads ---
function handleFileUpload($file, $uploadDir = '../uploads/partner/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return the public-facing path, not the server path
        return 'uploads/partner/' . $fileName;
    }
    return null;
}

// --- Main Logic ---
switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM partners ORDER BY name ASC");
        $partners = [];
        while ($row = $result->fetch_assoc()) {
            $partners[] = $row;
        }
        $response = ['status' => 'success', 'data' => $partners];
        break;

    case 'add':
        $conn->begin_transaction();
        try {
            $name = trim($_POST['name'] ?? '');
            $link = trim($_POST['link'] ?? '');
            if (empty($name) || empty($link)) {
                throw new Exception('Partner Name and Link cannot be empty.');
            }

            $logoPath = handleFileUpload($_FILES['logo_file'] ?? null);
            $bgPath = handleFileUpload($_FILES['background_file'] ?? null);

            $stmt = $conn->prepare("INSERT INTO partners (name, website_link, logo_path, background_image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $link, $logoPath, $bgPath);
            $stmt->execute();
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Partner added successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Add transaction failed: ' . $e->getMessage();
        }
        break;

    case 'edit':
        $conn->begin_transaction();
        try {
            $id = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $link = trim($_POST['link'] ?? '');
            if (empty($name) || empty($link) || empty($id)) {
                throw new Exception('Partner Name, Link, or ID is missing.');
            }

            // Get existing paths to manage file deletion
            $stmt = $conn->prepare("SELECT logo_path, background_image_path FROM partners WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $existingPaths = $stmt->get_result()->fetch_assoc();

            $logoPath = $existingPaths['logo_path'];
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == UPLOAD_ERR_OK) {
                if ($logoPath && file_exists('../' . $logoPath)) { unlink('../' . $logoPath); }
                $logoPath = handleFileUpload($_FILES['logo_file']);
            }
            
            $bgPath = $existingPaths['background_image_path'];
            if (isset($_FILES['background_file']) && $_FILES['background_file']['error'] == UPLOAD_ERR_OK) {
                if ($bgPath && file_exists('../' . $bgPath)) { unlink('../' . $bgPath); }
                $bgPath = handleFileUpload($_FILES['background_file']);
            }

            $stmt = $conn->prepare("UPDATE partners SET name = ?, website_link = ?, logo_path = ?, background_image_path = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $link, $logoPath, $bgPath, $id);
            $stmt->execute();
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Partner updated successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Edit transaction failed: ' . $e->getMessage();
        }
        break;
        
    case 'delete':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            $response['message'] = 'Invalid ID specified.';
            break;
        }

        // First, get file paths to delete files from the server
        $stmt_select = $conn->prepare("SELECT logo_path, background_image_path FROM partners WHERE id = ?");
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $paths = $stmt_select->get_result()->fetch_assoc();
        
        if ($paths) {
            if ($paths['logo_path'] && file_exists('../' . $paths['logo_path'])) { unlink('../' . $paths['logo_path']); }
            if ($paths['background_image_path'] && file_exists('../' . $paths['background_image_path'])) { unlink('../' . $paths['background_image_path']); }
        }

        // Then, delete the record from the database
        $stmt_delete = $conn->prepare("DELETE FROM partners WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            $response = ['status' => 'success', 'message' => 'Partner deleted.'];
        } else {
            $response['message'] = 'Failed to delete partner from database.';
        }
        break;

    default:
        $response['message'] = 'Invalid action specified.';
        break;
}

echo json_encode($response);
?>