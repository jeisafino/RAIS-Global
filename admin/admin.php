<?php
require_once '../config.php';

// Page-specific data
$page_title = "RAIS Admin Dashboard";
// Define "active" as someone seen in the last 15 seconds.
$active_threshold = 15; // in seconds

// --- CALCULATE SUMMARY CARD DATA ---
// Count active clients only
$sql_active = "SELECT COUNT(id) AS active_count FROM users WHERE role = 'client' AND last_activity >= NOW() - INTERVAL ? SECOND";
$stmt_active = $conn->prepare($sql_active);
$stmt_active->bind_param("i", $active_threshold);
$stmt_active->execute();
$result_active = $stmt_active->get_result();
$active_row = $result_active->fetch_assoc();
$active_users = $active_row['active_count'];
$stmt_active->close();

// Count total clients only
$sql_total = "SELECT COUNT(id) AS total_count FROM users WHERE role = 'client'";
$result_total = $conn->query($sql_total);
$total_row = $result_total->fetch_assoc();
$total_users = $total_row['total_count'];

// Inactive clients are the remainder
$inactive_users = $total_users - $active_users;

// --- FETCH USER DETAILS FOR THE TABLE ---
// Fetch client users only
$users = [];
$sql_users = "SELECT id, firstName, lastName, email, status, last_login, role, last_activity FROM users WHERE role = 'client' ORDER BY id ASC";
if ($result = $conn->query($sql_users)) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
} else {
    echo "ERROR: Could not execute user list query. " . $conn->error;
}

// --- FETCH LATEST CHAT MESSAGES FOR MONITORING ---
$latest_chats = [];
$sql_chats = "
    SELECT u.firstName, u.lastName, m.message
    FROM chat_messages m
    INNER JOIN (
        SELECT MAX(id) as max_id
        FROM chat_messages
        WHERE sender_id != 0 
        GROUP BY sender_id
        ORDER BY max_id DESC
        LIMIT 3
    ) latest_chat_messages ON m.id = latest_chat_messages.max_id
    INNER JOIN users u ON m.sender_id = u.id
    ORDER BY m.id DESC";

if ($result_chats = $conn->query($sql_chats)) {
    while ($row_chat = $result_chats->fetch_assoc()) {
        $latest_chats[] = $row_chat;
    }
    $result_chats->free();
}

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link rel="icon" href="../img/logoulit.png" />
    <link rel="stylesheet" href="style.css">

    <style>
        /* Light Mode Styles (Default) */
        .live-chat-list .chat-preview-item {
            background-color: #f8f9fa; /* Light gray background */
            border: 1px solid #e9ecef;
            padding: 0.75rem 1.25rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
        }
        .live-chat-list .user-identifier {
            color: #198754; /* Darker green for better contrast on light background */
            font-weight: 500;
            margin-right: 0.5rem;
        }
        .live-chat-list .message-preview {
            color: #495057; /* Standard text color */
        }
        
        /* --- BUTTON STYLE TO MATCH 'MANAGE ALL USERS' --- */
        .btn-view-chat-list {
            background-color: #811F1D; /* Solid dark red */
            color: #fff;
            border: 1px solid #811F1D;
            padding: 0.5rem 1.25rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            border-radius: 0.375rem; /* Standard bootstrap radius */
        }
        .btn-view-chat-list:hover {
            background-color: #9a2430; /* Slightly darker red on hover */
            border-color: #9a2430;
            color: #fff;
        }

        /* Dark Mode Overrides */
        body.dark-mode .live-chat-list .chat-preview-item {
            background-color: #2b2f35;
            border-color: #3e444a;
        }
        body.dark-mode .live-chat-list .user-identifier {
            color: #28a745; /* Brighter green for dark background */
        }
        body.dark-mode .live-chat-list .message-preview {
            color: #adb5bd;
        }
        
        /* --- TABLE ROW HOVER STYLES --- */
        /* Adds a smooth transition to the background color change */
        .table-hover tbody tr {
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        /* Applies the green background on hover ONLY in dark mode */
        body.dark-mode .table-hover tbody tr:hover {
            background-color: #0b4038 !important; /* Added !important to override Bootstrap defaults */
            color: #f8f9fa !important; /* This new line forces the text to be light */
        }

    </style>
    </head>
<body>
    <div class="main-wrapper">
        <?php require_once 'sidebar.php'; ?>
        <div class="content-area">
            <?php require_once 'header.php'; ?>
            <main class="main-content">
                <h1>Admin Dashboard</h1>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="content-card text-center">
                            <h2>Active Users</h2>
                            <h1 id="activeUserCount" class="display-4" style="color: #28a745;"><?php echo $active_users; ?></h1>
                            <p class="text-muted">Clients active in the last <?php echo $active_threshold; ?> seconds.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content-card text-center">
                            <h2>Inactive Users</h2>
                            <h1 id="inactiveUserCount" class="display-4" style="color: #ffc107;"><?php echo $inactive_users; ?></h1>
                            <p class="text-muted">Clients currently offline.</p>
                        </div>
                    </div>
                </div>

                <?php
                // Set the context for the partial file to show the correct button
                $page_context = 'dashboard';
                require_once '_user_table.php';
                ?>

                <div class="content-card mt-4">
                    <h2 class="mb-3">Live Chat Monitoring</h2>
                    <div class="live-chat-list mb-3">
                        <?php if (!empty($latest_chats)): ?>
                            <?php $user_counter = 1; ?>
                            <?php foreach ($latest_chats as $chat): ?>
                                <div class="chat-preview-item">
                                    <span class="user-identifier">User <?php echo $user_counter++; ?>:</span>
                                    <span class="message-preview">
                                        <?php 
                                            // Truncate message to fit preview
                                            echo htmlspecialchars(mb_strimwidth($chat['message'], 0, 45, "...")); 
                                        ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No recent chats to display.</p>
                        <?php endif; ?>
                    </div>
                    <a href="chat_management.php" class="btn-view-chat-list">View Chat List</a>
                </div>

            </main>
        </div>
    </div>
    
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="editUserModalLabel">Edit User</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><form id="editUserForm"><input type="hidden" id="editUserId"><div class="mb-3"><label for="editFirstName" class="form-label">First Name</label><input type="text" class="form-control" id="editFirstName" required></div><div class="mb-3"><label for="editLastName" class="form-label">Last Name</label><input type="text" class="form-control" id="editLastName" required></div><div class="mb-3"><label for="editEmail" class="form-label">Email address</label><input type="email" class="form-control" id="editEmail" required></div><div class="mb-3"><p><span id="editUserStatusBadge" class="badge"></span></p></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" form="editUserForm" class="btn btn-primary">Save Changes</button></div></div></div>
    </div>
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body" id="deleteModalBody">Are you sure you want to delete this user?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmationModalLabel">Success</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">Action completed successfully.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button></div></div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="togglemodeScript.js"></script>
    <script src="../js/user_management.js" defer></script>
    <script src="../js/admin_dashboard.js" defer></script>
</body>
</html>