<?php
// api/exams_handler.php (FINAL & COMPLETE)

header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function handleFileUpload($file, $uploadDir = '../uploads/exam/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $fileName = uniqid() . '-' . basename(preg_replace("/[^a-zA-Z0-9.\s_-]/", "", $file['name']));
    $targetPath = $uploadDir . $fileName;
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/exam/' . $fileName;
    }
    return null;
}

function generateExamFile($examId, $examName, $conn) {
    $templatePath = 'exam_template.php';
    if (!file_exists($templatePath)) return [false, 'Exam template file not found.'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $examName)));
    $newFileName = $slug . '.php';
    $newFilePath = '../' . $newFileName;
    $publicPath = $newFileName; 
    $templateContent = file_get_contents($templatePath);
    $newContent = str_replace('EXAM_ID_PLACEHOLDER', $examId, $templateContent);
    if (file_put_contents($newFilePath, $newContent)) {
        $stmt = $conn->prepare("UPDATE exams SET file_path = ? WHERE id = ?");
        $stmt->bind_param("si", $publicPath, $examId);
        $stmt->execute();
        return [true, $publicPath];
    }
    return [false, 'Failed to create exam file. Check permissions.'];
}

switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM exams ORDER BY name ASC");
        $exams = [];
        while ($row = $result->fetch_assoc()) {
            $examId = $row['id'];
            $row['formats'] = $conn->query("SELECT * FROM exam_formats WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $row['choice_cards'] = $conn->query("SELECT * FROM exam_choice_cards WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $row['infocards'] = $conn->query("SELECT * FROM exam_infocards WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $row['faqs'] = $conn->query("SELECT * FROM exam_faqs WHERE exam_id = $examId ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
            $exams[] = $row;
        }
        $response = ['status' => 'success', 'data' => $exams];
        break;

    case 'add':
    case 'edit':
        $conn->begin_transaction();
        try {
            $examId = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) { throw new Exception('Exam Name is required.'); }

            $description = $_POST['description'] ?? '';
            $about_content = $_POST['about_content'] ?? '';
            $infocards_intro = $_POST['infocards_intro'] ?? '';

            if ($action === 'add') {
                $heroPath = handleFileUpload($_FILES['hero_media'] ?? null);
                $heroLogoPath = handleFileUpload($_FILES['hero_logo'] ?? null);
                $aboutPath = handleFileUpload($_FILES['about_media'] ?? null);
                
                $stmt = $conn->prepare("INSERT INTO exams (name, description, hero_media_path, hero_logo_path, about_content, about_media_path, infocards_intro) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $name, $description, $heroPath, $heroLogoPath, $about_content, $aboutPath, $infocards_intro);
                $stmt->execute();
                $examId = $conn->insert_id;
            } else { // Edit
                $heroPath = $_POST['existing_hero_path'] ?? null;
                if (isset($_FILES['hero_media'])) { if ($heroPath && file_exists('../' . $heroPath)) unlink('../' . $heroPath); $heroPath = handleFileUpload($_FILES['hero_media']); }
                
                $heroLogoPath = $_POST['existing_hero_logo_path'] ?? null;
                if (isset($_FILES['hero_logo'])) { if ($heroLogoPath && file_exists('../' . $heroLogoPath)) unlink('../' . $heroLogoPath); $heroLogoPath = handleFileUpload($_FILES['hero_logo']); }

                $aboutPath = $_POST['existing_about_path'] ?? null;
                if (isset($_FILES['about_media'])) { if ($aboutPath && file_exists('../' . $aboutPath)) unlink('../' . $aboutPath); $aboutPath = handleFileUpload($_FILES['about_media']); }

                $stmt = $conn->prepare("UPDATE exams SET name=?, description=?, hero_media_path=?, hero_logo_path=?, about_content=?, about_media_path=?, infocards_intro=? WHERE id=?");
                $stmt->bind_param("sssssssi", $name, $description, $heroPath, $heroLogoPath, $about_content, $aboutPath, $infocards_intro, $examId);
                $stmt->execute();

                $conn->query("DELETE FROM exam_formats WHERE exam_id = $examId");
                $conn->query("DELETE FROM exam_choice_cards WHERE exam_id = $examId");
                $conn->query("DELETE FROM exam_infocards WHERE exam_id = $examId");
                $conn->query("DELETE FROM exam_faqs WHERE exam_id = $examId");
            }
            
            $formats = json_decode($_POST['formats'] ?? '[]', true);
            $stmt_format = $conn->prepare("INSERT INTO exam_formats (exam_id, icon_class, title, description, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($formats as $i => $f) { $stmt_format->bind_param("isssi", $examId, $f['icon'], $f['title'], $f['description'], $i); $stmt_format->execute(); }
            
            $choicecards = json_decode($_POST['choicecards'] ?? '[]', true);
            $stmt_choice = $conn->prepare("INSERT INTO exam_choice_cards (exam_id, title, description, image_path, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($choicecards as $i => $cc) {
                $imagePath = $_POST['existing_choice_card_image_' . $i] ?? null;
                if (isset($_FILES['choice_card_image_' . $i])) {
                    if ($imagePath && file_exists('../' . $imagePath)) unlink('../' . $imagePath);
                    $imagePath = handleFileUpload($_FILES['choice_card_image_' . $i]);
                }
                $stmt_choice->bind_param("isssi", $examId, $cc['title'], $cc['description'], $imagePath, $i);
                $stmt_choice->execute();
            }

            $infocards = json_decode($_POST['infocards'] ?? '[]', true);
            $stmt_infocard = $conn->prepare("INSERT INTO exam_infocards (exam_id, title, description, display_order) VALUES (?, ?, ?, ?)");
            foreach ($infocards as $i => $c) { $stmt_infocard->bind_param("issi", $examId, $c['title'], $c['description'], $i); $stmt_infocard->execute(); }

            $faqs = json_decode($_POST['faqs'] ?? '[]', true);
            $stmt_faq = $conn->prepare("INSERT INTO exam_faqs (exam_id, question, answer, display_order) VALUES (?, ?, ?, ?)");
            foreach ($faqs as $i => $q) { $stmt_faq->bind_param("issi", $examId, $q['question'], $q['answer'], $i); $stmt_faq->execute(); }
            
            list($fileGenerated, $message) = generateExamFile($examId, $name, $conn);
            if (!$fileGenerated) throw new Exception($message);
            
            $conn->commit();
            $response = ['status' => 'success', 'message' => "Exam '{$name}' processed successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Transaction failed: ' . $e->getMessage();
        }
        break;

    case 'delete':
        $examId = $_POST['id'] ?? 0;
        $filePathResult = $conn->query("SELECT file_path FROM exams WHERE id = $examId");
        $examFile = $filePathResult->fetch_assoc();
        $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->bind_param("i", $examId);
        if ($stmt->execute()) {
            if ($examFile && !empty($examFile['file_path'])) {
                $pathToDelete = '../' . $examFile['file_path'];
                if (file_exists($pathToDelete)) { unlink($pathToDelete); }
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