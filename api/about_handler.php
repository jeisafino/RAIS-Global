<?php
header('Content-Type: application/json');
require_once '../db_connect.php'; 

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$upload_path_relative = 'uploads/about/';
define('PROJECT_ROOT_PATH', dirname(__DIR__));

function delete_file($absolute_filepath) {
    if ($absolute_filepath && file_exists($absolute_filepath) && is_writable($absolute_filepath)) {
        unlink($absolute_filepath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $data = json_decode($_POST['data'], true);

        // --- THE DEFINITIVE FIX ---
        // 1. Read the CURRENT media path directly from the database.
        $current_media_result = $conn->query("SELECT media_path, media_type FROM about_main WHERE id = 1");
        $current_media_row = $current_media_result->fetch_assoc();
        $final_media_path = $current_media_row['media_path'] ?? null;
        $hero_media_type = $current_media_row['media_type'] ?? 'image';

        // 2. Now, process changes based on this reliable data.
        if (isset($_FILES['hero_media_file']) && $_FILES['hero_media_file']['error'] == 0) {
            // CASE 1: A new file is being uploaded.
            if ($final_media_path) { // Use the path we just read from the DB.
                delete_file(PROJECT_ROOT_PATH . '/' . $final_media_path);
            }
            $file = $_FILES['hero_media_file'];
            $filename = time() . '_' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], PROJECT_ROOT_PATH . '/' . $upload_path_relative . $filename)) {
                $final_media_path = $upload_path_relative . $filename;
                $hero_media_type = strpos($file['type'], 'video') === 0 ? 'video' : 'image';
            } else {
                throw new Exception('Failed to move new hero file.');
            }
        } 
        elseif (isset($data['hero']['clear_media']) && $data['hero']['clear_media']) {
            // CASE 2: The media was explicitly cleared.
            if ($final_media_path) { // Use the path we just read from the DB.
                delete_file(PROJECT_ROOT_PATH . '/' . $final_media_path);
            }
            $final_media_path = null;
        }
        // CASE 3: No new file and not clearing. The $final_media_path variable already holds the correct value from our database query.

        // 3. Update the database with the final, correct path.
        $stmt_hero = $conn->prepare("UPDATE about_main SET title = ?, description = ?, media_path = ?, media_type = ? WHERE id = 1");
        $stmt_hero->bind_param("ssss", $data['hero']['title'], $data['hero']['description'], $final_media_path, $hero_media_type);
        if (!$stmt_hero->execute()) { throw new Exception("Hero update failed: " . $stmt_hero->error); }

        // --- Process Content Blocks and Cards as before ---
        $conn->query("TRUNCATE TABLE about_content_blocks");
        // ... (rest of the script for blocks and cards is unchanged) ...
        $stmt_block = $conn->prepare("INSERT INTO about_content_blocks (type, content, media_path, media_type, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['contentBlocks'] as $i => $block) {
            $block_media_path = $block['media_path'] ?? null;
            $block_media_type = 'image';
            $file_key = 'block_media_file_' . $block['id'];
            if (isset($_FILES[$file_key])) {
                if ($block_media_path) delete_file(PROJECT_ROOT_PATH . '/' . $block_media_path);
                $file = $_FILES[$file_key];
                $filename = time() . '_block_' . $i . '_' . basename($file['name']);
                if (!move_uploaded_file($file['tmp_name'], PROJECT_ROOT_PATH . '/' . $upload_path_relative . $filename)) {
                    throw new Exception('Failed to move block file.');
                }
                $block_media_path = $upload_path_relative . $filename;
                $block_media_type = strpos($file['type'], 'video') === 0 ? 'video' : 'image';
            }
            $stmt_block->bind_param("ssssi", $block['type'], $block['content'], $block_media_path, $block_media_type, $i);
            $stmt_block->execute();
        }

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