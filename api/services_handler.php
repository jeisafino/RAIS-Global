<?php
// api/services_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Functions ---
function handleFileUpload($file, $uploadDir = '../uploads/service/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/service/' . $fileName;
    }
    return null;
}

function generateServiceFile($serviceId, $serviceName, $conn) {
    $templatePath = 'service_template.php';
    if (!file_exists($templatePath)) return [false, 'Service template file not found.'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $serviceName)));
    $newFileName = $slug . '.php';
    if (!is_dir('../services/')) { mkdir('../services/', 0777, true); }
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
    return [false, 'Failed to create service file. Check permissions for the services/ directory.'];
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
            while ($section = $sectionsResult->fetch_assoc()) { $sections[] = $section; }
            $row['sections'] = $sections;
            $services[] = $row;
        }
        $response = ['status' => 'success', 'data' => $services];
        break;

    case 'add':
        $conn->begin_transaction();
        try {
            $serviceName = trim($_POST['name'] ?? '');
            $serviceDesc = $_POST['description'] ?? '';
            if (empty($serviceName)) { throw new Exception('Service Name cannot be empty.'); }

            $heroPath = handleFileUpload($_FILES['hero_media'] ?? null);

            $stmt = $conn->prepare("INSERT INTO services (name, description, hero_media_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $serviceName, $serviceDesc, $heroPath);
            $stmt->execute();
            $serviceId = $conn->insert_id;

            $sectionTitles = $_POST['section_title'] ?? [];
            $sectionDescriptions = $_POST['section_description'] ?? [];
            $stmt = $conn->prepare("INSERT INTO service_sections (service_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($sectionTitles as $index => $title) {
                $content = $sectionDescriptions[$index] ?? '';
                $fileKey = 'section_media_' . $index;
                $sectionPath = handleFileUpload($_FILES[$fileKey] ?? null);
                $stmt->bind_param("isssi", $serviceId, $title, $content, $sectionPath, $index);
                $stmt->execute();
            }
            
            list($fileGenerated, $message) = generateServiceFile($serviceId, $serviceName, $conn);
            if (!$fileGenerated) throw new Exception($message);
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Service added successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Add transaction failed: ' . $e->getMessage();
        }
        break;

    case 'edit':
        $conn->begin_transaction();
        try {
            $serviceId = $_POST['id'] ?? 0;
            $serviceName = trim($_POST['name'] ?? '');
            $serviceDesc = $_POST['description'] ?? '';
            if (empty($serviceName) || empty($serviceId)) { throw new Exception('Service Name or ID is missing.'); }

            $heroPath = $_POST['existing_hero_path'] ?? null;
            if (isset($_FILES['hero_media']) && $_FILES['hero_media']['error'] == UPLOAD_ERR_OK) {
                if ($heroPath && file_exists('../' . $heroPath)) { unlink('../' . $heroPath); }
                $heroPath = handleFileUpload($_FILES['hero_media']);
            }

            $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, hero_media_path = ? WHERE id = ?");
            $stmt->bind_param("sssi", $serviceName, $serviceDesc, $heroPath, $serviceId);
            $stmt->execute();
            
            // Safely update sections: delete old ones first, then insert new ones.
            $stmt = $conn->prepare("DELETE FROM service_sections WHERE service_id = ?");
            $stmt->bind_param("i", $serviceId);
            $stmt->execute();

            $sectionTitles = $_POST['section_title'] ?? [];
            $sectionDescriptions = $_POST['section_description'] ?? [];
            $existingSectionPaths = $_POST['existing_section_paths'] ?? [];
            $stmt = $conn->prepare("INSERT INTO service_sections (service_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($sectionTitles as $index => $title) {
                $content = $sectionDescriptions[$index] ?? '';
                $sectionPath = $existingSectionPaths[$index] ?? null;
                $fileKey = 'section_media_' . $index;
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
                    if ($sectionPath && file_exists('../' . $sectionPath)) { unlink('../' . $sectionPath); }
                    $sectionPath = handleFileUpload($_FILES[$fileKey]);
                }
                $stmt->bind_param("isssi", $serviceId, $title, $content, $sectionPath, $index);
                $stmt->execute();
            }
            
            list($fileGenerated, $message) = generateServiceFile($serviceId, $serviceName, $conn);
            if (!$fileGenerated) throw new Exception($message);
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Service updated successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Edit transaction failed: ' . $e->getMessage();
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
                if (file_exists($filePath)) { unlink($filePath); }
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