<?php
header('Content-Type: application/json');
require_once '../db_connect.php'; // Adjust path if needed

// --- More Robust Path Definition ---
// This defines the absolute path to your project's root folder (e.g., C:/xampp/htdocs/RAIS-Global)
define('PROJECT_ROOT_PATH', dirname(__DIR__));
$upload_dir_absolute = PROJECT_ROOT_PATH . '/uploads/about/'; // Absolute path for moving files
$upload_path_relative = 'uploads/about/'; // Relative path to store in DB

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Function to safely delete a file using an absolute path
function delete_file($absolute_filepath) {
    if ($absolute_filepath && file_exists($absolute_filepath) && is_writable($absolute_filepath)) {
        unlink($absolute_filepath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET logic remains the same...
    try {
        $data = [];
        $result_main = $conn->query("SELECT * FROM about_main WHERE id = 1");
        $data['hero'] = $result_main->fetch_assoc();
        $result_blocks = $conn->query("SELECT * FROM about_content_blocks ORDER BY sort_order ASC");
        $data['contentBlocks'] = [];
        while ($row = $result_blocks->fetch_assoc()) { $data['contentBlocks'][] = $row; }
        $result_cards = $conn->query("SELECT * FROM about_cards ORDER BY sort_order ASC");
        $data['cards'] = [];
        while ($row = $result_cards->fetch_assoc()) { $data['cards'][] = $row; }
        $response = ['status' => 'success', 'data' => $data];
    } catch (Exception $e) {
        $response['message'] = 'Database query failed: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    // Check if the upload directory is writable
    if (!is_dir($upload_dir_absolute) || !is_writable($upload_dir_absolute)) {
        $response['message'] = 'Upload directory does not exist or is not writable. Please check server permissions for uploads/about/.';
        echo json_encode($response);
        exit();
    }
    
    $conn->begin_transaction();
    try {
        $data = json_decode($_POST['data'], true);
        $hero_data = $data['hero'];
        
        // --- Process Hero Section ---
        $current_hero = $conn->query("SELECT media_path FROM about_main WHERE id = 1")->fetch_assoc();
        $hero_media_path_db = $current_hero['media_path'] ?? null;
        $hero_media_type = 'image';

        if (isset($_FILES['hero_media_file'])) {
            if ($hero_media_path_db) delete_file(PROJECT_ROOT_PATH . '/' . $hero_media_path_db);
            
            $file = $_FILES['hero_media_file'];
            $filename = time() . '_' . basename($file['name']);
            // ADDED: Check if move_uploaded_file succeeds
            if (!move_uploaded_file($file['tmp_name'], $upload_dir_absolute . $filename)) {
                throw new Exception('Failed to move uploaded hero file. Check directory permissions.');
            }
            $hero_media_path_db = $upload_path_relative . $filename;
            $hero_media_type = strpos($file['type'], 'video') === 0 ? 'video' : 'image';
        } elseif (isset($hero_data['clear_media']) && $hero_data['clear_media'] && $hero_media_path_db) {
            delete_file(PROJECT_ROOT_PATH . '/' . $hero_media_path_db);
            $hero_media_path_db = null;
        }

        $stmt = $conn->prepare("UPDATE about_main SET title = ?, description = ?, media_path = ?, media_type = ? WHERE id = 1");
        $stmt->bind_param("ssss", $hero_data['title'], $hero_data['description'], $hero_media_path_db, $hero_media_type);
        $stmt->execute();

        // (The rest of the logic for blocks and cards is similar and will benefit from the robust pathing)
        // For brevity, the full logic for blocks and cards is omitted here, but the principle is the same.
        // The previously provided full script already contains the correct logic flow.
        // This example focuses on demonstrating the added error checking.
        
        // --- Process Content Blocks (truncated for clarity, use your existing full logic) ---
        $conn->query("TRUNCATE TABLE about_content_blocks");
        $stmt_block = $conn->prepare("INSERT INTO about_content_blocks (type, content, media_path, media_type, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['contentBlocks'] as $i => $block) {
            $block_media_path = !empty($block['media_path']) ? $block['media_path'] : null;
            $block_media_type = 'image';
            $file_key = 'block_media_file_' . $block['id'];

            if (isset($_FILES[$file_key])) {
                $file = $_FILES[$file_key];
                $filename = time() . '_' . $i . '_' . basename($file['name']);
                if (!move_uploaded_file($file['tmp_name'], $upload_dir_absolute . $filename)) {
                    throw new Exception('Failed to move uploaded block file. Check permissions.');
                }
                $block_media_path = $upload_path_relative . $filename;
                $block_media_type = strpos($file['type'], 'video') === 0 ? 'video' : 'image';
            }
            $stmt_block->bind_param("ssssi", $block['type'], $block['content'], $block_media_path, $block_media_type, $i);
            $stmt_block->execute();
        }

        // --- Process Cards ---
        $conn->query("TRUNCATE TABLE about_cards");
        $stmt_card = $conn->prepare("INSERT INTO about_cards (tab_title, card_title, content, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($data['cards'] as $i => $card) {
            $stmt_card->bind_param("sssi", $card['tabTitle'], $card['cardTitle'], $card['content'], $i);
            $stmt_card->execute();
        }

        $conn->commit();
        $response = ['status' => 'success', 'message' => 'About section updated successfully.'];
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Save failed: ' . $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>