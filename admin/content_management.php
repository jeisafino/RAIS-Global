<?php
// Page-specific data
$page_title = "RAIS Admin - Content Management";
$active_page = "content_management";

// In a real application, all this data would come from your database.
// We start with empty arrays for sections that are managed dynamically via localStorage.
$landingMediaData = [];
$aboutData = null;
$servicesData = [];
$blogsData = [];
$partnersData = [];

$footerData = [
    ["id" => 1, "label" => "Email", "description" => "contact@rais.com", "type" => "static"],
    ["id" => 2, "label" => "Contacts", "description" => "+1 (123) 456-7890", "type" => "static"],
    ["id" => 3, "label" => "Location", "description" => "123 Immigration Ave, Suite 100, Capital City", "type" => "static"]
];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="../img/logoulit.png" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Custom styles for CMS enhancements -->
    <style>
        #about-edit-nav .nav-link.active {
            background-color: #800000; /* Maroon color */
            color: #ffffff;
            border-color: #800000;
        }
        #about-edit-nav .nav-link { color: #333; }
        .service-card.selected, .blog-card.selected {
            box-shadow: 0 0 0 3px #800000;
            border-color: #800000;
        }
        .blog-section-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-left: 5px solid #1E4620; /* Dark green accent */
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-maroon {
            background-color:( #800000);
            border-color: #800000;
            color: #ffffff;
        }
        .btn-maroon:hover {
            background-color: #600000; /* A slightly darker shade for hover */
            border-color: #500000;
            color: #ffffff;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #800000; /* Maroon color */
            color: #ffffff;
        }

        /* Add this rule for the dark mode fix */
        body.dark-mode #about-edit-nav .nav-link {
            color: #f8f9fa; /* A light color for dark mode */
        }

        #about-edit-nav .nav-link.active {
            background-color: #800000; /* Maroon color */
            color: #ffffff;
            border-color: #800000;
        }
    </style>

</head>

<body>
    <div class="main-wrapper">
        <?php require_once 'sidebar.php'; ?>

        <div class="content-area">
            <?php require_once 'header.php'; ?>

            <main class="main-content">
                <h1>Content Management</h1>
                <div class="content-card">
                    <!-- Navigations -->
                    <div class="dropdown d-sm-none mb-3 content-nav">
                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="contentNavDropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu w-100" aria-labelledby="contentNavDropdown">
                            <li><a class="dropdown-item nav-link active" href="#" data-target="landing-page">Landing Page</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="about">About</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="services">Services</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="blogs">Blogs</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="partners">Partners</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="exams">Exams</a></li>
                            <li><a class="dropdown-item nav-link" href="#" data-target="footer">Footer</a></li>
                        </ul>
                    </div>
                    <nav class="nav nav-pills flex-sm-row content-nav d-none d-sm-flex">
                        <a class="flex-sm-fill text-sm-center nav-link active" href="#" data-target="landing-page">Landing Page</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="about">About</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="services">Services</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="blogs">Blogs</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="partners">Partners</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="exams">Exams</a>
                        <a class="flex-sm-fill text-sm-center nav-link" href="#" data-target="footer">Footer</a>
                    </nav>

                    <div id="content-sections">
                        <!-- Landing Page Section -->
                        <div id="landing-page" class="content-section active">
                            <div class="d-flex justify-content-between align-items-center mb-3"><h3>Edit Landing Page Hero Section</h3></div>
                            <p>Click a row in the table to select an item, then use the buttons at the bottom-right to manage it.</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead><tr><th scope="col">Media Name</th><th scope="col">Uploader</th><th scope="col">Date Uploaded</th><th scope="col">Media File Name</th></tr></thead>
                                    <tbody id="media-table-body"></tbody>
                                </table>
                            </div>
                            <div class="fab-container">
                                <button id="preview-landing-btn" class="btn btn-info btn-lg rounded-circle" title="Preview Media" disabled><i class="bi bi-eye-fill"></i></button>
                                <button id="edit-landing-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Media" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-landing-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Media" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-landing-btn" class="btn btn-primary btn-lg rounded-circle" title="Add New Media"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <!-- About Section -->
                        <div id="about" class="content-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3>Edit About Page</h3>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-info" id="preview-about-page-btn"><i class="bi bi-eye-fill me-2"></i>Preview</button>
                                    <button class="btn btn-outline-secondary" id="cancel-about-changes-btn"><i class="bi bi-arrow-counterclockwise me-2"></i>Cancel</button>
                                    <button class="btn btn-success" id="save-all-about-changes-btn"><i class="bi bi-check-lg me-2"></i>Save All Changes</button>
                                </div>
                            </div>
                            <p class="text-muted small">Click the categories to manage the about section contents.</p>
                            <nav class="nav nav-tabs mb-3" id="about-edit-nav">
                                <a class="nav-link active" href="#" data-target="about-main-section">Main Section</a>
                                <a class="nav-link" href="#" data-target="about-content-blocks">Content Paragraphs</a>
                                <a class="nav-link" href="#" data-target="about-cards-section">Mission/Vision Cards</a>
                            </nav>
                            <div id="about-main-section" class="about-edit-pane active"><div class="card"><div class="card-body"><h5 class="card-title">Main Media, Title, and Description</h5><form id="about-main-form"><div class="mb-3"><label for="about-hero-file" class="form-label">Hero Media (Photo or Video)</label><div class="input-group"><input type="file" class="form-control" id="about-hero-file" accept="image/*,video/*"><button class="btn btn-outline-danger" type="button" id="clear-hero-media-btn" title="Clear Media"><i class="bi bi-x-lg"></i></button></div><div id="about-hero-preview" class="mt-2 border rounded p-2" style="min-height: 100px;"></div></div><div class="mb-3"><label for="about-hero-title" class="form-label">Main Title</label><input type="text" class="form-control" id="about-hero-title"></div><div class="mb-3"><label for="about-hero-description" class="form-label">Main Description</label><textarea class="form-control" id="about-hero-description" rows="4"></textarea></div></form></div></div></div>
                            <div id="about-content-blocks" class="about-edit-pane" style="display: none;"><div class="card"><div class="card-body"><h5 class="card-title">Content Paragraphs & Media</h5><p class="card-text text-muted">Add, edit, or remove the paragraphs and media blocks that appear below the main section.</p><div id="about-content-blocks-container"></div><hr><div class="d-flex gap-2"><button class="btn btn-outline-primary" id="add-text-block-btn"><i class="bi bi-body-text me-2"></i>Add Text Paragraph</button><button class="btn btn-outline-secondary" id="add-media-block-btn"><i class="bi bi-image me-2"></i>Add Media Block</button></div></div></div></div>
                            <div id="about-cards-section" class="about-edit-pane" style="display: none;"><div class="card"><div class="card-body"><h5 class="card-title">Mission, Vision, and Objective Cards</h5><p class="card-text text-muted">These items will appear in the tabbed interface at the bottom of the page.</p><div id="about-cards-container"></div><hr><button class="btn btn-outline-primary" id="add-about-card-btn"><i class="bi bi-plus-square me-2"></i>Add New Card</button></div></div></div>
                        </div>

                        <!-- Services Section -->
                        <div id="services" class="content-section">
                            <h3>Manage Services</h3>
                            <p>Click a card to select a service, then use the buttons at the bottom-right to edit, preview, or delete it.</p>
                            <div id="service-cards-container" class="row g-4"></div>
                            <div class="fab-container">
                                <button id="preview-service-btn" class="btn btn-info btn-lg rounded-circle" title="Preview Service" disabled><i class="bi bi-eye-fill"></i></button>
                                <button id="edit-service-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Service" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-service-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Service" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-service-btn" class="btn btn-primary btn-lg rounded-circle" title="Add New Service"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <!-- Blogs Section -->
                        <div id="blogs" class="content-section">
                            <h3>Manage Blogs</h3>
                            <p>Click a card to select a blog post, then use the buttons at the bottom-right to edit, preview, or delete it.</p>
                            <div id="blog-cards-container" class="row g-4"></div>
                            <div class="fab-container">
                                <button id="preview-blog-btn" class="btn btn-info btn-lg rounded-circle" title="Preview Blog" disabled><i class="bi bi-eye-fill"></i></button>
                                <button id="edit-blog-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Blog" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-blog-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Blog" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-blog-btn" class="btn btn-primary btn-lg rounded-circle" title="Add New Blog"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <!-- Partners Section -->
                        <div id="partners" class="content-section">
                            <h3>Manage Partners</h3>
                            <p>Click a row to select a partner, then use the buttons at the bottom-right to manage their information.</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead><tr><th scope="col">Partner Name</th><th scope="col">Website Link</th></tr></thead>
                                    <tbody id="partners-table-body"></tbody>
                                </table>
                            </div>
                            <div class="fab-container">
                                <button id="visit-partner-btn" class="btn btn-info btn-lg rounded-circle" title="Visit Link" disabled><i class="bi bi-box-arrow-up-right"></i></button>
                                <button id="edit-partner-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Partner" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-partner-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Partner" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-partner-btn" class="btn btn-primary btn-lg rounded-circle" title="Add New Partner"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <div id="exams" class="content-section">
                            <h3>Manage Exams</h3>
                            <p>Click a card to select an exam, then use the buttons at the bottom-right to edit, preview, or delete it.</p>
                            <div id="exam-cards-container" class="row g-4">
                                </div>
                            <div class="fab-container">
                                <button id="preview-exam-btn" class="btn btn-info btn-lg rounded-circle" title="Preview Exam" disabled><i class="bi bi-eye-fill"></i></button>
                                <button id="edit-exam-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Exam" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-exam-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Exam" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-exam-btn" class="btn btn-primary btn-lg rounded-circle" title="Add New Exam"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div id="footer" class="content-section">
                            <h3>Edit Footer</h3>
                            <p>Select an item to manage. Static items (Email, Contacts, Location) can only be edited. Social media links can be added, edited, or deleted.</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead><tr><th scope="col">Label</th><th scope="col">Description / Link</th><th scope="col">Type</th></tr></thead>
                                    <tbody id="footer-table-body"></tbody>
                                </table>
                            </div>
                            <div class="fab-container">
                                <button id="visit-footer-link-btn" class="btn btn-info btn-lg rounded-circle" title="Visit Link" disabled><i class="bi bi-box-arrow-up-right"></i></button>
                                <button id="edit-footer-item-btn" class="btn btn-warning btn-lg rounded-circle" title="Edit Item" disabled><i class="bi bi-pencil-fill"></i></button>
                                <button id="delete-footer-item-btn" class="btn btn-danger btn-lg rounded-circle" title="Delete Item" disabled><i class="bi bi-trash-fill"></i></button>
                                <button id="add-footer-item-btn" class="btn btn-primary btn-lg rounded-circle" title="Add Social Media"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ALL MODALS AND TEMPLATES -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="uploadMediaModalLabel">Add New Hero Media</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="upload-form" novalidate><div class="mb-3"><label for="mediaName" class="form-label">Media Name</label><input type="text" class="form-control" id="mediaName" name="mediaName" required></div><div class="mb-3"><label for="uploaderName" class="form-label">Uploader</label><input type="text" class="form-control" id="uploaderName" name="uploaderName" required></div><div class="mb-3"><label for="mediaFile" class="form-label">Upload Photo or Video</label><input class="form-control" type="file" id="mediaFile" name="mediaFile" accept="image/*,video/*"><div class="form-text">Please select a landscape photo for best results.</div></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-media-btn">Save Media</button></div></div></div></div>
    <div class="modal fade" id="landscapeWarningModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Image Orientation Warning</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>The chosen photo is not a landscape photo. If you wish to continue, the photo may be stretched or cropped.</p></div><div class="modal-footer"> <button type="button" class="btn btn-secondary" id="repick-photo-btn">Repick Photo</button> <button type="button" class="btn btn-primary" id="continue-anyway-btn">Continue Anyway</button></div></div></div></div>
    <div class="modal fade" id="landing-confirmation-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="landing-confirmation-title">Confirm Action</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="landing-confirmation-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="confirm-landing-action-btn">Confirm</button></div></div></div></div>
    <div class="modal fade" id="landing-preview-modal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="landing-preview-title">Media Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="landing-preview-body" class="text-center"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>
    <div class="modal fade" id="about-page-preview-modal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">About Page Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="about-preview-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button></div></div></div></div>
    <div class="modal fade" id="service-modal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="service-modal-title">Add New Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="service-form" novalidate><h6>Service Information</h6><div class="mb-3"><label for="service-name" class="form-label">Service Name</label><input type="text" class="form-control" id="service-name" required></div><div class="mb-3"><label for="service-description" class="form-label">Subtitle (in hero)</label><textarea class="form-control" id="service-description" rows="3" required></textarea></div><div class="mb-3"><label for="service-hero-media" class="form-label">Hero Section Media</label><input type="file" class="form-control" id="service-hero-media" accept="image/*,video/*"></div><hr class="my-4"><h6>Service Sections (Tabs)</h6><p class="text-muted small">Add detailed sections for this service. Each section will become a tab in the preview.</p><div id="dynamic-sections-container"></div><button type="button" id="add-section-btn" class="btn btn-outline-primary mt-2"><i class="bi bi-plus-circle me-2"></i>Add Section</button></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-service-btn">Save Service</button></div></div></div></div><div class="modal-body"><form id="service-form" novalidate><h6>Service Information</h6><div class="mb-3"><label for="service-name" class="form-label">Service Name</label><input type="text" class="form-control" id="service-name" required></div><div class="mb-3"><label for="service-description" class="form-label">Subtitle (in hero)</label><textarea class="form-control" id="service-description" rows="3" required></textarea></div><div class="mb-3"><label for="service-hero-media" class="form-label">Hero Section Media</label><input type="file" class="form-control" id="service-hero-media" accept="image/*,video/*"></div><hr class="my-4"><h6>Service Sections (Tabs)</h6><p class="text-muted small">Add detailed sections for this service. Each section will become a tab in the preview.</p><div id="dynamic-sections-container"></div><button type="button" id="add-section-btn" class="btn btn-outline-primary mt-2"><i class="bi bi-plus-circle me-2"></i>Add Section</button></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-service-btn">Save Service</button></div></div></div></div>    <template id="service-section-template"><div class="p-3 border rounded mb-3 dynamic-section"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="section-number mb-0">Section 1</h6><button type="button" class="btn-close remove-section-btn"></button></div><div class="mb-2"><label class="form-label">Tab Title</label><input type="text" class="form-control section-title" placeholder="e.g., About" required></div><div class="mb-2"><label class="form-label">Tab Content</label><textarea class="form-control section-description" rows="5" required></textarea></div><div><label class="form-label">Tab Media (Optional)</label><input type="file" class="form-control section-media" accept="image/*,video/*"></div></div></template>
    <div class="modal fade" id="service-preview-modal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="service-preview-title">Service Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="service-preview-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>
    <div class="modal fade" id="blog-modal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="blog-modal-title">Add New Blog Post</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="blog-form" novalidate><h6>Blog Information</h6><div class="row"><div class="col-md-8"><div class="mb-3"><label for="blog-title" class="form-label">Blog Title</label><input type="text" class="form-control" id="blog-title" required></div></div><div class="col-md-4"><div class="mb-3"><label for="blog-publish-date" class="form-label">Publish Date</label><input type="date" class="form-control" id="blog-publish-date" required></div></div></div><div class="mb-3"><label for="blog-author" class="form-label">Author Name</label><input type="text" class="form-control" id="blog-author" required></div><div class="mb-3"><label for="blog-summary" class="form-label">Summary / Subtitle (for blog list page)</label><textarea class="form-control" id="blog-summary" rows="2" required></textarea></div><div class="mb-3"><label for="blog-hero-media" class="form-label">Hero Section Media (Main Image)</label><input type="file" class="form-control" id="blog-hero-media" accept="image/*,video/*"><div class="form-text">This image will appear below the title.</div></div><hr class="my-4"><h6>Blog Content</h6><p class="text-muted small">The first section will be the full-width introductory paragraph. Subsequent sections will alternate layout.</p><div id="blog-dynamic-sections-container"></div><button type="button" id="add-blog-section-btn" class="btn btn-outline-primary mt-2"><i class="bi bi-plus-circle me-2"></i>Add Section</button></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-blog-btn">Add Blog Post</button></div></div></div></div><template id="blog-section-template"><div class="p-3 border rounded mb-3 dynamic-section"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="section-number mb-0">Section 1</h6><button type="button" class="btn-close remove-section-btn"></button></div><div class="mb-2"><label class="form-label">Section Title (Optional)</label><input type="text" class="form-control section-title"></div><div class="mb-2"><label class="form-label">Section Content / Paragraph</label><textarea class="form-control section-description" rows="5" required></textarea></div><div><label class="form-label">Section Media (Optional)</label><input type="file" class="form-control section-media" accept="image/*,video/*"></div></div></template>
    <div class="modal fade" id="blog-preview-modal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="blog-preview-title">Blog Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="blog-preview-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>
    <div class="modal fade" id="partner-modal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="partner-modal-title">Add New Partner</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="partner-form" novalidate><div class="mb-3"><label for="partner-name" class="form-label">Partner Name</label><input type="text" class="form-control" id="partner-name" required></div><div class="mb-3"><label for="partner-link" class="form-label">Website Link</label><input type="url" class="form-control" id="partner-link" placeholder="https://example.com" required></div><div class="mb-3"><label for="partner-logo" class="form-label">Partner Logo</label><input class="form-control" type="file" id="partner-logo" accept="image/*"><div class="form-text">Upload a logo for the partner.</div></div><div class="text-center"><img src="https://via.placeholder.com/100x100.png?text=Logo" alt="Logo Preview" id="partner-logo-preview" class="mt-2 bg-light p-1"></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-partner-btn">Add Partner</button></div></div></div></div>
    <div class="modal fade" id="exam-modal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exam-modal-title">Add New Exam</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="exam-form" novalidate><nav class="nav nav-tabs mb-3"><a class="nav-link active" href="#" data-pane="exam-pane-info">Exam Info</a><a class="nav-link" href="#" data-pane="exam-pane-about">About</a><a class="nav-link" href="#" data-pane="exam-pane-format">Test Format</a><a class="nav-link" href="#" data-pane="exam-pane-cards">Info Cards</a><a class="nav-link" href="#" data-pane="exam-pane-faqs">FAQs</a></nav><div id="exam-pane-info" class="exam-pane active"><div class="p-3 border rounded"><h6><i class="bi bi-card-heading me-2"></i>Exam Info & Hero Section</h6><div class="mb-3"><label for="exam-name" class="form-label">Exam Name (e.g., IELTS)</label><input type="text" class="form-control" id="exam-name" required></div><div class="mb-3"><label for="exam-hero-media" class="form-label">Hero Section Media (Image or Video)</label><input type="file" class="form-control" id="exam-hero-media" accept="image/*,video/*"></div></div></div><div id="exam-pane-about" class="exam-pane" style="display: none;"><div class="p-3 border rounded"><h6><i class="bi bi-info-circle me-2"></i>About Section</h6><div class="mb-3"><label for="exam-about-content" class="form-label">About Content</label><textarea class="form-control" id="exam-about-content" rows="4"></textarea></div><div class="mb-3"><label for="exam-about-media" class="form-label">Optional Media for About Section</label><input type="file" class="form-control" id="exam-about-media" accept="image/*,video/*"></div></div></div><div id="exam-pane-format" class="exam-pane" style="display: none;"><div class="p-3 border rounded"><h6><i class="bi bi-card-checklist me-2"></i>Test Format</h6><p class="text-muted small">Add items that appear in the test format section.</p><div id="exam-format-container"></div><button type="button" id="add-exam-format-btn" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus-circle me-2"></i>Add Format Item</button></div></div><div id="exam-pane-cards" class="exam-pane" style="display: none;"><div class="p-3 border rounded"><h6><i class="bi bi-grid-1x2 me-2"></i>Informational Cards</h6><p class="text-muted small">Add cards for sections like "How does it work?" or "Why choose us?".</p><div id="exam-infocards-container"></div><button type="button" id="add-exam-infocard-btn" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus-circle me-2"></i>Add Card</button></div></div><div id="exam-pane-faqs" class="exam-pane" style="display: none;"><div class="p-3 border rounded"><h6><i class="bi bi-patch-question me-2"></i>Frequently Asked Questions</h6><p class="text-muted small">Add question and answer pairs.</p><div id="exam-faq-container"></div><button type="button" id="add-exam-faq-btn" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus-circle me-2"></i>Add FAQ</button></div></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-exam-btn">Save Exam</button></div></div></div></div><div class="modal fade" id="exam-preview-modal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exam-preview-title">Exam Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="exam-preview-body" style="background-color: #f8f9fa;"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div><template id="exam-format-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">Format Item</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Icon Class (e.g., 'bi bi-headphones')</label><input type="text" class="form-control form-control-sm format-icon"></div><div class="mb-2"><label class="form-label small">Title (e.g., 'Listening')</label><input type="text" class="form-control form-control-sm format-title" required></div><div><label class="form-label small">Description (e.g., '30 minutes...')</label><textarea class="form-control form-control-sm format-description" rows="2"></textarea></div></div></template><template id="exam-infocard-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">Info Card</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Card Title</label><input type="text" class="form-control form-control-sm infocard-title" required></div><div><label class="form-label small">Card Description</label><textarea class="form-control form-control-sm infocard-description" rows="3" required></textarea></div></div></template><template id="exam-faq-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">FAQ Item</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Question</label><input type="text" class="form-control form-control-sm faq-question" required></div><div><label class="form-label small">Answer</label><textarea class="form-control form-control-sm faq-answer" rows="3" required></textarea></div></div></template>    <div class="modal fade" id="exam-preview-modal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exam-preview-title">Exam Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="exam-preview-body" style="background-color: #f8f9fa;"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div></div>
    <div class="modal fade" id="footer-modal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="footer-modal-title">Add Social Media Link</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="footer-form" novalidate><div class="mb-3"><label for="footer-label" class="form-label">Label</label><input type="text" class="form-control" id="footer-label" placeholder="e.g., Facebook" required></div><div class="mb-3"><label for="footer-description" class="form-label">Description / Link</label><input type="text" class="form-control" id="footer-description" placeholder="e.g., https://facebook.com/your-page" required></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="save-footer-item-btn">Save Item</button></div></div></div></div>
    
    <template id="about-text-block-template"><div class="p-3 border rounded mb-3 dynamic-about-block" data-type="text"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0 text-muted">Text Paragraph</h6><button type="button" class="btn-close remove-about-block-btn"></button></div><textarea class="form-control block-content" rows="5" placeholder="Enter paragraph text here..."></textarea></div></template>
    <template id="about-media-block-template"><div class="p-3 border rounded mb-3 dynamic-about-block" data-type="media"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0 text-muted">Media Block</h6><button type="button" class="btn-close remove-about-block-btn"></button></div><div class="input-group"><input type="file" class="form-control block-media-file" accept="image/*,video/*"><button class="btn btn-outline-danger btn-sm clear-block-media-btn" type="button" title="Clear Media"><i class="bi bi-x-lg"></i></button></div><div class="mt-2 border rounded p-2 block-media-preview" style="min-height: 100px;"></div></div></template>
    <template id="about-card-template"><div class="p-3 border rounded mb-3 dynamic-about-card"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0 text-muted">Tabbed Card</h6><button type="button" class="btn-close remove-about-card-btn"></button></div><div class="row"><div class="col-md-6 mb-2"><label class="form-label small">Tab Title</label><input type="text" class="form-control card-tab-title" placeholder="e.g., Mission"></div><div class="col-md-6 mb-2"><label class="form-label small">Card Title</label><input type="text" class="form-control card-title" placeholder="e.g., Mission Statement"></div></div><div class="mb-2"><label class="form-label small">Card Content</label><textarea class="form-control card-content" rows="4"></textarea></div></div></template>
    <template id="exam-format-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">Format Item</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Icon Class (e.g., 'bi bi-headphones')</label><input type="text" class="form-control form-control-sm format-icon"></div><div class="mb-2"><label class="form-label small">Title (e.g., 'Listening')</label><input type="text" class="form-control form-control-sm format-title" required></div><div><label class="form-label small">Description (e.g., '30 minutes...')</label><textarea class="form-control form-control-sm format-description" rows="2"></textarea></div></div></template>
    <template id="exam-infocard-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">Info Card</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Card Title</label><input type="text" class="form-control form-control-sm infocard-title" required></div><div><label class="form-label small">Card Description</label><textarea class="form-control form-control-sm infocard-description" rows="3" required></textarea></div></div></template>
    <template id="exam-faq-template"><div class="p-3 border rounded mb-3 dynamic-exam-item"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">FAQ Item</h6><button type="button" class="btn-close remove-exam-item-btn"></button></div><div class="mb-2"><label class="form-label small">Question</label><input type="text" class="form-control form-control-sm faq-question" required></div><div><label class="form-label small">Answer</label><textarea class="form-control form-control-sm faq-answer" rows="3" required></textarea></div></div></template>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="togglemodeScript.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // --- DATA INJECTION FROM PHP (used only for initial non-localStorage data) ---
    let initialBlogsData = <?php echo json_encode($blogsData, JSON_PRETTY_PRINT); ?>;
    let initialPartnersData = <?php echo json_encode($partnersData, JSON_PRETTY_PRINT); ?>;
    let footerData = <?php echo json_encode($footerData, JSON_PRETTY_PRINT); ?>;

    // --- MAIN NAVIGATION LOGIC ---
    const navLinks = document.querySelectorAll('.content-nav .nav-link');
    const contentSections = document.querySelectorAll('.content-section');
    const dropdownButton = document.getElementById('contentNavDropdown');
    function setActiveNav(targetId) { const activeLink = document.querySelector(`.content-nav .nav-link[data-target="${targetId}"]`); if (!activeLink) return; if (dropdownButton) { dropdownButton.textContent = activeLink.textContent; } navLinks.forEach(link => { link.classList.toggle('active', link.getAttribute('data-target') === targetId); }); contentSections.forEach(section => { section.classList.toggle('active', section.id === targetId); }); }
    navLinks.forEach(link => { link.addEventListener('click', function(e) { e.preventDefault(); const targetId = this.getAttribute('data-target'); setActiveNav(targetId); }); });
    (function handleDeepLink() { const hash = window.location.hash; const targetId = hash ? hash.substring(1) : null; const initialActiveLink = document.querySelector('.content-nav .nav-link.active'); if (targetId) { setActiveNav(targetId); } else if (initialActiveLink) { setActiveNav(initialActiveLink.getAttribute('data-target')); } })();

    const confirmationModalEl = document.getElementById('landing-confirmation-modal');
    const confirmationModal = new bootstrap.Modal(confirmationModalEl);
    const confirmationModalBody = document.getElementById('landing-confirmation-body');
    const confirmActionBtn = document.getElementById('confirm-landing-action-btn');
    const confirmationModalTitle = document.getElementById('landing-confirmation-title');

    const readFileAsDataURL = (file) => new Promise((resolve, reject) => { const reader = new FileReader(); reader.onload = () => resolve(reader.result); reader.onerror = reject; reader.readAsDataURL(file); });

    // --- START: SCRIPT FOR LANDING PAGE MANAGEMENT (DATABASE-DRIVEN) ---
(function() {
    let landingMediaData = [];
    let selectedMediaId = null;

    const mediaTableBody = document.getElementById('media-table-body');
    const addLandingBtn = document.getElementById('add-landing-btn');
    const editLandingBtn = document.getElementById('edit-landing-btn');
    const deleteLandingBtn = document.getElementById('delete-landing-btn');
    const previewLandingBtn = document.getElementById('preview-landing-btn');
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadMediaModal'));
    const previewModal = new bootstrap.Modal(document.getElementById('landing-preview-modal'));
    const uploadForm = document.getElementById('upload-form');
    const saveMediaBtn = document.getElementById('save-media-btn');
    
    // Helper function to build the correct API URL
    const getApiPath = (file) => `../api/${file}`;

    // --- Data Fetching and Rendering ---
    async function fetchAndRenderTable() {
        try {
            // CORRECTED FETCH PATH
            const response = await fetch(getApiPath('hero_media_handler.php'));
            const result = await response.json();
            if (result.status === 'success') {
                landingMediaData = result.data;
                renderTable();
            } else {
                console.error('Error fetching media:', result.message);
            }
        } catch (error) {
            console.error('Error fetching media:', error);
        }
    }

    function renderTable() {
        mediaTableBody.innerHTML = '';
        landingMediaData.forEach(media => {
            const row = mediaTableBody.insertRow();
            row.dataset.id = media.id;
            row.className = media.is_active == 1 ? 'table-success' : ''; // Highlight active row
            if (media.id === selectedMediaId) row.classList.add('selected');

            const fileName = media.file_path.split('/').pop();

            row.innerHTML = `
                <td>${media.media_name} ${media.is_active == 1 ? '<span class="badge bg-success ms-2">Active</span>' : ''}</td>
                <td>${media.uploader}</td>
                <td>${media.date}</td>
                <td>${fileName}</td>
                <td>
                    <button class="btn btn-sm ${media.is_active == 1 ? 'btn-secondary' : 'btn-outline-success'} activate-btn" data-id="${media.id}" ${media.is_active == 1 ? 'disabled' : ''}>
                        <i class="bi bi-check-circle-fill"></i> Set Active
                    </button>
                </td>
            `;
        });
    }

    function updateFabState() {
        const isSelected = selectedMediaId !== null;
        editLandingBtn.disabled = !isSelected;
        deleteLandingBtn.disabled = !isSelected;
        previewLandingBtn.disabled = !isSelected;
    }
    
    // --- Event Handlers ---
    mediaTableBody.addEventListener('click', e => {
        const row = e.target.closest('tr');
        if (e.target.closest('.activate-btn')) {
            const mediaId = e.target.closest('.activate-btn').dataset.id;
            setActiveMedia(mediaId);
        } else if (row) {
            const mediaId = parseInt(row.dataset.id);
            selectedMediaId = (selectedMediaId === mediaId) ? null : mediaId;
            renderTable();
            updateFabState();
        }
    });

    addLandingBtn.addEventListener('click', () => {
        resetAndPrepareModal('add');
        uploadModal.show();
    });

    editLandingBtn.addEventListener('click', () => {
        if (selectedMediaId === null) return;
        const media = landingMediaData.find(m => m.id === selectedMediaId);
        resetAndPrepareModal('edit', media);
        uploadModal.show();
    });

    deleteLandingBtn.addEventListener('click', () => {
        if (selectedMediaId === null) return;
        const media = landingMediaData.find(m => m.id === selectedMediaId);
        confirmationModalTitle.textContent = "Confirm Deletion";
        confirmationModalBody.innerHTML = `Are you sure you want to delete the media: <strong>${media.media_name}</strong>? This will also remove the file from the server.`;
        confirmActionBtn.className = 'btn btn-danger';
        confirmActionBtn.onclick = () => deleteMedia(selectedMediaId);
        confirmationModal.show();
    });

    previewLandingBtn.addEventListener('click', () => {
        if (selectedMediaId === null) return;
        const media = landingMediaData.find(m => m.id === selectedMediaId);
        document.getElementById('landing-preview-title').textContent = `Preview: ${media.media_name}`;
        const previewBody = document.getElementById('landing-preview-body');
        
        const previewPath = `../${media.file_path}`;

        const isVideo = media.file_path.match(/\.(mp4|webm|ogg)$/i);
        if (isVideo) {
            previewBody.innerHTML = `<video src="${previewPath}" class="img-fluid rounded" controls autoplay muted loop></video>`;
        } else {
            previewBody.innerHTML = `<img src="${previewPath}" class="img-fluid rounded" alt="Preview">`;
        }
        previewModal.show();
    });

    saveMediaBtn.addEventListener('click', async () => {
        if (!uploadForm.checkValidity()) {
            uploadForm.reportValidity();
            return;
        }

        const formData = new FormData(uploadForm);
        const mode = saveMediaBtn.dataset.mode;
        formData.append('action', mode);

        if (mode === 'edit') {
            formData.append('mediaId', selectedMediaId);
        }

        try {
            // CORRECTED FETCH PATH
            const response = await fetch(getApiPath('hero_media_handler.php'), {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                uploadModal.hide();
                fetchAndRenderTable();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('An error occurred: ' + error.message);
        }
    });

    // --- Helper and Action Functions ---
    async function deleteMedia(mediaId) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', mediaId);

        // CORRECTED FETCH PATH
        const response = await fetch(getApiPath('hero_media_handler.php'), { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === 'success') {
            selectedMediaId = null;
            fetchAndRenderTable();
            updateFabState();
            confirmationModal.hide();
        } else {
            alert('Error deleting media: ' + result.message);
        }
    }

    async function setActiveMedia(mediaId) {
        const formData = new FormData();
        formData.append('action', 'set_active');
        formData.append('id', mediaId);

        // CORRECTED FETCH PATH
        const response = await fetch(getApiPath('hero_media_handler.php'), { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === 'success') {
            fetchAndRenderTable();
        } else {
             alert('Error setting active media: ' + result.message);
        }
    }

    function resetAndPrepareModal(mode = 'add', media = null) {
        uploadForm.reset();
        uploadForm.classList.remove('was-validated');
        const modalTitle = document.getElementById('uploadMediaModalLabel');
        const mediaFileInput = document.getElementById('mediaFile');

        if (mode === 'add') {
            modalTitle.textContent = 'Add New Hero Media';
            saveMediaBtn.textContent = 'Add Media';
            saveMediaBtn.dataset.mode = 'add';
            mediaFileInput.required = true;
        } else if (mode === 'edit' && media) {
            modalTitle.textContent = `Edit Media: ${media.media_name}`;
            saveMediaBtn.textContent = 'Update Media';
            saveMediaBtn.dataset.mode = 'edit';
            document.getElementById('mediaName').value = media.media_name;
            document.getElementById('uploaderName').value = media.uploader;
            mediaFileInput.required = false; // Not required to upload a new file on edit
        }
    }

    // Initial Load
    fetchAndRenderTable();
})();
    
    // --- START: SCRIPT FOR ABOUT PAGE MANAGEMENT ---
(function() {
    // State variables to track user actions reliably
    let heroMediaWasCleared = false;
    let blockMediaState = {}; 

    const previewModal = new bootstrap.Modal(document.getElementById('about-page-preview-modal'));
    const previewModalBody = document.getElementById('about-preview-body');
    const previewBtn = document.getElementById('preview-about-page-btn');
    const cancelBtn = document.getElementById('cancel-about-changes-btn');
    const saveBtn = document.getElementById('save-all-about-changes-btn');
    const getApiPath = (file) => `../api/${file}`;
    let aboutPageData = {};

    async function loadAboutPageData() {
        try {
            const response = await fetch(getApiPath('about_handler.php'));
            const result = await response.json();
            if (result.status === 'success') {
                aboutPageData = result.data;
                // Reset flags every time data is loaded from the server
                heroMediaWasCleared = false;
                blockMediaState = {};
                renderAll();
            } else {
                console.error('Failed to load about page data:', result.message);
            }
        } catch (error) {
            console.error('Error fetching about page data:', error);
        }
    }

    async function saveAboutPageData() {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        const formData = new FormData();

        // **THE FIX**: The logic now ONLY uses the heroMediaWasCleared flag.
        // It no longer tries to guess based on the hidden DOM element.
        const dataToSave = {
            hero: {
                title: document.getElementById('about-hero-title').value,
                description: document.getElementById('about-hero-description').value,
                clear_media: heroMediaWasCleared, // Use the reliable flag directly
                media_path: aboutPageData.hero.media_path || null
            },
            contentBlocks: [],
            cards: []
        };

        const heroFileInput = document.getElementById('about-hero-file');
        if (heroFileInput.files[0]) {
            formData.append('hero_media_file', heroFileInput.files[0]);
        }

        document.querySelectorAll('#about-content-blocks-container .dynamic-about-block').forEach(el => {
            const blockId = el.dataset.id;
            const originalBlock = aboutPageData.contentBlocks.find(b => b.id == blockId) || {};
            let media_path = originalBlock.media_path || null;
            if (blockMediaState[blockId] === 'cleared') {
                media_path = null;
            }
            const blockData = { id: blockId, type: el.dataset.type, media_path: media_path, content: '' };
            if (blockData.type === 'text') {
                blockData.content = el.querySelector('.block-content').value;
            } else {
                const fileInput = el.querySelector('.block-media-file');
                if (fileInput.files[0]) {
                    formData.append(`block_media_file_${blockId}`, fileInput.files[0]);
                }
            }
            dataToSave.contentBlocks.push(blockData);
        });

        document.querySelectorAll('#about-cards-container .dynamic-about-card').forEach(el => {
            dataToSave.cards.push({
                id: el.dataset.id,
                tabTitle: el.querySelector('.card-tab-title').value,
                cardTitle: el.querySelector('.card-title').value,
                content: el.querySelector('.card-content').value
            });
        });
        formData.append('data', JSON.stringify(dataToSave));

        try {
            const response = await fetch(getApiPath('about_handler.php'), {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                saveBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i> Saved!';
                await loadAboutPageData();
            } else {
                alert('Save failed: ' + result.message);
                saveBtn.innerHTML = '<i class="bi bi-x-lg me-2"></i> Failed';
            }
        } catch (error) {
            alert('An error occurred: ' + error.message);
            saveBtn.innerHTML = '<i class="bi bi-x-lg me-2"></i> Error';
        } finally {
            setTimeout(() => {
                saveBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Save All Changes';
                saveBtn.disabled = false;
            }, 2000);
            confirmationModal.hide();
        }
    }

    function renderAll() {
        renderMainSection();
        renderContentBlocks();
        renderCards();
    }
    
    function renderMainSection() {
        const { hero } = aboutPageData;
        document.getElementById('about-hero-title').value = hero.title || '';
        document.getElementById('about-hero-description').value = hero.description || '';
        document.getElementById('about-hero-file').value = '';
        const previewContainer = document.getElementById('about-hero-preview');
        if (hero.media_path) {
            const mediaUrl = `../${hero.media_path}?t=${new Date().getTime()}`; 
            previewContainer.innerHTML = hero.media_type === 'video' 
                ? `<video src="${mediaUrl}" class="img-fluid" controls autoplay muted loop></video>` 
                : `<img src="${mediaUrl}" class="img-fluid" alt="Hero Preview">`;
        } else {
            previewContainer.innerHTML = `<p class="text-muted m-0">No media uploaded.</p>`;
        }
    }
    
    function renderContentBlocks() {
        const container = document.getElementById('about-content-blocks-container'); container.innerHTML = '';
        (aboutPageData.contentBlocks || []).forEach(block => {
            const templateId = block.type === 'text' ? 'about-text-block-template' : 'about-media-block-template';
            const clone = document.getElementById(templateId).content.cloneNode(true);
            const blockEl = clone.firstElementChild; blockEl.dataset.id = block.id;
            if (block.type === 'text') {
                blockEl.querySelector('.block-content').value = block.content;
            } else {
                blockEl.querySelector('.block-media-file').value = '';
                const preview = blockEl.querySelector('.block-media-preview');
                if (block.media_path) {
                    const mediaUrl = `../${block.media_path}?t=${new Date().getTime()}`;
                    preview.innerHTML = block.media_type === 'video' 
                        ? `<video src="${mediaUrl}" class="img-fluid" controls autoplay muted loop></video>` 
                        : `<img src="${mediaUrl}" class="img-fluid" alt="Media Preview">`;
                } else {
                    preview.innerHTML = `<p class="text-muted m-0">Choose a file to preview.</p>`;
                }
            }
            container.appendChild(clone);
        });
    }

    function renderCards() {
        const container = document.getElementById('about-cards-container'); container.innerHTML = '';
        (aboutPageData.cards || []).forEach(card => {
            const clone = document.getElementById('about-card-template').content.cloneNode(true);
            const cardEl = clone.firstElementChild; cardEl.dataset.id = card.id;
            cardEl.querySelector('.card-tab-title').value = card.tab_title;
            cardEl.querySelector('.card-title').value = card.card_title;
            cardEl.querySelector('.card-content').value = card.content;
            container.appendChild(clone);
        });
    }

    function gatherCurrentDataForPreview() {
        const data = { hero: {}, contentBlocks: [], cards: [] };
        data.hero.title = document.getElementById('about-hero-title').value;
        data.hero.description = document.getElementById('about-hero-description').value;
        const heroPreviewEl = document.getElementById('about-hero-preview').querySelector('img, video');
        data.hero.mediaHTML = heroPreviewEl ? heroPreviewEl.outerHTML : '';
        document.querySelectorAll('#about-content-blocks-container .dynamic-about-block').forEach(el => {
            if (el.dataset.type === 'text') {
                data.contentBlocks.push({ type: 'text', content: el.querySelector('.block-content').value });
            } else {
                const mediaPreviewEl = el.querySelector('.block-media-preview').querySelector('img, video');
                data.contentBlocks.push({ type: 'media', mediaHTML: mediaPreviewEl ? mediaPreviewEl.outerHTML : '' });
            }
        });
        document.querySelectorAll('#about-cards-container .dynamic-about-card').forEach(el => {
            data.cards.push({
                id: el.dataset.id,
                tabTitle: el.querySelector('.card-tab-title').value,
                cardTitle: el.querySelector('.card-title').value,
                content: el.querySelector('.card-content').value
            });
        });
        return data;
    }

    // --- Event Listeners ---
    document.getElementById('about').addEventListener('click', e => {
        const removeBtn = e.target.closest('.remove-about-block-btn, .remove-about-card-btn');
        if (removeBtn) { removeBtn.closest('.dynamic-about-block, .dynamic-about-card').remove(); }

        // Set the flag to true ONLY when the user clicks 'Clear'
        if (e.target.closest('#clear-hero-media-btn')) {
            document.getElementById('about-hero-preview').innerHTML = `<p class="text-muted m-0">Media cleared. Save to confirm.</p>`;
            document.getElementById('about-hero-file').value = '';
            heroMediaWasCleared = true;
        }

        const clearBlockBtn = e.target.closest('.clear-block-media-btn');
        if (clearBlockBtn) {
            const blockEl = clearBlockBtn.closest('.dynamic-about-block');
            blockEl.querySelector('.block-media-preview').innerHTML = `<p class="text-muted m-0">Media cleared. Save to confirm.</p>`;
            blockEl.querySelector('.block-media-file').value = '';
            blockMediaState[blockEl.dataset.id] = 'cleared';
        }
    });

    document.getElementById('about').addEventListener('change', async e => {
        const heroFileInput = e.target.closest('#about-hero-file');
        const blockFileInput = e.target.closest('.block-media-file');
        
        // Reset the flag if the user uploads a new file, as they are replacing, not clearing.
        if (heroFileInput && heroFileInput.files[0]) {
            heroMediaWasCleared = false;
        }
        if (blockFileInput && blockFileInput.files[0]) {
             const blockEl = blockFileInput.closest('.dynamic-about-block');
             blockMediaState[blockEl.dataset.id] = 'new_file';
        }
        
        // This logic just shows a temporary preview of the new file
        const fileInput = e.target.closest('#about-hero-file, .block-media-file');
        if(fileInput && fileInput.files[0]){
             const file = fileInput.files[0];
             const reader = new FileReader();
             reader.onload = (event) => {
                const previewContainer = fileInput.id === 'about-hero-file' ? document.getElementById('about-hero-preview') : fileInput.closest('.dynamic-about-block').querySelector('.block-media-preview');
                previewContainer.innerHTML = file.type.startsWith('video/')
                    ? `<video src="${event.target.result}" class="img-fluid" controls autoplay muted loop></video>`
                    : `<img src="${event.target.result}" class="img-fluid" alt="Preview">`;
             }
             reader.readAsDataURL(file);
        }
    });

    previewBtn.addEventListener('click', () => {
        const data = gatherCurrentDataForPreview();
        let previewHTML = '<div class="container py-3">';
        previewHTML += `
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        ${data.hero.mediaHTML}
                    </div>
                    <div class="col-lg-6">
                        <h2 style="color: #023621; font-weight: 700;">${data.hero.title}</h2>
                        <p class="fs-5 my-4" style="line-height: 1.7;">${data.hero.description.replace(/\n/g, '<br>')}</p>
                        ${(data.contentBlocks.length > 0 || data.cards.length > 0) ? '<button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#aboutPreviewCollapsibleContent">Learn More</button>' : ''}
                    </div>
                </div>
            </div>`;
        previewHTML += '<div class="collapse show" id="aboutPreviewCollapsibleContent">';
        data.contentBlocks.forEach(block => {
            if (block.type === 'text') {
                previewHTML += `<p class="p-4">${block.content.replace(/\n/g, '<br>')}</p>`;
            } else if (block.mediaHTML) {
                previewHTML += `<div class="my-4">${block.mediaHTML}</div>`;
            }
        });
        if (data.cards.length > 0) {
            previewHTML += '<hr class="my-5"><div class="p-4 rounded" style="background-color: #f0f0f0;">';
            previewHTML += '<ul class="nav nav-tabs" role="tablist">';
            data.cards.forEach((card, index) => {
                const cardPreviewId = `preview-card-${index}`;
                previewHTML += `<li class="nav-item" role="presentation"><button class="nav-link ${index === 0 ? 'active' : ''}" data-bs-toggle="tab" data-bs-target="#pane-${cardPreviewId}" type="button">${card.tabTitle}</button></li>`;
            });
            previewHTML += '</ul>';
            previewHTML += '<div class="tab-content bg-white p-4 border border-top-0 rounded-bottom">';
            data.cards.forEach((card, index) => {
                const cardPreviewId = `preview-card-${index}`;
                previewHTML += `<div class="tab-pane fade ${index === 0 ? 'show active' : ''}" id="pane-${cardPreviewId}"><h5>${card.cardTitle}</h5><p>${card.content.replace(/\n/g, '<br>')}</p></div>`;
            });
            previewHTML += '</div></div>';
        }
        previewHTML += '</div></div>';
        previewModalBody.innerHTML = previewHTML;
        previewModal.show();
    });

    document.getElementById('about-edit-nav').addEventListener('click', e => { if (e.target.tagName === 'A') { e.preventDefault(); document.querySelectorAll('#about-edit-nav .nav-link').forEach(link => link.classList.remove('active')); e.target.classList.add('active'); document.querySelectorAll('.about-edit-pane').forEach(pane => pane.style.display = 'none'); document.getElementById(e.target.dataset.target).style.display = 'block'; } });
    document.getElementById('add-text-block-btn').addEventListener('click', () => { const newId = `new_${Date.now()}`; const clone = document.getElementById('about-text-block-template').content.cloneNode(true); clone.firstElementChild.dataset.id = newId; document.getElementById('about-content-blocks-container').appendChild(clone); });
    document.getElementById('add-media-block-btn').addEventListener('click', () => { const newId = `new_${Date.now()}`; const clone = document.getElementById('about-media-block-template').content.cloneNode(true); const blockEl = clone.firstElementChild; blockEl.dataset.id = newId; blockEl.querySelector('.block-media-preview').innerHTML = `<p class="text-muted m-0">Choose a file to preview.</p>`; document.getElementById('about-content-blocks-container').appendChild(clone); });
    document.getElementById('add-about-card-btn').addEventListener('click', () => { const clone = document.getElementById('about-card-template').content.cloneNode(true); clone.firstElementChild.dataset.id = `new_${Date.now()}`; document.getElementById('about-cards-container').appendChild(clone); });
    saveBtn.addEventListener('click', () => { confirmationModalTitle.textContent = "Confirm Save"; confirmationModalBody.textContent = 'Are you sure you want to save all changes to the About page? This will overwrite the current live version.'; confirmActionBtn.className = 'btn btn-success'; confirmActionBtn.onclick = saveAboutPageData; confirmationModal.show(); });
    cancelBtn.addEventListener('click', () => { confirmationModalTitle.textContent = "Confirm Cancel"; confirmationModalBody.textContent = 'Are you sure you want to discard all unsaved changes? The editor will be reset to the last saved state.'; confirmActionBtn.className = 'btn btn-secondary'; confirmActionBtn.onclick = () => { loadAboutPageData(); confirmationModal.hide(); }; confirmationModal.show(); });

    // Initial Load
    loadAboutPageData();
})();

    // --- START: SCRIPT FOR SERVICES PAGE MANAGEMENT ---
    (function() {
        // Data Persistence
        function loadServicesData() {
            const savedData = localStorage.getItem('raisCmsServicesData');
            if (savedData) {
                return JSON.parse(savedData);
            }
            return [];
        }

        function saveServicesData(data) {
            localStorage.setItem('raisCmsServicesData', JSON.stringify(data));
        }

        let servicesData = loadServicesData();
        let selectedServiceId = null;
        let nextServiceId = (servicesData.length > 0 ? Math.max(...servicesData.map(s => s.id)) : 0) + 1;

        // UI Elements
        const serviceCardsContainer = document.getElementById('service-cards-container');
        const addServiceBtn = document.getElementById('add-service-btn');
        const editServiceBtn = document.getElementById('edit-service-btn');
        const deleteServiceBtn = document.getElementById('delete-service-btn');
        const previewServiceBtn = document.getElementById('preview-service-btn');
        const serviceModal = new bootstrap.Modal(document.getElementById('service-modal'));
        const previewModal = new bootstrap.Modal(document.getElementById('service-preview-modal'));
        const serviceModalTitle = document.getElementById('service-modal-title');
        const serviceForm = document.getElementById('service-form');
        const saveServiceBtn = document.getElementById('save-service-btn');
        const addSectionBtn = document.getElementById('add-section-btn');
        const dynamicSectionsContainer = document.getElementById('dynamic-sections-container');
        const sectionTemplate = document.getElementById('service-section-template');

        function renderServiceCards() {
            serviceCardsContainer.innerHTML = '';
            servicesData.forEach(service => {
                const cardCol = document.createElement('div');
                cardCol.className = 'col-md-6 col-lg-4';
                const card = document.createElement('div');
                card.className = 'card service-card';
                card.dataset.id = service.id;
                if (service.id === selectedServiceId) card.classList.add('selected');
                const mediaUrl = service.heroMediaDataUrl || 'https://via.placeholder.com/800x600.png?text=Service+Media';
                card.innerHTML = `<img src="${mediaUrl}" class="card-img-top service-card-img" alt="${service.name}"><div class="card-body"><h5 class="card-title">${service.name}</h5><p class="card-text text-muted">${service.description}</p></div>`;
                cardCol.appendChild(card);
                serviceCardsContainer.appendChild(cardCol);
            });
        }

        function updateFabState() {
            const isSelected = selectedServiceId !== null;
            editServiceBtn.disabled = !isSelected;
            deleteServiceBtn.disabled = !isSelected;
            previewServiceBtn.disabled = !isSelected;
        }

        function selectCard(serviceId) {
            selectedServiceId = (selectedServiceId === serviceId) ? null : serviceId;
            renderServiceCards();
            updateFabState();
        }

        function createSectionElement(data = {}) {
            const newSection = sectionTemplate.content.cloneNode(true).firstElementChild;
            newSection.querySelector('.remove-section-btn').addEventListener('click', () => {
                newSection.remove();
                updateSectionNumbers();
            });
            if (data.title) newSection.querySelector('.section-title').value = data.title;
            if (data.description) newSection.querySelector('.section-description').value = data.description;
            return newSection;
        }

        function updateSectionNumbers() {
            dynamicSectionsContainer.querySelectorAll('.dynamic-section').forEach((section, index) => {
                section.querySelector('.section-number').textContent = `Section ${index + 1}`;
            });
        }

        function resetAndPrepareModal(mode = 'add', service = null) {
            serviceForm.reset();
            dynamicSectionsContainer.innerHTML = '';
            serviceForm.classList.remove('was-validated');
            if (mode === 'add') {
                serviceModalTitle.textContent = 'Add New Service';
                saveServiceBtn.textContent = 'Add Service';
                saveServiceBtn.dataset.mode = 'add';
            } else if (mode === 'edit' && service) {
                serviceModalTitle.textContent = `Edit Service: ${service.name}`;
                saveServiceBtn.textContent = 'Update Service';
                saveServiceBtn.dataset.mode = 'edit';
                document.getElementById('service-name').value = service.name;
                document.getElementById('service-description').value = service.description;
                (service.sections || []).forEach(sectionData => {
                    dynamicSectionsContainer.appendChild(createSectionElement(sectionData));
                });
                updateSectionNumbers();
            }
        }

        serviceCardsContainer.addEventListener('click', (e) => {
            const card = e.target.closest('.service-card');
            if (card) selectCard(parseInt(card.dataset.id));
        });
        addServiceBtn.addEventListener('click', () => {
            resetAndPrepareModal('add');
            serviceModal.show();
        });
        editServiceBtn.addEventListener('click', () => {
            if (selectedServiceId === null) return;
            const service = servicesData.find(s => s.id === selectedServiceId);
            resetAndPrepareModal('edit', service);
            serviceModal.show();
        });
        addSectionBtn.addEventListener('click', () => {
            dynamicSectionsContainer.appendChild(createSectionElement());
            updateSectionNumbers();
        });

        deleteServiceBtn.addEventListener('click', () => {
            if (selectedServiceId === null) return;
            const service = servicesData.find(s => s.id === selectedServiceId);
            confirmationModalTitle.textContent = "Confirm Deletion";
            confirmationModalBody.innerHTML = `Are you sure you want to delete the service: <strong>${service.name}</strong>?`;
            confirmActionBtn.onclick = () => {
                servicesData = servicesData.filter(s => s.id !== selectedServiceId);
                saveServicesData(servicesData);
                selectedServiceId = null;
                renderServiceCards();
                updateFabState();
                confirmationModal.hide();
            };
            confirmationModal.show();
        });

        previewServiceBtn.addEventListener('click', () => {
            if (selectedServiceId === null) return;
            const service = servicesData.find(s => s.id === selectedServiceId);
            const previewTitle = document.getElementById('service-preview-title');
            const previewBody = document.getElementById('service-preview-body');
            previewTitle.textContent = `Preview: ${service.name}`;

            let heroHTML = `<div class="text-center mb-5" style="padding: 6rem 1rem; background-image: url('${service.heroMediaDataUrl || ''}'); background-size: cover; background-position: center; color: white; text-shadow: 1px 1px 3px rgba(0,0,0,0.7);">
                                <h1>${service.name}</h1>
                                <p class="lead">${service.description}</p>
                                <a href="${service.applyNowLink}" class="btn btn-success btn-lg">Apply Now</a>
                            </div>`;

            let tabsHTML = '';
            if (service.sections && service.sections.length > 0) {
                tabsHTML += '<div class="container"><ul class="nav nav-tabs" role="tablist">';
                service.sections.forEach((section, index) => {
                    tabsHTML += `<li class="nav-item" role="presentation"><button class="nav-link ${index === 0 ? 'active' : ''}" id="preview-service-tab-${index}" data-bs-toggle="tab" data-bs-target="#preview-service-pane-${index}" type="button">${section.title}</button></li>`;
                });
                tabsHTML += '</ul>';
                tabsHTML += '<div class="tab-content bg-white p-4 border border-top-0 rounded-bottom">';
                service.sections.forEach((section, index) => {
                    let mediaHTML = section.mediaDataUrl ? `<div class="col-md-5"><img src="${section.mediaDataUrl}" class="img-fluid rounded"></div>` : '';
                    let textColClass = section.mediaDataUrl ? 'col-md-7' : 'col-12';
                    tabsHTML += `<div class="tab-pane fade ${index === 0 ? 'show active' : ''}" id="preview-service-pane-${index}"><div class="row align-items-center"><div class="${textColClass}"><p>${section.description.replace(/\n/g, '<br>')}</p></div>${mediaHTML}</div></div>`;
                });
                tabsHTML += '</div></div>';
            }

            previewBody.innerHTML = heroHTML + tabsHTML;
            previewModal.show();
        });

        // Add event listener to reset z-index when confirmation modal is hidden
        confirmationModalEl.addEventListener('hidden.bs.modal', () => {
            confirmationModalEl.style.zIndex = '';
        });

        saveServiceBtn.addEventListener('click', async () => {
            if (!serviceForm.checkValidity()) {
                serviceForm.classList.add('was-validated');
                serviceForm.reportValidity();
                return;
            }
            const mode = saveServiceBtn.dataset.mode;

            confirmationModalTitle.textContent = "Confirm Save";
            confirmationModalBody.textContent = `Are you sure you want to ${mode === 'add' ? 'add this new' : 'update this'} service?`;

            confirmActionBtn.onclick = async () => {
                serviceModal.hide(); 

                const heroFile = document.getElementById('service-hero-media').files[0];
                const sectionPromises = Array.from(dynamicSectionsContainer.querySelectorAll('.dynamic-section')).map(async (el) => {
                    const sectionFile = el.querySelector('.section-media').files[0];
                    return {
                        title: el.querySelector('.section-title').value,
                        description: el.querySelector('.section-description').value,
                        file: sectionFile,
                        fileName: sectionFile ? sectionFile.name : null
                    };
                });
                const sectionsWithFiles = await Promise.all(sectionPromises);

                let heroMediaDataUrl = null;
                if (heroFile) heroMediaDataUrl = await readFileAsDataURL(heroFile);

                const sectionsWithDataUrls = await Promise.all(sectionsWithFiles.map(async sec => {
                    let mediaDataUrl = null;
                    if (sec.file) mediaDataUrl = await readFileAsDataURL(sec.file);
                    return { ...sec,
                        mediaDataUrl: mediaDataUrl,
                        file: null
                    };
                }));

                if (mode === 'add') {
                    servicesData.push({
                        id: nextServiceId++,
                        name: document.getElementById('service-name').value,
                        description: document.getElementById('service-description').value,
                        applyNowLink: '#', // The button is now static
                        heroMediaDataUrl: heroMediaDataUrl,
                        sections: sectionsWithDataUrls
                    });
                } else {
                    const service = servicesData.find(s => s.id === selectedServiceId);
                    service.name = document.getElementById('service-name').value;
                    service.description = document.getElementById('service-description').value;
                    service.applyNowLink = '#'; // The button is now static
                    if (heroMediaDataUrl) service.heroMediaDataUrl = heroMediaDataUrl;

                    const originalSections = service.sections || [];
                    const updatedSections = sectionsWithDataUrls.map((sec, index) => {
                        if (!sec.mediaDataUrl) {
                            sec.mediaDataUrl = originalSections[index]?.mediaDataUrl || null;
                        }
                        return sec;
                    });
                    service.sections = updatedSections;
                }

                saveServicesData(servicesData);
                renderServiceCards();
                updateFabState();
                confirmationModal.hide();
            };
            
            // 📌 FIX: Manually set the z-index to ensure it appears on top
            confirmationModalEl.style.zIndex = "1060";
            confirmationModal.show();
        });
        renderServiceCards();
        updateFabState();
    })();
    
    // --- START: SCRIPT FOR BLOGS PAGE MANAGEMENT ---
(function() {
    let selectedBlogId = null;
    let blogsData = loadBlogsData();
    let nextBlogId = (blogsData.length > 0 ? Math.max(...blogsData.map(b => b.id)) : 0) + 1;

    const blogCardsContainer = document.getElementById('blog-cards-container');
    const addBlogBtn = document.getElementById('add-blog-btn');
    const editBlogBtn = document.getElementById('edit-blog-btn');
    const deleteBlogBtn = document.getElementById('delete-blog-btn');
    const previewBlogBtn = document.getElementById('preview-blog-btn');
    const blogModal = new bootstrap.Modal(document.getElementById('blog-modal'));
    const blogPreviewModal = new bootstrap.Modal(document.getElementById('blog-preview-modal'));
    const blogModalTitle = document.getElementById('blog-modal-title');
    const blogForm = document.getElementById('blog-form');
    const saveBlogBtn = document.getElementById('save-blog-btn');
    const addBlogSectionBtn = document.getElementById('add-blog-section-btn');
    const blogDynamicSectionsContainer = document.getElementById('blog-dynamic-sections-container');
    const blogSectionTemplate = document.getElementById('blog-section-template');

    function loadBlogsData() {
        const savedData = localStorage.getItem('raisCmsBlogsData');
        return savedData ? JSON.parse(savedData) : initialBlogsData;
    }

    function saveBlogsData(data) {
        localStorage.setItem('raisCmsBlogsData', JSON.stringify(data));
    }

    function renderBlogCards() {
        blogCardsContainer.innerHTML = '';
        blogsData.forEach(blog => {
            const cardCol = document.createElement('div');
            cardCol.className = 'col-md-6 col-lg-4';
            const card = document.createElement('div');
            card.className = 'card blog-card';
            card.dataset.id = blog.id;
            if (blog.id === selectedBlogId) card.classList.add('selected');
            const mediaUrl = blog.heroMediaDataUrl || 'https://via.placeholder.com/800x600.png?text=Blog+Image';
            card.innerHTML = `<img src="${mediaUrl}" class="card-img-top blog-card-img" alt="${blog.title}"><div class="card-body"><h5 class="card-title">${blog.title}</h5><p class="card-text text-muted">${blog.summary}</p></div>`;
            cardCol.appendChild(card);
            blogCardsContainer.appendChild(cardCol);
        });
    }

    function updateFabState() {
        const isSelected = selectedBlogId !== null;
        editBlogBtn.disabled = !isSelected;
        deleteBlogBtn.disabled = !isSelected;
        previewBlogBtn.disabled = !isSelected;
    }

    function selectCard(blogId) {
        selectedBlogId = (selectedBlogId === blogId) ? null : blogId;
        renderBlogCards();
        updateFabState();
    }

    function createSectionElement(data = {}) {
        const newSection = blogSectionTemplate.content.cloneNode(true).firstElementChild;
        newSection.querySelector('.remove-section-btn').addEventListener('click', () => {
            newSection.remove();
            updateSectionNumbers();
        });
        if (data.title) newSection.querySelector('.section-title').value = data.title;
        if (data.description) newSection.querySelector('.section-description').value = data.description;
        return newSection;
    }

    function updateSectionNumbers() {
        blogDynamicSectionsContainer.querySelectorAll('.dynamic-section').forEach((section, index) => {
            section.querySelector('.section-number').textContent = `Section ${index + 1}`;
        });
    }

    function resetAndPrepareModal(mode = 'add', blog = null) {
        blogForm.reset();
        blogDynamicSectionsContainer.innerHTML = '';
        blogForm.classList.remove('was-validated');
        if (mode === 'add') {
            blogModalTitle.textContent = 'Add New Blog Post';
            saveBlogBtn.textContent = 'Add Blog Post';
            saveBlogBtn.dataset.mode = 'add';
        } else if (mode === 'edit' && blog) {
            blogModalTitle.textContent = `Edit Blog: ${blog.title}`;
            saveBlogBtn.textContent = 'Update Blog Post';
            saveBlogBtn.dataset.mode = 'edit';
            document.getElementById('blog-title').value = blog.title;
            document.getElementById('blog-author').value = blog.author;
            document.getElementById('blog-publish-date').value = blog.publishDate;
            document.getElementById('blog-summary').value = blog.summary;
            (blog.sections || []).forEach(sectionData => {
                blogDynamicSectionsContainer.appendChild(createSectionElement(sectionData));
            });
            updateSectionNumbers();
        }
    }

    blogCardsContainer.addEventListener('click', (e) => {
        const card = e.target.closest('.blog-card');
        if (card) selectCard(parseInt(card.dataset.id));
    });
    addBlogBtn.addEventListener('click', () => {
        resetAndPrepareModal('add');
        blogModal.show();
    });
    editBlogBtn.addEventListener('click', () => {
        if (selectedBlogId === null) return;
        const blog = blogsData.find(b => b.id === selectedBlogId);
        resetAndPrepareModal('edit', blog);
        blogModal.show();
    });
    addBlogSectionBtn.addEventListener('click', () => {
        blogDynamicSectionsContainer.appendChild(createSectionElement());
        updateSectionNumbers();
    });

    saveBlogBtn.addEventListener('click', () => {
        if (!blogForm.checkValidity()) {
            blogForm.classList.add('was-validated');
            blogForm.reportValidity();
            return;
        }
        const mode = saveBlogBtn.dataset.mode;
        confirmationModalTitle.textContent = "Confirm Save";
        confirmationModalBody.textContent = `Are you sure you want to ${mode === 'add' ? 'add this new' : 'update this'} blog post?`;

        confirmActionBtn.onclick = async () => {
            blogModal.hide();
            const blogDataToSave = {
                title: document.getElementById('blog-title').value,
                author: document.getElementById('blog-author').value,
                publishDate: document.getElementById('blog-publish-date').value,
                summary: document.getElementById('blog-summary').value,
            };
            const heroFile = document.getElementById('blog-hero-media').files[0];
            const sectionPromises = Array.from(blogDynamicSectionsContainer.querySelectorAll('.dynamic-section')).map(async (el) => {
                const sectionFile = el.querySelector('.section-media').files[0];
                return { title: el.querySelector('.section-title').value, description: el.querySelector('.section-description').value, file: sectionFile };
            });
            const sectionsWithFiles = await Promise.all(sectionPromises);
            blogDataToSave.heroMediaDataUrl = heroFile ? await readFileAsDataURL(heroFile) : null;
            blogDataToSave.sections = await Promise.all(sectionsWithFiles.map(async sec => ({ title: sec.title, description: sec.description, mediaDataUrl: sec.file ? await readFileAsDataURL(sec.file) : null })));
            if (mode === 'add') {
                blogDataToSave.id = nextBlogId++;
                blogsData.push(blogDataToSave);
            } else {
                const blogIndex = blogsData.findIndex(b => b.id === selectedBlogId);
                const originalBlog = blogsData[blogIndex];
                if (!blogDataToSave.heroMediaDataUrl) blogDataToSave.heroMediaDataUrl = originalBlog.heroMediaDataUrl;
                blogDataToSave.sections = blogDataToSave.sections.map((sec, index) => {
                    if (!sec.mediaDataUrl) sec.mediaDataUrl = originalBlog.sections[index]?.mediaDataUrl || null;
                    return sec;
                });
                blogsData[blogIndex] = { ...originalBlog, ...blogDataToSave };
            }
            saveBlogsData(blogsData);
            renderBlogCards();
            updateFabState();
            confirmationModal.hide();
        };
        
        // 📌 FIX: Manually set the z-index to ensure it appears on top of the blog modal
        confirmationModalEl.style.zIndex = "1060";
        confirmationModal.show();
    });

    deleteBlogBtn.addEventListener('click', () => {
        if (selectedBlogId === null) return;
        const blog = blogsData.find(b => b.id === selectedBlogId);
        confirmationModalTitle.textContent = "Confirm Deletion";
        confirmationModalBody.innerHTML = `Are you sure you want to delete the blog post: <strong>${blog.title}</strong>?`;
        confirmActionBtn.onclick = () => {
            blogsData = blogsData.filter(b => b.id !== selectedBlogId);
            saveBlogsData(blogsData);
            selectedBlogId = null;
            renderBlogCards();
            updateFabState();
            confirmationModal.hide();
        };
        confirmationModal.show();
    });

    // 📌 CHANGE: Revamped preview logic for the new card-based layout
    previewBlogBtn.addEventListener('click', () => {
        if (selectedBlogId === null) return;
        const blog = blogsData.find(b => b.id === selectedBlogId);
        document.getElementById('blog-preview-title').textContent = `Preview: ${blog.title}`;
        const previewBody = document.getElementById('blog-preview-body');

        const formatDate = (dateString) => {
            if (!dateString) return '';
            const date = new Date(dateString + 'T00:00:00');
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        };
        
        let content = `<div style="background-color: #f8f9fa; padding: 2rem;">
                        <div class="container">`;

        // Title and Byline (outside the cards)
        content += `<div class="text-center mb-4">
                        <h1 class="display-5">${blog.title}</h1>
                        <p class="text-muted">Written by ${blog.author || 'N/A'} on ${formatDate(blog.publishDate)}</p>
                    </div>`;
        
        // --- First Card: Hero Image and Intro ---
        if (blog.heroMediaDataUrl || (blog.sections && blog.sections.length > 0)) {
            content += `<div class="blog-section-card">`;
            if (blog.heroMediaDataUrl) {
                content += `<img src="${blog.heroMediaDataUrl}" class="img-fluid rounded mb-4" alt="Hero Image">`;
            }
            if (blog.sections && blog.sections.length > 0) {
                const introSection = blog.sections[0];
                content += `<p class="lead">${introSection.description.replace(/\n/g, '<br>')}</p>`;
            }
            content += `</div>`;
        }

        // --- Subsequent Cards: One for each remaining section ---
        if (blog.sections && blog.sections.length > 1) {
            const otherSections = blog.sections.slice(1);
            otherSections.forEach((section, index) => {
                content += `<div class="blog-section-card">`;
                if (section.title) {
                    content += `<h3 class="mb-3">${section.title}</h3>`;
                }

                const hasMedia = !!section.mediaDataUrl;
                const textColClass = hasMedia ? 'col-lg-7' : 'col-lg-12';
                const imageOrderClass = index % 2 === 0 ? '' : 'order-lg-2';

                content += `<div class="row align-items-center">`;
                if (hasMedia) {
                    content += `<div class="col-lg-5 ${imageOrderClass}">
                                    <img src="${section.mediaDataUrl}" class="img-fluid rounded">
                                </div>`;
                }
                content += `<div class="${textColClass}">
                                <p>${section.description.replace(/\n/g, '<br>')}</p>
                            </div>
                        </div>`; // close .row
                content += `</div>`; // close .blog-section-card
            });
        }
        
        content += `</div></div>`; // Close container and wrapper
        previewBody.innerHTML = content;
        blogPreviewModal.show();
    });

    renderBlogCards();
    updateFabState();
})();

    // ... after the closing })(); for the blog management script ...

// --- START: SCRIPT FOR EXAMS MANAGEMENT ---
(function() {
    function loadExamsData() {
        const savedData = localStorage.getItem('raisCmsExamsData');
        return savedData ? JSON.parse(savedData) : [];
    }

    function saveExamsData(data) {
        localStorage.setItem('raisCmsExamsData', JSON.stringify(data));
    }

    let examsData = loadExamsData();
    let selectedExamId = null;
    let nextExamId = (examsData.length > 0 ? Math.max(...examsData.map(e => e.id)) : 0) + 1;

    const examCardsContainer = document.getElementById('exam-cards-container');
    const addExamBtn = document.getElementById('add-exam-btn');
    const editExamBtn = document.getElementById('edit-exam-btn');
    const deleteExamBtn = document.getElementById('delete-exam-btn');
    const previewExamBtn = document.getElementById('preview-exam-btn');

    const examModalEl = document.getElementById('exam-modal');
    const examModal = new bootstrap.Modal(examModalEl);
    const examPreviewModal = new bootstrap.Modal(document.getElementById('exam-preview-modal'));
    const examModalTitle = document.getElementById('exam-modal-title');
    const examForm = document.getElementById('exam-form');
    const saveExamBtn = document.getElementById('save-exam-btn');

    // Add an event listener to the confirmation modal to reset its z-index when closed
    confirmationModalEl.addEventListener('hidden.bs.modal', () => {
        confirmationModalEl.style.zIndex = '';
    });

    function renderExamCards() {
        examCardsContainer.innerHTML = '';
        examsData.forEach(exam => {
            const cardCol = document.createElement('div');
            cardCol.className = 'col-md-6 col-lg-4';
            const card = document.createElement('div');
            card.className = 'card service-card';
            card.dataset.id = exam.id;
            if (exam.id === selectedExamId) card.classList.add('selected');
            const mediaUrl = exam.heroMediaDataUrl || 'https://via.placeholder.com/800x600.png?text=Exam+Media';
            card.innerHTML = `<img src="${mediaUrl}" class="card-img-top" alt="${exam.name}"><div class="card-body"><h5 class="card-title">${exam.name}</h5></div>`;
            cardCol.appendChild(card);
            examCardsContainer.appendChild(cardCol);
        });
    }

    function updateFabState() {
        const isSelected = selectedExamId !== null;
        editExamBtn.disabled = !isSelected;
        deleteExamBtn.disabled = !isSelected;
        previewExamBtn.disabled = !isSelected;
    }

    function selectCard(examId) {
        selectedExamId = (selectedExamId === examId) ? null : examId;
        renderExamCards();
        updateFabState();
    }

    examCardsContainer.addEventListener('click', (e) => {
        const card = e.target.closest('.service-card');
        if (card) selectCard(parseInt(card.dataset.id));
    });

    function createDynamicElement(containerId, templateId, data) {
        const container = document.getElementById(containerId);
        const template = document.getElementById(templateId);
        const clone = template.content.cloneNode(true);
        const itemEl = clone.firstElementChild;
        if (templateId === 'exam-format-template' && data) {
            itemEl.querySelector('.format-icon').value = data.icon || '';
            itemEl.querySelector('.format-title').value = data.title || '';
            itemEl.querySelector('.format-description').value = data.description || '';
        } else if (templateId === 'exam-infocard-template' && data) {
            itemEl.querySelector('.infocard-title').value = data.title || '';
            itemEl.querySelector('.infocard-description').value = data.description || '';
        } else if (templateId === 'exam-faq-template' && data) {
            itemEl.querySelector('.faq-question').value = data.question || '';
            itemEl.querySelector('.faq-answer').value = data.answer || '';
        }
        itemEl.querySelector('.remove-exam-item-btn').addEventListener('click', () => itemEl.remove());
        container.appendChild(clone);
    }

    examModalEl.addEventListener('click', e => {
        if (e.target.matches('.nav-link')) {
            e.preventDefault();
            const targetPaneId = e.target.dataset.pane;
            examModalEl.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            e.target.classList.add('active');
            examModalEl.querySelectorAll('.exam-pane').forEach(pane => {
                pane.style.display = (pane.id === targetPaneId) ? 'block' : 'none';
            });
        }
    });

    function resetAndPrepareModal(mode = 'add', exam = null) {
        examForm.reset();
        examForm.classList.remove('was-validated');
        document.getElementById('exam-format-container').innerHTML = '';
        document.getElementById('exam-infocards-container').innerHTML = '';
        document.getElementById('exam-faq-container').innerHTML = '';
        examModalEl.querySelector('.nav-link[data-pane="exam-pane-info"]').click();
        if (mode === 'add') {
            examModalTitle.textContent = 'Add New Exam';
            saveExamBtn.dataset.mode = 'add';
        } else if (mode === 'edit' && exam) {
            examModalTitle.textContent = `Edit Exam: ${exam.name}`;
            saveExamBtn.dataset.mode = 'edit';
            document.getElementById('exam-name').value = exam.name;
            document.getElementById('exam-about-content').value = exam.aboutContent || '';
            (exam.formats || []).forEach(d => createDynamicElement('exam-format-container', 'exam-format-template', d));
            (exam.infoCards || []).forEach(d => createDynamicElement('exam-infocards-container', 'exam-infocard-template', d));
            (exam.faqs || []).forEach(d => createDynamicElement('exam-faq-container', 'exam-faq-template', d));
        }
    }

    addExamBtn.addEventListener('click', () => { resetAndPrepareModal('add'); examModal.show(); });
    editExamBtn.addEventListener('click', () => { if (selectedExamId === null) return; const exam = examsData.find(e => e.id === selectedExamId); resetAndPrepareModal('edit', exam); examModal.show(); });
    document.getElementById('add-exam-format-btn').addEventListener('click', () => createDynamicElement('exam-format-container', 'exam-format-template'));
    document.getElementById('add-exam-infocard-btn').addEventListener('click', () => createDynamicElement('exam-infocards-container', 'exam-infocard-template'));
    document.getElementById('add-exam-faq-btn').addEventListener('click', () => createDynamicElement('exam-faq-container', 'exam-faq-template'));

    deleteExamBtn.addEventListener('click', () => {
        if (selectedExamId === null) return;
        const exam = examsData.find(e => e.id === selectedExamId);
        confirmationModalTitle.textContent = "Confirm Deletion";
        confirmationModalBody.innerHTML = `Are you sure you want to delete the exam: <strong>${exam.name}</strong>?`;
        
        // FIX: Change button color for delete
        confirmActionBtn.className = 'btn btn-danger';
        
        confirmActionBtn.onclick = () => {
            examsData = examsData.filter(e => e.id !== selectedExamId);
            saveExamsData(examsData);
            selectedExamId = null;
            renderExamCards();
            updateFabState();
            confirmationModal.hide();
        };
        confirmationModal.show();
    });

    saveExamBtn.addEventListener('click', () => {
        if (!examForm.checkValidity()) {
            examForm.classList.add('was-validated');
            return;
        }
        const mode = saveExamBtn.dataset.mode;
        confirmationModalTitle.textContent = "Confirm Save";
        confirmationModalBody.textContent = `Are you sure you want to ${mode === 'add' ? 'add this new' : 'update this'} exam?`;

        // FIX: Change button color for save
        confirmActionBtn.className = 'btn btn-primary';

        confirmActionBtn.onclick = async () => {
            const examDataToSave = {
                name: document.getElementById('exam-name').value,
                aboutContent: document.getElementById('exam-about-content').value,
                formats: Array.from(document.querySelectorAll('#exam-format-container .dynamic-exam-item')).map(el => ({ icon: el.querySelector('.format-icon').value, title: el.querySelector('.format-title').value, description: el.querySelector('.format-description').value })),
                infoCards: Array.from(document.querySelectorAll('#exam-infocards-container .dynamic-exam-item')).map(el => ({ title: el.querySelector('.infocard-title').value, description: el.querySelector('.infocard-description').value })),
                faqs: Array.from(document.querySelectorAll('#exam-faq-container .dynamic-exam-item')).map(el => ({ question: el.querySelector('.faq-question').value, answer: el.querySelector('.faq-answer').value }))
            };
            const heroFile = document.getElementById('exam-hero-media').files[0];
            const aboutFile = document.getElementById('exam-about-media').files[0];
            if (heroFile) examDataToSave.heroMediaDataUrl = await readFileAsDataURL(heroFile);
            if (aboutFile) examDataToSave.aboutMediaDataUrl = await readFileAsDataURL(aboutFile);
            if (mode === 'add') {
                examDataToSave.id = nextExamId++;
                examsData.push(examDataToSave);
            } else {
                const examIndex = examsData.findIndex(e => e.id === selectedExamId);
                const originalExam = examsData[examIndex];
                if (!examDataToSave.heroMediaDataUrl) examDataToSave.heroMediaDataUrl = originalExam.heroMediaDataUrl;
                if (!examDataToSave.aboutMediaDataUrl) examDataToSave.aboutMediaDataUrl = originalExam.aboutMediaDataUrl;
                examsData[examIndex] = { ...originalExam, ...examDataToSave };
            }
            saveExamsData(examsData);
            renderExamCards();
            updateFabState();
            examModal.hide();
            confirmationModal.hide();
        };

        // FIX: Manually set z-index to ensure it appears on top of the editor modal
        confirmationModalEl.style.zIndex = "1060";
        confirmationModal.show();
    });

    previewExamBtn.addEventListener('click', () => {
        if (selectedExamId === null) return;
        const exam = examsData.find(e => e.id === selectedExamId);
        document.getElementById('exam-preview-title').textContent = `Preview: ${exam.name}`;
        const previewBody = document.getElementById('exam-preview-body');
        let content = '';
        content += `<div class="text-white text-center d-flex align-items-center justify-content-center" style="height: 40vh; background: url(${exam.heroMediaDataUrl || 'https://via.placeholder.com/1200x400'}) no-repeat center center; background-size: cover; text-shadow: 2px 2px 4px #000;"><h1>${exam.name}</h1></div>`;
        if (exam.aboutContent) {
            content += `<div class="container my-5"><div class="row align-items-center g-4"><div class="${exam.aboutMediaDataUrl ? 'col-md-7' : 'col-md-12'}"><h2 class="mb-3">About ${exam.name}</h2><p>${exam.aboutContent.replace(/\n/g, '<br>')}</p></div>`;
            if (exam.aboutMediaDataUrl) content += `<div class="col-md-5"><img src="${exam.aboutMediaDataUrl}" class="img-fluid rounded shadow"></div>`;
            content += `</div></div>`;
        }
        if (exam.formats && exam.formats.length > 0) {
            content += `<div class="container my-5"><h2 class="text-center mb-4">Test Format</h2><div class="row text-center g-4">`;
            exam.formats.forEach(f => {
                content += `<div class="col-md-3"><div class="card h-100 shadow-sm"><div class="card-body"><i class="${f.icon}" style="font-size: 2rem; color: #1E4620;"></i><h5 class="card-title mt-3">${f.title}</h5><p class="card-text text-muted">${f.description}</p></div></div></div>`;
            });
            content += `</div></div>`;
        }
        if (exam.infoCards && exam.infoCards.length > 0) {
            content += `<div style="background-color: #ffffff;"><div class="container py-5"><h2 class="text-center mb-4">How does ${exam.name} work?</h2><div class="row g-4">`;
            exam.infoCards.forEach(c => {
                content += `<div class="col-md-6"><div class="card h-100 shadow-sm"><div class="card-body p-4"><h5>${c.title}</h5><p class="text-muted">${c.description}</p></div></div></div>`;
            });
            content += `</div></div></div>`;
        }
        if (exam.faqs && exam.faqs.length > 0) {
            content += `<div class="container my-5"><h2 class="text-center mb-4">Frequently Asked Questions</h2><div class="accordion" id="faqAccordionPreview">`;
            exam.faqs.forEach((faq, index) => {
                content += `<div class="accordion-item">
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}">${faq.question}</button>
                    </h2>
                    <div id="collapse${index}" class="accordion-collapse collapse" data-bs-parent="#faqAccordionPreview">
                        <div class="accordion-body">${faq.answer.replace(/\n/g, '<br>')}</div>
                    </div>
                </div>`;
            });
            content += `</div></div>`;
        }
        previewBody.innerHTML = content;
        examPreviewModal.show();
    });

    renderExamCards();
    updateFabState();
})();
    
    // --- START: SCRIPT FOR PARTNERS PAGE MANAGEMENT ---
    (function() {
        let selectedPartnerId = null;
        let partnersData = loadPartnersData();
        let nextPartnerId = (partnersData.length > 0 ? Math.max(...partnersData.map(p => p.id)) : 0) + 1;
        
        const partnersTableBody = document.getElementById('partners-table-body');
        const addPartnerBtn = document.getElementById('add-partner-btn');
        const editPartnerBtn = document.getElementById('edit-partner-btn');
        const deletePartnerBtn = document.getElementById('delete-partner-btn');
        const visitPartnerBtn = document.getElementById('visit-partner-btn');
        const partnerModal = new bootstrap.Modal(document.getElementById('partner-modal'));
        const partnerForm = document.getElementById('partner-form');
        const partnerModalTitle = document.getElementById('partner-modal-title');
        const savePartnerBtn = document.getElementById('save-partner-btn');
        const logoInput = document.getElementById('partner-logo');
        const logoPreview = document.getElementById('partner-logo-preview');

        function loadPartnersData() {
            const savedData = localStorage.getItem('raisCmsPartnersData');
            return savedData ? JSON.parse(savedData) : initialPartnersData;
        }

        function savePartnersData(data) {
            localStorage.setItem('raisCmsPartnersData', JSON.stringify(data));
        }

        function renderTable() {
            partnersTableBody.innerHTML = '';
            partnersData.forEach(p => {
                const row = partnersTableBody.insertRow();
                row.dataset.id = p.id;
                if (p.id === selectedPartnerId) row.classList.add('selected');
                row.innerHTML = `<td><img src="${p.logoDataUrl || 'https://via.placeholder.com/30x30.png?text=L'}" alt="${p.name}" class="me-2" style="width:30px; height:30px; object-fit:contain;">${p.name}</td><td><a href="${p.link}" target="_blank">${p.link}</a></td>`;
            });
        }
        function updateFabState() { const isSelected = selectedPartnerId !== null; editPartnerBtn.disabled = !isSelected; deletePartnerBtn.disabled = !isSelected; visitPartnerBtn.disabled = !isSelected; }
        function selectRow(partnerId) { selectedPartnerId = (selectedPartnerId === partnerId) ? null : partnerId; renderTable(); updateFabState(); }
        function resetAndPrepareModal(mode = 'add', partner = null) {
            partnerForm.reset(); partnerForm.classList.remove('was-validated');
            logoPreview.src = 'https://via.placeholder.com/100x100.png?text=Logo';
            if (mode === 'add') {
                partnerModalTitle.textContent = 'Add New Partner'; savePartnerBtn.textContent = 'Add Partner'; savePartnerBtn.dataset.mode = 'add';
            } else if (mode === 'edit' && partner) {
                partnerModalTitle.textContent = `Edit Partner: ${partner.name}`; savePartnerBtn.textContent = 'Update Partner'; savePartnerBtn.dataset.mode = 'edit';
                document.getElementById('partner-name').value = partner.name;
                document.getElementById('partner-link').value = partner.link;
                if (partner.logoDataUrl) { logoPreview.src = partner.logoDataUrl; }
            }
        }
        partnersTableBody.addEventListener('click', e => { const row = e.target.closest('tr'); if (row) selectRow(parseInt(row.dataset.id)); });
        logoInput.addEventListener('change', function() { if (this.files[0]) { readFileAsDataURL(this.files[0]).then(url => logoPreview.src = url); } });
        addPartnerBtn.addEventListener('click', () => { resetAndPrepareModal('add'); partnerModal.show(); });
        editPartnerBtn.addEventListener('click', () => { if (selectedPartnerId === null) return; const partner = partnersData.find(p => p.id === selectedPartnerId); resetAndPrepareModal('edit', partner); partnerModal.show(); });
        visitPartnerBtn.addEventListener('click', () => { if (selectedPartnerId === null) return; const partner = partnersData.find(p => p.id === selectedPartnerId); window.open(partner.link, '_blank'); });
        deletePartnerBtn.addEventListener('click', () => {
            if (selectedPartnerId === null) return;
            const partner = partnersData.find(p => p.id === selectedPartnerId);
            confirmationModalTitle.textContent = "Confirm Deletion";
            confirmationModalBody.innerHTML = `Are you sure you want to delete the partner: <strong>${partner.name}</strong>?`;
            confirmActionBtn.onclick = () => {
                partnersData = partnersData.filter(p => p.id !== selectedPartnerId);
                savePartnersData(partnersData);
                selectedPartnerId = null; renderTable(); updateFabState(); confirmationModal.hide();
            };
            confirmationModal.show();
        });
        savePartnerBtn.addEventListener('click', async () => {
            if (!partnerForm.checkValidity()) { partnerForm.classList.add('was-validated'); partnerForm.reportValidity(); return; }
            const mode = savePartnerBtn.dataset.mode;
            const name = document.getElementById('partner-name').value;
            const link = document.getElementById('partner-link').value;
            const logoFile = logoInput.files[0];
            let logoDataUrl = null;
            if(logoFile) logoDataUrl = await readFileAsDataURL(logoFile);

            if (mode === 'add') {
                partnersData.push({ id: nextPartnerId++, name, link, logoDataUrl });
            } else {
                const partner = partnersData.find(p => p.id === selectedPartnerId);
                partner.name = name;
                partner.link = link;
                if (logoDataUrl) partner.logoDataUrl = logoDataUrl;
            }
            savePartnersData(partnersData);
            renderTable(); partnerModal.hide();
        });
        renderTable(); updateFabState();
    })();

    // --- START: SCRIPT FOR FOOTER PAGE MANAGEMENT ---
    (function() {
        let selectedItemId = null;
        let localFooterData = loadFooterData();
        let nextItemId = (localFooterData.length > 0 ? Math.max(...localFooterData.map(i => i.id)) : 0) + 1;
        
        const footerTableBody = document.getElementById('footer-table-body');
        const addItemBtn = document.getElementById('add-footer-item-btn');
        const editItemBtn = document.getElementById('edit-footer-item-btn');
        const deleteItemBtn = document.getElementById('delete-footer-item-btn');
        const visitLinkBtn = document.getElementById('visit-footer-link-btn');
        const itemModal = new bootstrap.Modal(document.getElementById('footer-modal'));
        const itemForm = document.getElementById('footer-form');
        const itemModalTitle = document.getElementById('footer-modal-title');
        const saveItemBtn = document.getElementById('save-footer-item-btn');
        const itemLabelInput = document.getElementById('footer-label');

        function loadFooterData() {
            const savedData = localStorage.getItem('raisCmsFooterData');
            return savedData ? JSON.parse(savedData) : footerData;
        }

        function saveFooterData(data) {
            localStorage.setItem('raisCmsFooterData', JSON.stringify(data));
        }

        function renderTable() {
            footerTableBody.innerHTML = '';
            localFooterData.forEach(item => {
                const row = footerTableBody.insertRow(); row.dataset.id = item.id;
                if (item.id === selectedItemId) row.classList.add('selected');
                const typeDisplay = item.type === 'static' ? '<span class="badge bg-secondary">Static</span>' : '<span class="badge bg-info">Social</span>';
                row.innerHTML = `<td>${item.label}</td><td>${item.description}</td><td>${typeDisplay}</td>`;
            });
        }
        function updateFabState() {
            const selectedItem = localFooterData.find(item => item.id === selectedItemId);
            if (!selectedItem) { editItemBtn.disabled = true; deleteItemBtn.disabled = true; visitLinkBtn.disabled = true; } 
            else {
                editItemBtn.disabled = false;
                if (selectedItem.type === 'static') { deleteItemBtn.disabled = true; visitLinkBtn.disabled = true; } 
                else { deleteItemBtn.disabled = false; visitLinkBtn.disabled = false; }
            }
        }
        function selectRow(itemId) { selectedItemId = (selectedItemId === itemId) ? null : itemId; renderTable(); updateFabState(); }
        function resetAndPrepareModal(mode = 'add', item = null) {
            itemForm.reset(); itemForm.classList.remove('was-validated'); itemLabelInput.readOnly = false;
            if (mode === 'add') { itemModalTitle.textContent = 'Add Social Media Link'; saveItemBtn.textContent = 'Add Item'; saveItemBtn.dataset.mode = 'add'; } 
            else if (mode === 'edit' && item) {
                itemModalTitle.textContent = `Edit Item: ${item.label}`; saveItemBtn.textContent = 'Update Item'; saveItemBtn.dataset.mode = 'edit';
                itemLabelInput.value = item.label;
                document.getElementById('footer-description').value = item.description;
                if (item.type === 'static') itemLabelInput.readOnly = true;
            }
        }
        footerTableBody.addEventListener('click', e => { const row = e.target.closest('tr'); if (row) selectRow(parseInt(row.dataset.id)); });
        addItemBtn.addEventListener('click', () => { resetAndPrepareModal('add'); itemModal.show(); });
        editItemBtn.addEventListener('click', () => { if (selectedItemId === null) return; const item = localFooterData.find(i => i.id === selectedItemId); resetAndPrepareModal('edit', item); itemModal.show(); });
        visitLinkBtn.addEventListener('click', () => { if (selectedItemId === null) return; const item = localFooterData.find(i => i.id === selectedItemId); if (item && item.type !== 'static') window.open(item.description, '_blank'); });
        deleteItemBtn.addEventListener('click', () => {
            if (selectedItemId === null) return;
            const item = localFooterData.find(i => i.id === selectedItemId);
            if (item.type === 'static') return;
            confirmationModalTitle.textContent = "Confirm Deletion";
            confirmationModalBody.innerHTML = `Are you sure you want to delete the link for <strong>${item.label}</strong>?`;
            confirmActionBtn.onclick = () => {
                localFooterData = localFooterData.filter(i => i.id !== selectedItemId);
                saveFooterData(localFooterData);
                selectedItemId = null; renderTable(); updateFabState(); confirmationModal.hide();
            };
            confirmationModal.show();
        });
        saveItemBtn.addEventListener('click', () => {
            if (!itemForm.checkValidity()) { itemForm.classList.add('was-validated'); return; }
            const mode = saveItemBtn.dataset.mode;
            const label = itemLabelInput.value;
            const description = document.getElementById('footer-description').value;
            if (mode === 'add') { localFooterData.push({ id: nextItemId++, label, description, type: 'social' }); } 
            else { const item = localFooterData.find(i => i.id === selectedItemId); item.label = label; item.description = description; }
            saveFooterData(localFooterData);
            renderTable(); itemModal.hide();
        });
        renderTable(); updateFabState();
    })();
    
    // --- GLOBAL FAB VISIBILITY MANAGER ---
    (function() {
        function updateFabVisibility() {
            document.querySelectorAll('.fab-container').forEach(container => { container.style.display = 'none'; });
            const activeSection = document.querySelector('.content-section.active');
            if (activeSection) {
                const activeFabContainer = activeSection.querySelector('.fab-container');
                if (activeFabContainer) activeFabContainer.style.display = 'flex';
            }
        }
        navLinks.forEach(link => { link.addEventListener('click', () => setTimeout(updateFabVisibility, 50)); });
        updateFabVisibility();
    })();
});
    </script>
</body>
</html>