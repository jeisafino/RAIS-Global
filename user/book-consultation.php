<?php
// book-consultation.php - Page for users to book a consultation

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

// --- FETCH USER DATA & SETTINGS FROM DATABASE ---
$stmt = $conn->prepare("SELECT firstName, lastName, email, dark_mode FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userProfile = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If no user is found, handle it gracefully.
if (!$userProfile) {
    $userProfile = ['firstName' => 'Guest', 'lastName' => '', 'email' => '', 'dark_mode' => false];
}

$darkModeEnabled = (bool)$userProfile['dark_mode'];

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAIS Book Consultation</title>
    <link rel="icon" href="../img/logoulit.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
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
            display: flex;
            align-items: center;
            justify-content: center;
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
        }

        .booking-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--rais-card-bg);
            border-radius: 12px;
            box-shadow: var(--rais-shadow);
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.5s ease-in-out;
        }

        .booking-info {
            flex-basis: 40%;
            padding: 2.5rem;
            border-right: 1px solid var(--rais-light-gray);
        }

        .booking-info .logo {
            width: 120px;
            margin-bottom: 1.5rem;
        }

        .booking-info h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--rais-text-dark);
        }

        .booking-info .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: var(--rais-text-light);
            font-size: 1.1rem;
        }

        .booking-scheduler {
            flex-basis: 60%;
            padding: 2.5rem;
        }

        .booking-scheduler h4 {
            font-size: 1.5rem;
        }

        #monthYear {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-nav-btn {
            background: var(--rais-primary-green);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-weight: bold;
        }

        .weekdays,
        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            text-align: center;
        }

        .weekday {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .day {
            cursor: pointer;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            margin: 0 auto;
            border: 1px solid transparent;
            font-size: 1.1rem;
        }

        .day:hover:not(.empty):not(.disabled) {
            background-color: var(--rais-light-gray);
        }

        .day.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .day.today {
            border-color: var(--rais-primary-green);
        }

        .day.selected {
            background-color: var(--rais-primary-green);
            color: white;
            transform: scale(1.1);
        }

        #time-slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.5rem;
        }

        .time-slot-btn {
            border: 1px solid var(--rais-light-gray);
        }

        .time-slot-btn.active {
            background-color: var(--rais-primary-green);
            color: white;
            border-color: var(--rais-primary-green);
        }

        #details-entry-view {
            animation: fadeIn 0.5s ease-in-out;
        }

        #details-entry-view .form-label {
            font-weight: 600;
        }

        .btn-submit {
            background-color: var(--rais-button-maroon);
            color: white;
            width: 100%;
        }

        #confirmation-view {
            animation: fadeIn 0.5s ease-in-out;
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
        .dark-mode .booking-wrapper,
        .dark-mode .chat-container,
        .dark-mode .modal-content,
        .dark-mode #full-screen-chat,
        .dark-mode .chat-footer-fullscreen,
        .dark-mode .card {
            background-color: #1e1e1e;
            color: #EAEAEA;
            border-color: #2c2c2c;
        }
        .dark-mode .header {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .dark-mode .header-title,
        .dark-mode h1, .dark-mode h2, .dark-mode h4, .dark-mode h5, .dark-mode h6,
        .dark-mode .user-status .me-3,
        .dark-mode .chat-title-fullscreen,
        .dark-mode .weekday,
        .dark-mode #monthYear {
            color: #EAEAEA !important;
        }
        .dark-mode .booking-info,
        .dark-mode .time-slot-btn {
             border-color: #2c2c2c;
        }
        .dark-mode .day:hover:not(.empty):not(.disabled) {
            background-color: #2c2c2c;
        }
        .dark-mode .day.disabled {
            color: #555;
        }
        .dark-mode .form-control {
            background-color: #2a2a2a;
            color: #EAEAEA;
            border-color: #3c3c3c;
        }
        .dark-mode .form-control:focus {
            background-color: #2a2a2a;
            color: #EAEAEA;
            border-color: var(--rais-primary-green);
            box-shadow: 0 0 0 0.25rem rgba(0, 77, 64, 0.5);
        }
        .dark-mode .text-muted, .dark-mode .info-item {
            color: #B0B0B0 !important;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .booking-wrapper {
                flex-direction: column;
            }

            .booking-info {
                border-right: none;
                border-bottom: 1px solid var(--rais-light-gray);
            }
            .dark-mode .booking-info {
                border-bottom-color: #2c2c2c;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 60px;
                padding-bottom: 50px;
                overflow: auto;
            }

            .main-wrapper {
                height: auto;
            }
            
            .content-area {
                height: auto;
                overflow-y: visible;
                margin-left: 0;
            }

            .main-wrapper {
                flex-direction: column;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1030;
                height: 60px;
            }
            
            .header-title {
                display: none;
            }

            .main-content {
                padding: 15px;
            }

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
                justify-content: center;
                padding: 0 10px;
                gap: 0;
                height: 100%;
            }

            .sidebar .nav-link i {
                font-size: 1.5rem;
                margin-bottom: 0;
            }

            .sidebar .nav-link span,
            .sidebar:hover .nav-link span {
                display: none;
            }

            .day {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
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
                    <div id="headerDate" class="me-3" style="font-weight: 500;"></div>
                    <a href="#" class="btn btn-link power-btn" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-power"></i></a>
                    <span class="badge"><?php echo htmlspecialchars($userProfile['firstName']); ?></span>
                </div>
            </div>

            <!-- Main Content -->
            <main class="main-content">
                <div id="booking-view">
                    <div class="booking-wrapper">
                        <div class="booking-info">
                            <img src="../img/logoulit.png" alt="RAIS Logo" class="logo light-mode-logo">
                            <img src="../img/logowhite.png" alt="RAIS Logo Dark" class="logo dark-mode-logo" onerror="this.style.display='none'">
                            <hr>
                            <h2>45 Minute Meeting</h2>
                            <div class="info-item mt-3">
                                <i class="bi bi-clock"></i>
                                <span>45 min</span>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-camera-video"></i>
                                <span>Zoom Meeting</span>
                            </div>
                        </div>
                        <div class="booking-scheduler">
                            <div id="date-time-selection-view">
                                <h4 class="mb-3">Select a Date & Time</h4>
                                <div class="row">
                                    <div class="calendar col-lg-7">
                                        <div class="calendar-controls">
                                            <button id="prevMonth" class="calendar-nav-btn">&lt;</button>
                                            <div id="monthYear"></div>
                                            <button id="nextMonth" class="calendar-nav-btn">&gt;</button>
                                        </div>
                                        <div class="weekdays"></div>
                                        <div class="days"></div>
                                    </div>
                                    <div class="time-slots col-lg-5 mt-4 mt-lg-0">
                                        <h6 class="text-center fw-bold" id="selected-date-display"></h6>
                                        <div id="time-slots-container" class="mt-3">
                                            <!-- Time slots will be populated here -->
                                        </div>
                                    </div>
                                </div>
                                <button id="continue-to-details" class="btn btn-primary mt-4 w-100"
                                    style="background-color: var(--rais-button-maroon); border: none; display: none;">
                                    BOOK YOUR FREE CONSULTATION NOW!
                                    <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                                </button>
                            </div>

                            <div id="details-entry-view" style="display: none;">
                                <button id="back-to-calendar" class="btn btn-sm btn-link mb-3 ps-0">&lt; Back</button>
                                <h4>Enter Your Details</h4>
                                <p><strong>Selected:</strong> <span id="details-date-display"></span> at <span
                                        id="details-time-display"></span></p>
                                <form id="booking-form">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name *</label>
                                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($userProfile['firstName'] . ' ' . $userProfile['lastName']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($userProfile['email']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Additional Notes</label>
                                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <p class="small text-muted">By proceeding, you agree to our Terms of Use and Privacy Notice.</p>
                                    <button type="submit" class="btn btn-submit">Schedule Event</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="confirmation-view" style="display:none;" class="text-center mt-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h2 class="mt-3">Consultation Booked!</h2>
                    <p class="lead">Your meeting has been scheduled successfully.</p>
                    <div class="card mt-4 d-inline-block text-start">
                        <div class="card-body">
                            <h5 class="card-title">Meeting Details</h5>
                            <p><strong>With:</strong> Roman & Associates</p>
                            <p><strong>Date:</strong> <span id="confirm-date"></span></p>
                            <p><strong>Time:</strong> <span id="confirm-time"></span></p>
                            <p>A confirmation email with the Zoom link has been sent to <strong id="confirm-email"></strong>.</p>
                        </div>
                    </div>
                    <br>
                    <button id="book-another" class="btn btn-primary mt-4"
                        style="background-color: var(--rais-button-maroon); border: none;">Book Another Consultation</button>
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
            <!-- Chat messages will go here -->
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            document.getElementById('headerDate').textContent = new Date().toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            const mainWrapper = document.querySelector('.main-wrapper');
            const floatingBtn = document.querySelector('.floating-btn');
            const chatToggleBtn = document.querySelector('.chat-toggle-btn');
            const popupChatContainer = document.getElementById('chatContainer');
            const fullScreenChat = document.getElementById('full-screen-chat');

            function toggleChat() {
                if (window.innerWidth <= 768) {
                    const isChatVisible = fullScreenChat.style.display === 'flex';
                    if (isChatVisible) {
                        fullScreenChat.style.display = 'none';
                        mainWrapper.style.display = 'flex';
                        floatingBtn.style.display = 'flex';
                        chatToggleBtn.style.display = 'flex';
                    } else {
                        fullScreenChat.style.display = 'flex';
                        mainWrapper.style.display = 'none';
                        floatingBtn.style.display = 'none';
                        chatToggleBtn.style.display = 'none';
                    }
                } else {
                    popupChatContainer.classList.toggle('show');
                }
            }

            window.toggleChat = toggleChat;
            
            document.getElementById('backToDashboardBtn').addEventListener('click', toggleChat);


            let currentDate = new Date();
            let selectedDate = null;
            let selectedTime = null;

            const monthYearEl = document.getElementById('monthYear');
            const weekdaysEl = document.querySelector('.weekdays');
            const daysEl = document.querySelector('.days');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const selectedDateDisplay = document.getElementById('selected-date-display');
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const continueBtn = document.getElementById('continue-to-details');

            const bookingView = document.getElementById('booking-view');
            const detailsView = document.getElementById('details-entry-view');
            const dateTimeSelectionView = document.getElementById('date-time-selection-view');
            const backToCalendarBtn = document.getElementById('back-to-calendar');
            const bookingForm = document.getElementById('booking-form');

            const confirmationView = document.getElementById('confirmation-view');


            const timeSlots = ["9:00 AM", "10:00 AM", "11:00 AM", "1:00 PM", "2:00 PM", "3:00 PM", "4:00 PM"];

            function renderCalendar() {
                daysEl.innerHTML = '';
                weekdaysEl.innerHTML = '';

                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();

                monthYearEl.textContent = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);

                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                weekdays.forEach(day => {
                    const weekday = document.createElement('div');
                    weekday.className = 'weekday';
                    weekday.textContent = day;
                    weekdaysEl.appendChild(weekday);
                });

                for (let i = 0; i < firstDayOfMonth; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'day empty';
                    daysEl.appendChild(emptyDay);
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const dayEl = document.createElement('div');
                    dayEl.className = 'day';
                    dayEl.textContent = i;

                    const today = new Date();
                    const date = new Date(year, month, i);

                    if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                        dayEl.classList.add('disabled');
                    }

                    if (year === today.getFullYear() && month === today.getMonth() && i === today.getDate()) {
                        dayEl.classList.add('today');
                    }

                    if (selectedDate && selectedDate.getTime() === date.getTime()) {
                        dayEl.classList.add('selected');
                    }

                    dayEl.addEventListener('click', () => {
                        if (dayEl.classList.contains('disabled')) return;

                        selectedDate = new Date(year, month, i);
                        selectedTime = null;
                        renderCalendar();
                        renderTimeSlots();
                        updateSelectedDateDisplay();
                        checkContinueButton();
                    });

                    daysEl.appendChild(dayEl);
                }
            }

            function renderTimeSlots() {
                timeSlotsContainer.innerHTML = '';
                if (!selectedDate) {
                    timeSlotsContainer.innerHTML = '<p class="text-muted small">Select a date to see available times.</p>';
                    return;
                }

                timeSlots.forEach(time => {
                    const timeBtn = document.createElement('button');
                    timeBtn.className = 'btn btn-outline-secondary time-slot-btn';
                    timeBtn.textContent = time;

                    if (selectedTime === time) {
                        timeBtn.classList.add('active');
                    }

                    timeBtn.addEventListener('click', () => {
                        selectedTime = time;
                        renderTimeSlots();
                        checkContinueButton();
                    });
                    timeSlotsContainer.appendChild(timeBtn);
                });
            }

            function updateSelectedDateDisplay() {
                if (selectedDate) {
                    selectedDateDisplay.textContent = new Intl.DateTimeFormat('en-US', { weekday: 'long', month: 'long', day: 'numeric' }).format(selectedDate);
                } else {
                    selectedDateDisplay.textContent = 'Select a Date';
                }
            }

            function checkContinueButton() {
                if (selectedDate && selectedTime) {
                    continueBtn.style.display = 'block';
                } else {
                    continueBtn.style.display = 'none';
                }
            }

            prevMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });

            continueBtn.addEventListener('click', () => {
                dateTimeSelectionView.style.display = 'none';
                detailsView.style.display = 'block';
                document.getElementById('details-date-display').textContent = new Intl.DateTimeFormat('en-US', { weekday: 'long', month: 'long', day: 'numeric' }).format(selectedDate);
                document.getElementById('details-time-display').textContent = selectedTime;
            });

            backToCalendarBtn.addEventListener('click', () => {
                detailsView.style.display = 'none';
                dateTimeSelectionView.style.display = 'block';
            });

            bookingForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const email = document.getElementById('email').value;

                document.getElementById('confirm-date').textContent = new Intl.DateTimeFormat('en-US', { weekday: 'long', month: 'long', day: 'numeric' }).format(selectedDate);
                document.getElementById('confirm-time').textContent = selectedTime;
                document.getElementById('confirm-email').textContent = email;

                bookingView.style.display = 'none';
                confirmationView.style.display = 'block';
            });

            document.getElementById('book-another').addEventListener('click', () => {
                confirmationView.style.display = 'none';
                bookingView.style.display = 'block';
                detailsView.style.display = 'none';
                dateTimeSelectionView.style.display = 'block';

                // Reset state
                selectedDate = null;
                selectedTime = null;
                bookingForm.reset();
                renderCalendar();
                renderTimeSlots();
                updateSelectedDateDisplay();
                checkContinueButton();
            });

            renderCalendar();
            renderTimeSlots();
            updateSelectedDateDisplay();
        });
    </script>
</body>

</html>
