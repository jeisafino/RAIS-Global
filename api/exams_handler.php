<?php
// api/exams_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Functions ---
function handleFileUpload($file, $uploadDir = '../uploads/exam/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/exam/' . $fileName;
    }
    return null;
}

function generateExamFile($examId, $examName, $conn) {
    $templatePath = 'exam_template.php'; // We will create this file next
    if (!file_exists($templatePath)) return [false, 'Exam template file not found.'];
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $examName)));
    $newFileName = $slug . '.php';
    $newFilePath = '../' . $newFileName; // Place it in the root directory
    $publicPath = $newFileName; 

    $templateContent = file_get_contents($templatePath);
    $newContent = str_replace('EXAM_ID_PLACEHOLDER', $examId, $templateContent);

    if (file_put_contents($newFilePath, $newContent)) {
        $stmt = $conn->prepare("UPDATE exams SET file_path = ? WHERE id = ?");
        $stmt->bind_param("si", $publicPath, $examId);
        $stmt->execute();
        return [true, $publicPath];
    }
    return [false, 'Failed to create exam file. Check permissions for the root directory.'];
}

// --- Main Logic ---
switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM exams ORDER BY name ASC");
        $exams = [];
        while ($row = $result->fetch_assoc()) {
            $examId = $row['id'];
            // Fetch related data
            $row['formats'] = $conn->query("SELECT * FROM exam_formats WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $row['infocards'] = $conn->query("SELECT * FROM exam_infocards WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $row['faqs'] = $conn->query("SELECT * FROM exam_faqs WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $exams[] = $row;
        }
        $response = ['status' => 'success', 'data' => $exams];
        break;

    case 'add':
        $conn->begin_transaction();
        try {
            $name = trim($_POST['name'] ?? '');
            $description = $_POST['description'] ?? '';
            $about_content = $_POST['about_content'] ?? '';
            if (empty($name)) { throw new Exception('Exam Name is required.'); }

            $heroPath = handleFileUpload($_FILES['hero_media'] ?? null);
            $aboutPath = handleFileUpload($_FILES['about_media'] ?? null);

            $stmt = $conn->prepare("INSERT INTO exams (name, description, hero_media_path, about_content, about_media_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $description, $heroPath, $about_content, $aboutPath);
            $stmt->execute();
            $examId = $conn->insert_id;

            // Insert formats
            $formats = json_decode($_POST['formats'] ?? '[]', true);
            $stmt_format = $conn->prepare("INSERT INTO exam_formats (exam_id, icon_class, title, description, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($formats as $i => $f) { $stmt_format->bind_param("isssi", $examId, $f['icon'], $f['title'], $f['description'], $i); $stmt_format->execute(); }
            
            // Insert infocards
            $infocards = json_decode($_POST['infocards'] ?? '[]', true);
            $stmt_infocard = $conn->prepare("INSERT INTO exam_infocards (exam_id, title, description, display_order) VALUES (?, ?, ?, ?)");
            foreach ($infocards as $i => $c) { $stmt_infocard->bind_param("issi", $examId, $c['title'], $c['description'], $i); $stmt_infocard->execute(); }

            // Insert FAQs
            $faqs = json_decode($_POST['faqs'] ?? '[]', true);
            $stmt_faq = $conn->prepare("INSERT INTO exam_faqs (exam_id, question, answer, display_order) VALUES (?, ?, ?, ?)");
            foreach ($faqs as $i => $q) { $stmt_faq->bind_param("issi", $examId, $q['question'], $q['answer'], $i); $stmt_faq->execute(); }
            
            list($fileGenerated, $message) = generateExamFile($examId, $name, $conn);
            if (!$fileGenerated) throw new Exception($message);
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Exam '{$name}' added successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Add failed: ' . $e->getMessage();
        }
        break;

    case 'edit':
        $conn->begin_transaction();
        try {
            $examId = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            if (empty($name) || empty($examId)) { throw new Exception('Exam Name or ID is missing.'); }
            
            $description = $_POST['description'] ?? '';
            $about_content = $_POST['about_content'] ?? '';

            $heroPath = $_POST['existing_hero_path'] ?? null;
            if (isset($_FILES['hero_media'])) {
                if ($heroPath && file_exists('../' . $heroPath)) unlink('../' . $heroPath);
                $heroPath = handleFileUpload($_FILES['hero_media']);
            }
            $aboutPath = $_POST['existing_about_path'] ?? null;
            if (isset($_FILES['about_media'])) {
                if ($aboutPath && file_exists('../' . $aboutPath)) unlink('../' . $aboutPath);
                $aboutPath = handleFileUpload($_FILES['about_media']);
            }

            $stmt = $conn->prepare("UPDATE exams SET name=?, description=?, hero_media_path=?, about_content=?, about_media_path=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $description, $heroPath, $about_content, $aboutPath, $examId);
            $stmt->execute();

            // Clear old relational data
            $conn->query("DELETE FROM exam_formats WHERE exam_id = $examId");
            $conn->query("DELETE FROM exam_infocards WHERE exam_id = $examId");
            $conn->query("DELETE FROM exam_faqs WHERE exam_id = $examId");
            
            // Re-insert new data (same logic as 'add')
            $formats = json_decode($_POST['formats'] ?? '[]', true);
            $stmt_format = $conn->prepare("INSERT INTO exam_formats (exam_id, icon_class, title, description, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($formats as $i => $f) { $stmt_format->bind_param("isssi", $examId, $f['icon'], $f['title'], $f['description'], $i); $stmt_format->execute(); }
            
            $infocards = json_decode($_POST['infocards'] ?? '[]', true);
            $stmt_infocard = $conn->prepare("INSERT INTO exam_infocards (exam_id, title, description, display_order) VALUES (?, ?, ?, ?)");
            foreach ($infocards as $i => $c) { $stmt_infocard->bind_param("issi", $examId, $c['title'], $c['description'], $i); $stmt_infocard->execute(); }

            $faqs = json_decode($_POST['faqs'] ?? '[]', true);
            $stmt_faq = $conn->prepare("INSERT INTO exam_faqs (exam_id, question, answer, display_order) VALUES (?, ?, ?, ?)");
            foreach ($faqs as $i => $q) { $stmt_faq->bind_param("issi", $examId, $q['question'], $q['answer'], $i); $stmt_faq->execute(); }

            list($fileGenerated, $message) = generateExamFile($examId, $name, $conn);
            if (!$fileGenerated) throw new Exception($message);

            $conn->commit();
            $response = ['status' => 'success', 'message' => "Exam '{$name}' updated."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Update failed: ' . $e->getMessage();
        }
        break;

    case 'delete':
        $examId = $_POST['id'] ?? 0;
        $filePath = $_POST['file_path'] ?? '';
        $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->bind_param("i", $examId);
        if ($stmt->execute()) {
            if (!empty($filePath) && file_exists('../' . $filePath)) {
                unlink('../' . $filePath);
            }
            $response = ['status' => 'success', 'message' => 'Exam deleted.'];
        } else {
            $response['message'] = 'Failed to delete exam.';
        }
        break;

    default:
        $response['message'] = 'Invalid action specified.';
        break;
}

echo json_encode($response);
?>