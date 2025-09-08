<?php
// api/blogs_handler.php

header('Content-Type: application/json');
require_once '../db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Helper Functions ---
function handleFileUpload($file, $uploadDir = '../uploads/blog/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/blog/' . $fileName;
    }
    return null;
}

function generateBlogFile($blogId, $blogTitle, $conn) {
    $templatePath = 'blog_template.php'; // We will create this in the next step
    if (!file_exists($templatePath)) return [false, 'Blog template file not found.'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $blogTitle)));
    $newFileName = $slug . '.php';
    if (!is_dir('../blog/')) { mkdir('../blog/', 0777, true); } // Changed directory to /blog/
    $newFilePath = '../blog/' . $newFileName;
    $publicPath = 'blog/' . $newFileName;
    $templateContent = file_get_contents($templatePath);
    $newContent = str_replace('BLOG_ID_PLACEHOLDER', $blogId, $templateContent);
    if (file_put_contents($newFilePath, $newContent)) {
        $stmt = $conn->prepare("UPDATE blogs SET file_path = ? WHERE id = ?");
        $stmt->bind_param("si", $publicPath, $blogId);
        $stmt->execute();
        return [true, $publicPath];
    }
    return [false, 'Failed to create blog file. Check permissions for the blog/ directory.'];
}

// --- Main Logic ---
switch ($action) {
    case 'get':
        $result = $conn->query("SELECT * FROM blogs ORDER BY publish_date DESC");
        $blogs = [];
        while ($row = $result->fetch_assoc()) {
            $blogId = $row['id'];
            $sectionsResult = $conn->query("SELECT * FROM blog_sections WHERE blog_id = $blogId ORDER BY display_order ASC");
            $sections = [];
            while ($section = $sectionsResult->fetch_assoc()) { $sections[] = $section; }
            $row['sections'] = $sections;
            $blogs[] = $row;
        }
        $response = ['status' => 'success', 'data' => $blogs];
        break;

    case 'add':
    $conn->begin_transaction();
    try {
        $blogTitle = trim($_POST['title'] ?? '');
        $blogSummary = $_POST['summary'] ?? '';
        $blogAuthor = $_POST['author'] ?? '';
        $blogDate = $_POST['publish_date'] ?? null;
        $mapTitle = $_POST['map_title'] ?? null;
        $mapSummary = $_POST['map_summary'] ?? null;
        $mapAddress = $_POST['map_address'] ?? null; // Get address
        $mapLatitude = !empty($_POST['map_latitude']) ? $_POST['map_latitude'] : null;
        $mapLongitude = !empty($_POST['map_longitude']) ? $_POST['map_longitude'] : null;

        if (empty($blogTitle)) { throw new Exception('Blog Title cannot be empty.'); }

        $heroPath = handleFileUpload($_FILES['hero_media'] ?? null);

        $stmt = $conn->prepare("INSERT INTO blogs (title, summary, author, publish_date, hero_media_path, map_title, map_summary, map_address, map_latitude, map_longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $blogTitle, $blogSummary, $blogAuthor, $blogDate, $heroPath, $mapTitle, $mapSummary, $mapAddress, $mapLatitude, $mapLongitude);
        $stmt->execute();
        $blogId = $conn->insert_id;

        // *** MODIFIED SQL AND BIND_PARAM ***
        $stmt = $conn->prepare("INSERT INTO blogs (title, summary, author, publish_date, hero_media_path, map_title, map_summary, map_latitude, map_longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $blogTitle, $blogSummary, $blogAuthor, $blogDate, $heroPath, $mapTitle, $mapSummary, $mapLatitude, $mapLongitude);
        $stmt->execute();
        $blogId = $conn->insert_id;

        // This part remains the same
        $sectionTitles = $_POST['section_title'] ?? [];
        $sectionContents = $_POST['section_content'] ?? [];
        $stmt = $conn->prepare("INSERT INTO blog_sections (blog_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($sectionTitles as $index => $title) {
            $content = $sectionContents[$index] ?? '';
            $fileKey = 'section_media_' . $index;
            $sectionPath = handleFileUpload($_FILES[$fileKey] ?? null);
            $stmt->bind_param("isssi", $blogId, $title, $content, $sectionPath, $index);
            $stmt->execute();
        }

        list($fileGenerated, $message) = generateBlogFile($blogId, $blogTitle, $conn);
        if (!$fileGenerated) throw new Exception($message);

        $conn->commit();
        $response = ['status' => 'success', 'message' => "Blog added successfully."];
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Add transaction failed: ' . $e->getMessage();
    }
    break;

    case 'edit':
        $conn->begin_transaction();
    try {
        $blogId = $_POST['id'] ?? 0;
        $blogTitle = trim($_POST['title'] ?? '');
        $blogSummary = $_POST['summary'] ?? '';
        $blogAuthor = $_POST['author'] ?? '';
        $blogDate = $_POST['publish_date'] ?? null;
        $mapTitle = $_POST['map_title'] ?? null;
        $mapSummary = $_POST['map_summary'] ?? null;
        $mapAddress = $_POST['map_address'] ?? null; // Get address
        $mapLatitude = !empty($_POST['map_latitude']) ? $_POST['map_latitude'] : null;
        $mapLongitude = !empty($_POST['map_longitude']) ? $_POST['map_longitude'] : null;

        if (empty($blogTitle) || empty($blogId)) { throw new Exception('Blog Title or ID is missing.'); }

        $heroPath = $_POST['existing_hero_path'] ?? null;
        if (isset($_FILES['hero_media']) && $_FILES['hero_media']['error'] == UPLOAD_ERR_OK) {
            if ($heroPath && file_exists('../' . $heroPath)) { unlink('../' . $heroPath); }
            $heroPath = handleFileUpload($_FILES['hero_media']);
        }

        $stmt = $conn->prepare("UPDATE blogs SET title = ?, summary = ?, author = ?, publish_date = ?, hero_media_path = ?, map_title = ?, map_summary = ?, map_address = ?, map_latitude = ?, map_longitude = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $blogTitle, $blogSummary, $blogAuthor, $blogDate, $heroPath, $mapTitle, $mapSummary, $mapAddress, $mapLatitude, $mapLongitude, $blogId);
        $stmt->execute();

            // This part remains the same
            $stmt = $conn->prepare("DELETE FROM blog_sections WHERE blog_id = ?");
            $stmt->bind_param("i", $blogId);
            $stmt->execute();
            $clearedSectionPaths = $_POST['cleared_section_paths'] ?? [];
            foreach ($clearedSectionPaths as $pathToDelete) {
                if (!empty($pathToDelete) && file_exists('../' . $pathToDelete)) {
                    unlink('../' . $pathToDelete);
                }
            }
            $sectionTitles = $_POST['section_title'] ?? [];
            $sectionContents = $_POST['section_content'] ?? [];
            $existingSectionPaths = $_POST['existing_section_paths'] ?? [];
            $stmt = $conn->prepare("INSERT INTO blog_sections (blog_id, title, content, media_path, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($sectionTitles as $index => $title) {
                $content = $sectionContents[$index] ?? '';
                $sectionPath = $existingSectionPaths[$index] ?? null;
                $fileKey = 'section_media_' . $index;
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
                    if ($sectionPath && file_exists('../' . $sectionPath)) { unlink('../' . $sectionPath); }
                    $sectionPath = handleFileUpload($_FILES[$fileKey]);
                }
                $stmt->bind_param("isssi", $blogId, $title, $content, $sectionPath, $index);
                $stmt->execute();
            }

            list($fileGenerated, $message) = generateBlogFile($blogId, $blogTitle, $conn);
            if (!$fileGenerated) throw new Exception($message);

            $conn->commit();
            $response = ['status' => 'success', 'message' => "Blog updated successfully."];
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Edit transaction failed: ' . $e->getMessage();
        }
        break;
}

echo json_encode($response);
?>