<?php
// api/services_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php'; // This file should create the $conn variable

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Functions ---
function handleFileUpload($file, $uploadDir = '../uploads/service/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/service/' . $fileName;
    }
    return null;
}

function generateServiceFile($serviceId, $serviceName, $conn) {
    $templatePath = 'service_template.php';
    if (!file_exists($templatePath)) {
        return [false, 'Service template file not found.'];
    }
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $serviceName)));
    $newFileName = $slug . '.php';
    $newFilePath = '../services/' . $newFileName;
    $publicPath = 'services/' . $newFileName;
    $templateContent = file_get_contents($templatePath);
    $newContent = str_replace('SERVICE_ID_PLACEHOLDER', $serviceId, $templateContent);
    if (file_put_contents($newFilePath, $newContent)) {
        $stmt = $conn->prepare("UPDATE services SET file_path = ? WHERE id = ?");
        $stmt->bind_param("si", $publicPath, $serviceId);
        $stmt->execute();
        return [true, $publicPath];
    }
    return [false, 'Failed to create service file.'];
}

// --- Main Logic ---
switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM services ORDER BY name ASC");
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $serviceId = $row['id'];
            $sectionsResult = $conn->query("SELECT * FROM service_sections WHERE service_id = $serviceId ORDER BY display_order ASC");
            $sections = [];
            while ($section = $sectionsResult->fetch_assoc()) {
                $sections[] = $section;
            }
            $row['sections'] = $sections;
            $services[] = $row;
        }
        $response = ['status' => 'success', 'data' => $services];
        break;

    case 'add':
case 'edit':
    $serviceName = trim($_POST['name'] ?? '');
    $serviceDesc = $_POST['description'] ?? '';
    $serviceId = ($action === 'edit') ? ($_POST['id'] ?? 0) : 0;

    $sectionTitles = $_POST['section_title'] ?? [];
    $sectionDescriptions = $_POST['section_description'] ?? [];
    $existingMediaPaths = $_POST['existing_media_path'] ?? [];
    
    if (empty($serviceName)) {
        $response['message'] = 'Service Name cannot be empty.';
        break;
    }

    $conn->begin_transaction();

    try {
        $heroPath = null;
        if ($action === 'edit') {
            $stmt = $conn->prepare("SELECT description, hero_media_path FROM services WHERE id = ?");
            $stmt->bind_param("i", $serviceId);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentService = $result->fetch_assoc();

            if (empty($serviceDesc) && isset($currentService['description'])) {
                $serviceDesc = $currentService['description'];
            }

            if (isset($_FILES['hero_media']) && $_FILES['hero_media']['error'] == UPLOAD_ERR_OK) {
                $heroPath = handleFileUpload($_FILES['hero_media']);
            } else {
                $heroPath = $currentService['hero_media_path'] ?? null;
            }
        } else { // 'add' action
            if (isset($_FILES['hero_media']) && $_FILES['hero_media']['error'] == UPLOAD_ERR_OK) {
                $heroPath = handleFileUpload($_FILES['hero_media']);
            }
        }

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO services (name, description, hero_media_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $serviceName, $serviceDesc, $heroPath);
            $stmt->execute();
            $serviceId = $conn->insert_id;
        } else { // 'edit' action
            $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, hero_media_path = ? WHERE id = ?");
            $stmt->bind_param("sssi", $serviceName, $serviceDesc, $heroPath, $serviceId);
            $stmt->execute();
            
            $stmt = $conn->prepare("DELETE FROM service_sections WHERE service_id = ?");
            $stmt->bind_param("i", $serviceId);
            $stmt->execute();
        }

        $stmt = $conn->prepare("INSERT INTO service_sections (service_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($sectionTitles as $index => $title) {
            $content = $sectionDescriptions[$index] ?? '';
            $sectionPath = null;
            
            // Handle array of uploaded section files
            if (isset($_FILES['section_media']) && $_FILES['section_media']['error'][$index] == UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['section_media']['name'][$index],
                    'type' => $_FILES['section_media']['type'][$index],
                    'tmp_name' => $_FILES['section_media']['tmp_name'][$index],
                    'error' => $_FILES['section_media']['error'][$index],
                    'size' => $_FILES['section_media']['size'][$index]
                ];
                $sectionPath = handleFileUpload($file);
            } else if ($action === 'edit') {
                 // If no new file, keep the old one
                $sectionPath = $existingMediaPaths[$index] ?? null;
            }

            $stmt->bind_param("isssi", $serviceId, $title, $content, $sectionPath, $index);
            $stmt->execute();
        }
        
        list($fileGenerated, $message) = generateServiceFile($serviceId, $serviceName, $conn);
        if (!$fileGenerated) throw new Exception($message);
        
        $conn->commit();
        $response = ['status' => 'success', 'message' => "Service " . ($action === 'add' ? "added" : "updated") . " successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Transaction failed: ' . $e->getMessage();
    }
    break;

    case 'delete':
        $serviceId = $_POST['id'] ?? 0;
        $fileSlug = $_POST['file_slug'] ?? '';

        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $serviceId);
        if ($stmt->execute()) {
            if (!empty($fileSlug)) {
                $filePath = '../services/' . $fileSlug . '.php';
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $response = ['status' => 'success', 'message' => 'Service deleted.'];
        } else {
            $response['message'] = 'Failed to delete service.';
        }
        break;

    default:
        $response['message'] = 'Invalid action specified.';
        break;
}

echo json_encode($response);
?>