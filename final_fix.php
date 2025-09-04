<?php
// This is a self-contained script for debugging and fixing the update issue.
// It connects to the DB, handles a form submission, and displays the form.

// --- Make sure this path is correct ---
require_once 'db_connect.php'; 

$message = '';
$current_media_path = '';
$current_title = '';
$current_description = '';
$upload_path_relative = 'uploads/about/';
define('PROJECT_ROOT_PATH', __DIR__);

// --- Logic to handle the form when it is submitted ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $existing_media_path = $_POST['existing_media_path'] ?? '';
    $clear_media = isset($_POST['clear_media']);
    
    $final_media_path = $existing_media_path;

    // CASE 1: A new file is uploaded
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        // Delete the old file if it exists
        if ($existing_media_path && file_exists(PROJECT_ROOT_PATH . '/' . $existing_media_path)) {
            unlink(PROJECT_ROOT_PATH . '/' . $existing_media_path);
        }
        
        $file = $_FILES['media_file'];
        $filename = time() . '_' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], PROJECT_ROOT_PATH . '/' . $upload_path_relative . $filename)) {
            $final_media_path = $upload_path_relative . $filename;
            $message = "<p style='color:green;'>New file uploaded successfully.</p>";
        } else {
            $message = "<p style='color:red;'>Error uploading new file.</p>";
        }
    } 
    // CASE 2: The "Clear Media" checkbox was checked
    elseif ($clear_media) {
        if ($existing_media_path && file_exists(PROJECT_ROOT_PATH . '/' . $existing_media_path)) {
            unlink(PROJECT_ROOT_PATH . '/' . $existing_media_path);
        }
        $final_media_path = null;
        $message = "<p style='color:orange;'>Media was cleared.</p>";
    }
    // CASE 3: No new file, not clearing. The $final_media_path is already set to the existing path.

    // --- Final Update to the Database ---
    $stmt = $conn->prepare("UPDATE about_main SET title = ?, description = ?, media_path = ? WHERE id = 1");
    $stmt->bind_param("sss", $title, $description, $final_media_path);

    if ($stmt->execute()) {
        $message .= "<p style='color:green;'><strong>Database updated successfully!</strong> The media path was saved correctly.</p>";
    } else {
        $message .= "<p style='color:red;'><strong>DATABASE UPDATE FAILED:</strong> " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

// --- Logic to get the current data to display on the page ---
$result = $conn->query("SELECT title, description, media_path FROM about_main WHERE id = 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_title = $row['title'];
    $current_description = $row['description'];
    $current_media_path = $row['media_path'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Update Test</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; border: 1px solid #ccc; padding: 2rem; border-radius: 8px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 0.5rem; }
        textarea { min-height: 100px; }
        button { padding: 0.75rem 1.5rem; background-color: #007bff; color: white; border: none; cursor: pointer; }
        img, video { max-width: 100%; height: auto; border: 1px solid #ddd; }
        .message { border: 1px solid; padding: 1rem; margin-bottom: 2rem; }
    </style>
</head>
<body>

    <h1>Final About Section Update Test</h1>
    <p>This page directly tests the save logic. Replicate the bug here. Change only the text and click "Save Changes". If the media is preserved, the logic is correct.</p>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="final_fix.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="existing_media_path" value="<?php echo htmlspecialchars($current_media_path); ?>">

        <div class="form-group">
            <label>Current Media</label>
            <?php if ($current_media_path): ?>
                <?php $ext = pathinfo($current_media_path, PATHINFO_EXTENSION); ?>
                <?php if (in_array($ext, ['mp4', 'mov', 'webm'])): ?>
                    <video src="<?php echo htmlspecialchars($current_media_path); ?>" controls></video>
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($current_media_path); ?>" alt="Current Media">
                <?php endif; ?>
                <br>
                <input type="checkbox" name="clear_media" id="clear_media">
                <label for="clear_media">Check this box to delete the current media.</label>
            <?php else: ?>
                <p>No media file is currently saved.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="media_file">Upload New Media (optional, will replace existing)</label>
            <input type="file" name="media_file" id="media_file">
        </div>

        <div class="form-group">
            <label for="title">Main Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($current_title); ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description"><?php echo htmlspecialchars($current_description); ?></textarea>
        </div>

        <button type="submit">Save Changes</button>
    </form>

</body>
</html>