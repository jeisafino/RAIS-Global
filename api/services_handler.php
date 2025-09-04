<?php
// api/services_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Functions ---
// In /api/services_handler.php

function handleFileUpload($file, $uploadDir = '../uploads/service/') { // THIS PATH IS UPDATED
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // This path is also updated to match
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
        $serviceName = $_POST['name'] ?? '';
        $serviceDesc = $_POST['description'] ?? '';
         if (empty($serviceName)) {
        // If the service name is empty, stop and send an error.
        $response['message'] = 'Service Name cannot be empty.';
        break; 
    }
        $sectionsData = json_decode($_POST['sections'] ?? '[]', true);
        $serviceId = ($action === 'edit') ? ($_POST['id'] ?? 0) : 0;

        $conn->begin_transaction();

        try {
            // Handle Hero Media
            $heroPath = $_POST['existing_hero_path'] ?? null;
            if (isset($_FILES['hero_media'])) {
                $heroPath = handleFileUpload($_FILES['hero_media']);
            }

            // Insert or Update Service
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO services (name, description, hero_media_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $serviceName, $serviceDesc, $heroPath);
                $stmt->execute();
                $serviceId = $conn->insert_id;
            } else {
                $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, hero_media_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $serviceName, $serviceDesc, $heroPath, $serviceId);
                $stmt->execute();
                // Clear old sections
                $conn->query("DELETE FROM service_sections WHERE service_id = $serviceId");
            }

            // Insert Sections
            $stmt = $conn->prepare("INSERT INTO service_sections (service_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($sectionsData as $index => $section) {
                $sectionPath = $section['existing_media_path'] ?? null;
                if (isset($_FILES["section_media_$index"])) {
                    $sectionPath = handleFileUpload($_FILES["section_media_$index"]);
                }
                $stmt->bind_param("isssi", $serviceId, $section['title'], $section['description'], $sectionPath, $index);
                $stmt->execute();
            }

            // Generate the .php file
            list($fileGenerated, $message) = generateServiceFile($serviceId, $serviceName, $conn);
            if (!$fileGenerated) throw new Exception($message);
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Service " . ($action === 'add' ? "added" : "updated") . " successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Database transaction failed: ' . $e->getMessage();
        }
        break;

    case 'delete':
        $serviceId = $_POST['id'] ?? 0;
        // You might want to also delete files from the server here
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $serviceId);
        if ($stmt->execute()) {
            // Also delete the generated file
            $filePath = '../services/' . $_POST['file_slug'] . '.php';
            if (file_exists($filePath)) {
                unlink($filePath);
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
$conn->close();
?>