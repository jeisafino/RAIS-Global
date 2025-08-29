<?php
// settings.php - Page for users to manage their account settings

// Start the session to access logged-in user's data.
session_start();

// Include the database configuration file.
include_once '../config.php';

// Check if the user is logged in. If not, redirect them to the login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

// Get the logged-in user's ID from the session.
$userId = $_SESSION['id'];

// Fetch user's dark mode setting
$stmt = $conn->prepare("SELECT firstName, dark_mode FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$darkModeEnabled = $user ? $user['dark_mode'] : false; // Default to false if not found
$stmt->close();
$conn->close();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RAIS Settings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../img/logoulit.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        :root {
            --rais-primary-green: #004d40;
            --rais-dark-green: #00332c;
            --rais-text-dark: #333333;
            --rais-text-light: #525252;
            --rais-card-bg: #FFFFFF;
            --rais-light-gray: #E8E8E8;
            --rais-bg-light: #F7F7F7;
            --rais-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            --rais-button-maroon: #811F1D;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--rais-light-gray);
            color: var(--rais-text-dark);
            overflow: hidden; /* Prevent body scroll */
        }

        .main-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: var(--rais-primary-green);
            width: 70px;
            flex-shrink: 0;
            color: white;
            padding: 30px 0;
            box-shadow: var(--rais-shadow);
            transition: width 0.3s ease-in-out;
            overflow: hidden;
            position: fixed;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1031;
        }

        .sidebar:hover {
            width: 280px;
        }

        .sidebar .logo {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 1px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar:hover .logo {
            opacity: 1;
        }

        .sidebar .nav {
            flex-grow: 1;
        }

        .sidebar .nav-link {
            color: white;
            font-weight: 500;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            white-space: nowrap;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background-color: var(--rais-dark-green);
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            min-width: 20px;
            text-align: center;
        }

        .sidebar .nav-link span {
            opacity: 0;
            transition: opacity 0.1s ease-in-out 0.2s;
        }

        .sidebar:hover .nav-link span {
            opacity: 1;
        }

        .sidebar .footer-text {
            font-size: 0.8rem;
            color: #bbb;
            text-align: center;
            padding: 15px 0;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            margin-top: auto;
        }

        .sidebar:hover .footer-text {
            opacity: 1;
        }
        
        .content-area {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            margin-left: 70px;
        }

        /* Header Styles */
        .header {
            background-color: var(--rais-bg-light);
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px;
            box-shadow: var(--rais-shadow);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        .header-brand {
            gap: 10px;
        }

        .header-logo-img {
            height: 100px;
            width: auto;
            object-fit: contain;
        }

        .header-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--rais-text-dark);
            white-space: nowrap;
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-status .badge {
            background-color: var(--rais-button-maroon);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 5px 15px;
            border-radius: 20px;
        }

        .user-status .btn {
            color: var(--rais-text-light);
            font-size: 1.5rem;
            padding: 0;
        }
        
        .power-btn i {
            color: #dc3545;
        }
        
        .power-btn:hover i {
            color: #a71d2a;
        }

        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            padding: 30px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-content h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .content-card {
            background-color: var(--rais-card-bg);
            border-radius: 12px;
            box-shadow: var(--rais-shadow);
            padding: 30px;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--rais-light-gray);
        }

        .setting-item:last-child {
            border-bottom: none;
        }
        
        /* Floating Button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--rais-button-maroon);
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            transition: background-color 0.2s;
            z-index: 100;
        }

        .floating-btn:hover {
            background-color: var(--rais-dark-green);
        }

        /* Collapsible Chatbox */
        .chat-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            max-height: 500px;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--rais-shadow);
            display: flex;
            flex-direction: column;
            z-index: 99;
            transform: translateY(100%);
            opacity: 0;
            visibility: hidden;
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        .chat-container.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .chat-header {
            background-color: var(--rais-primary-green);
            color: white;
            padding: 1rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            cursor: pointer;
        }

        .chat-body {
            padding: 1rem;
            flex-grow: 1;
            overflow-y: auto;
            background-color: var(--rais-bg-light);
            display: flex;
            flex-direction: column-reverse;
            height: 350px;
        }

        .chat-footer {
            padding: 1rem;
            border-top: 1px solid var(--rais-light-gray);
        }

        .chat-footer .input-group {
            border-radius: 20px;
        }

        .chat-footer .form-control {
            border-radius: 20px 0 0 20px;
        }

        .chat-toggle-btn {
            position: fixed;
            bottom: 30px;
            right: 100px;
            background-color: var(--rais-button-maroon);
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 100;
        }
        
        #full-screen-chat {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: var(--rais-bg-light);
            z-index: 2000;
            display: none;
            flex-direction: column;
        }

        .chat-header-fullscreen {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: var(--rais-primary-green);
            color: white;
            flex-shrink: 0;
        }

        .back-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 1rem;
        }

        .chat-title-fullscreen {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .chat-body-fullscreen {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column-reverse;
        }

        .chat-footer-fullscreen {
            padding: 1rem;
            border-top: 1px solid var(--rais-light-gray);
            background-color: white;
            flex-shrink: 0;
        }

        /* Dark Mode Styles */
        .dark-mode-logo {
            display: none;
        }
        .dark-mode .light-mode-logo {
            display: none;
        }
        .dark-mode .dark-mode-logo {
            display: block;
        }
        body.dark-mode {
            background-color: #121212;
            color: #EAEAEA;
        }
        .dark-mode .sidebar {
            background-color: #1a1a1a;
            border-right: 1px solid #2c2c2c;
        }
        .dark-mode .header,
        .dark-mode .content-card,
        .dark-mode .chat-container,
        .dark-mode .modal-content,
        .dark-mode #full-screen-chat,
        .dark-mode .chat-footer-fullscreen {
            background-color: #1e1e1e;
            color: #EAEAEA;
            border: 1px solid #2c2c2c;
        }
        .dark-mode .header {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .dark-mode .header-title,
        .dark-mode h1, .dark-mode h6,
        .dark-mode .user-status .me-3,
        .dark-mode .chat-title-fullscreen {
            color: #EAEAEA !important;
        }
        .dark-mode .setting-item p,
        .dark-mode .text-muted {
            color: #B0B0B0 !important;
        }
        .dark-mode .sidebar .nav-link.active,
        .dark-mode .sidebar .nav-link:hover {
            background-color: #00332c;
        }
        .dark-mode .setting-item {
            border-bottom-color: #2c2c2c;
        }
        .dark-mode .toast {
            background-color: #2a2a2a;
            color: #EAEAEA;
            border: 1px solid #3c3c3c;
        }
        .dark-mode .toast-header {
            background-color: #3c3c3c;
            color: #EAEAEA;
        }
        .dark-mode .toast .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .dark-mode .form-check-input:checked {
            background-color: var(--rais-primary-green);
            border-color: var(--rais-primary-green);
        }
        .dark-mode .btn-outline-secondary {
            color: #EAEAEA;
            border-color: #EAEAEA;
        }
        .dark-mode .btn-outline-secondary:hover {
            background-color: #EAEAEA;
            color: #1e1e1e;
        }
        .dark-mode .text-danger {
            color: #ff5252 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 50px;
                padding-bottom: 50px;
                overflow: auto;
            }

            .main-wrapper {
                flex-direction: column;
                height: auto;
            }

            .content-area {
                height: auto;
                overflow-y: visible;
                margin-left: 0;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1030;
                height: 50px;
            }

            .header-title {
                display: none;
            }

            .main-content {
                padding-top: 15px;
            }

            /* Transform sidebar into a bottom navigation bar */
            .sidebar {
                width: 100%;
                height: 50px;
                position: fixed;
                top: auto;
                bottom: 0;
                left: 0;
                z-index: 1029;
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                padding: 0;
                transition: none;
            }

            .sidebar:hover {
                width: 100%;
            }

            .sidebar .logo,
            .sidebar .footer-text {
                display: none;
            }

            .sidebar .nav {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                flex-grow: 1;
                height: 100%;
            }

            .sidebar .nav-link {
                flex: 1;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                padding: 0 10px;
                gap: 0;
                height: 100%;
            }

            .sidebar .nav-link:hover {
                background-color: var(--rais-dark-green);
            }

            .sidebar .nav-link i {
                font-size: 1.5rem;
                margin-bottom: 0;
            }

            .sidebar .nav-link span,
            .sidebar:hover .nav-link span {
                display: none;
            }

            .floating-btn {
                bottom: 80px;
                right: 15px;
            }

            .chat-toggle-btn {
                bottom: 80px;
                right: 85px;
            }

            .chat-container {
                display: none !important;
            }
        }
    </style>
</head>

<body class="<?php echo $darkModeEnabled ? 'dark-mode' : ''; ?>">
    <div class="main-wrapper">
        <!-- Sidebar -->
                <?php require_once 'sidebar.php' ?>

        <!-- Main Content Area -->
        <div class="content-area">
            <!-- Header -->
            <div class="header">
                <div class="header-brand d-flex align-items-center">
                    <img src="../img/logo.png" alt="RAIS Logo" class="header-logo-img light-mode-logo">
                    <img src="../img/logo1.png" alt="RAIS Logo" class="header-logo-img dark-mode-logo" onerror="this.style.display='none'">
                    <span class="header-title">Roman & Associates Immigration Services</span>
                </div>
                <div class="user-status d-flex align-items-center gap-2">
                    <div id="headerDate" class="me-3" style="font-weight: 500;"><?= date('F j, Y') ?></div>
                    <a href="#" class="btn btn-link power-btn" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-power"></i></a>
                    <span class="badge"><?php echo htmlspecialchars($user['firstName']); ?></span>
                </div>
            </div>

            <!-- Main Content -->
            <main class="main-content">
                <h1>Settings</h1>
                <div class="content-card">
                    <div class="setting-item">
                        <div>
                            <h6 class="mb-1" id="darkModeLabel"><?php echo $darkModeEnabled ? 'Turn Off Dark Mode' : 'Turn On Dark Mode'; ?></h6>
                            <p class="mb-0 text-muted">Toggle the dark theme for the interface.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="darkModeSwitch" <?php echo $darkModeEnabled ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="darkModeSwitch"></label>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div>
                            <h6 class="mb-1">Change Password</h6>
                            <p class="mb-0 text-muted">Update your account password regularly.</p>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change</button>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div>
                            <h6 class="mb-1 text-danger">Delete Account</h6>
                            <p class="mb-0 text-muted">Permanently delete your account and data.</p>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Floating Action Button -->
    <a href="book-flight.php" class="floating-btn text-decoration-none">
        <i class="bi bi-plus-lg"></i>
    </a>

    <!-- Collapsible Chatbox -->
    <div class="chat-container" id="chatContainer">
        <div class="chat-header d-flex justify-content-between align-items-center" onclick="toggleChat()">
            <h5 class="chat-modal-title mb-0"><i class="bi bi-chat-dots-fill me-2"></i>Live Chat</h5>
            <i class="bi bi-x-lg text-white"></i>
        </div>
        <div class="chat-body">
            <div class="text-center text-muted">RAIS Support how may i assist you?</div>
        </div>
        <div class="chat-footer">
            <div class="input-group">
                <input type="text" class="form-control message-input" placeholder="Type a message..."
                    aria-label="Message input">
                <button class="btn btn-outline-secondary" type="button" id="send-button-popup">
                    <i class="bi bi-send-fill text-dark"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Full Screen Chat for Mobile -->
    <div id="full-screen-chat">
        <div class="chat-header-fullscreen">
            <button class="back-btn" id="backToDashboardBtn"><i class="bi bi-arrow-left"></i></button>
            <span class="chat-title-fullscreen"><i class="bi bi-chat-dots-fill me-2"></i>Live Chat</span>
        </div>
        <div class="chat-body-fullscreen">
            <div class="text-center text-muted">RAIS Support, how may I help you?</div>
        </div>
        <div class="chat-footer-fullscreen">
            <div class="input-group">
                <input type="text" class="form-control message-input" placeholder="Type a message..."
                    aria-label="Message input">
                <button class="btn btn-outline-secondary" type="button" id="send-button-fullscreen">
                    <i class="bi bi-send-fill text-dark"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Chat Toggle Button -->
    <button class="chat-toggle-btn" onclick="toggleChat()">
        <i class="bi bi-chat-dots"></i>
    </button>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmNewPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="background-color: var(--rais-button-maroon); border: none;">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                    <form id="deleteAccountForm">
                        <div class="mb-3">
                            <label for="deleteConfirmPassword" class="form-label">Please enter your password to confirm:</label>
                            <input type="password" class="form-control" id="deleteConfirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-danger">Delete My Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to log out?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
      <div id="notificationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <strong class="me-auto">Notification</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody">
          <!-- Message here -->
        </div>
      </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const darkModeLabel = document.getElementById('darkModeLabel');

            function showAlert(message, type = 'success') {
                const toastEl = document.getElementById('notificationToast');
                const toastBody = document.getElementById('toastBody');
                
                const toastOptions = {
                    autohide: type === 'success',
                    delay: 2000
                };
                const toast = new bootstrap.Toast(toastEl, toastOptions);

                toastBody.textContent = message;
                
                const toastHeader = toastEl.querySelector('.toast-header');
                toastHeader.classList.remove('text-bg-success', 'text-bg-danger');

                if (type === 'success') {
                    toastHeader.classList.add('text-bg-success');
                } else {
                    toastHeader.classList.add('text-bg-danger');
                }

                toast.show();
            }
            
            // Dark Mode Toggle
            document.getElementById('darkModeSwitch').addEventListener('change', function(e) {
                const isChecked = e.target.checked;
                document.body.classList.toggle('dark-mode', isChecked);
                darkModeLabel.textContent = isChecked ? 'Turn Off Dark Mode' : 'Turn On Dark Mode';

                const formData = new FormData();
                formData.append('dark_mode', isChecked ? '1' : '0');

                fetch('update-dark-mode.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Theme updated successfully!');
                    } else {
                        showAlert(data.error || 'Failed to update theme.', 'danger');
                        document.body.classList.toggle('dark-mode', !isChecked);
                        e.target.checked = !isChecked;
                        darkModeLabel.textContent = !isChecked ? 'Turn Off Dark Mode' : 'Turn On Dark Mode';
                    }
                })
                .catch(() => {
                    showAlert('An error occurred.', 'danger');
                    document.body.classList.toggle('dark-mode', !isChecked);
                    e.target.checked = !isChecked;
                    darkModeLabel.textContent = !isChecked ? 'Turn Off Dark Mode' : 'Turn On Dark Mode';
                });
            });

            // Change Password Form Submission
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmNewPassword = document.getElementById('confirmNewPassword').value;

                if (newPassword !== confirmNewPassword) {
                    showAlert('New passwords do not match.', 'danger');
                    return;
                }

                const formData = new FormData();
                formData.append('currentPassword', currentPassword);
                formData.append('newPassword', newPassword);

                fetch('change-password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Password changed successfully!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                        modal.hide();
                        document.getElementById('changePasswordForm').reset();
                    } else {
                        showAlert(data.error || 'Failed to change password.', 'danger');
                    }
                })
                .catch(() => {
                    showAlert('An error occurred.', 'danger');
                });
            });

            // Delete Account Form Submission
            document.getElementById('deleteAccountForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // ... (rest of the delete account logic remains the same)
            });

            const mainWrapper = document.querySelector('.main-wrapper');
            const floatingBtn = document.querySelector('.floating-btn');
            const chatToggleBtn = document.querySelector('.chat-toggle-btn');
            const popupChatContainer = document.getElementById('chatContainer');
            const fullScreenChat = document.getElementById('full-screen-chat');

            window.toggleChat = function() {
                if (window.innerWidth <= 768) {
                    const isChatVisible = fullScreenChat.style.display === 'flex';
                    if (isChatVisible) {
                        fullScreenChat.style.display = 'none';
                        mainWrapper.style.display = 'flex';
                        if(floatingBtn) floatingBtn.style.display = 'flex';
                        if(chatToggleBtn) chatToggleBtn.style.display = 'flex';
                    } else {
                        fullScreenChat.style.display = 'flex';
                        mainWrapper.style.display = 'none';
                        if(floatingBtn) floatingBtn.style.display = 'none';
                        if(chatToggleBtn) chatToggleBtn.style.display = 'none';
                    }
                } else {
                    popupChatContainer.classList.toggle('show');
                }
            }
            
            if(document.getElementById('backToDashboardBtn')) {
                document.getElementById('backToDashboardBtn').addEventListener('click', toggleChat);
            }
        });
    </script>
</body>

</html>
