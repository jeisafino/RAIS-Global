<?php
// This placeholder will be replaced with the actual service ID when the file is generated.
$service_id = 7;

// --- Database Fetch Logic ---
// In a real application, you'd include your database connection file.
// For this example, we'll simulate the connection and data fetch.
$servername = "localhost";
$username = "your_db_username"; // Replace with your DB credentials
$password = "your_db_password"; // Replace with your DB credentials
$dbname = "your_db_name";     // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Service Details
$service_stmt = $conn->prepare("SELECT name, description, hero_media_path FROM services WHERE id = ?");
$service_stmt->bind_param("i", $service_id);
$service_stmt->execute();
$service_result = $service_stmt->get_result();
$service = $service_result->fetch_assoc();

// Fetch Service Sections
$sections_stmt = $conn->prepare("SELECT title, content, media_path FROM service_sections WHERE service_id = ? ORDER BY display_order ASC");
$sections_stmt->bind_param("i", $service_id);
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();
$sections = [];
while ($row = $sections_result->fetch_assoc()) {
    $sections[] = $row;
}

$conn->close();

if (!$service) {
    // Handle service not found, e.g., show a 404 page
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Service Not Found</h1>";
    exit;
}

// --- Dynamic HTML Generation ---
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars(\$service['name']); ?></title>
  <link rel="icon" href="../img/logoulit.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    /* All the CSS from visitpermit.php goes here. */
    /* For brevity, this is omitted. Copy and paste the <style> block from visitpermit.php */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    header { position: relative; height: 100vh; background: url("../{$hero_path}") no-repeat center center/cover; display: flex; justify-content: center; align-items: center; color: white; text-align: center; }    .overlay-box { position: absolute; top: 0; left: 0; height: 100%; width: 100%; background-color: rgba(0, 0, 0, 0.4); z-index: 1; }
    .logo { position: absolute; top: 20px; left: 20px; z-index: 2; }
    .logo img { width: 150px; transition: transform 0.3s ease; }
    .logo a:hover img { transform: scale(1.1); }
    .hero-content { position: relative; z-index: 2; max-width: 750px; padding: 20px; }
    .hero-content h1 { font-size: 3.2rem; font-weight: 700; margin-bottom: 20px; text-shadow: 2px 2px 5px #000; }
    .hero-content p { font-size: 1.2rem; line-height: 1.7; margin-bottom: 30px; }
    .hero-btn { background-color: #0C470C; color: white; padding: 10px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease-in-out; }
    .hero-btn:hover { background-color: #167e16; transform: scale(1.05) translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .nav-tabs { border-bottom: none; }
    .nav-tabs .nav-link { color: #444; font-weight: 600; border-radius: 0; border: 0; transition: all 0.3s ease; }
    .nav-tabs .nav-link:not(.active):hover { color: #0C470C; transform: translateY(-3px); }
    .nav-tabs .nav-link.active { background-color: #0C470C; color: white; }
    .tab-content { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05); }
    .tab-image { max-height: 400px; width: 100%; object-fit: contain; }
  </style>
</head>
<body>
  <header>
    <div class="overlay-box"></div>
    <div class="logo">
      <a href="../index.php"><img src="../img/logo1.png" alt="logo" /></a>
    </div>
    <div class="hero-content text-white text-center">
      <h1><?php echo htmlspecialchars(\$service['name']); ?></h1>
      <p><?php echo htmlspecialchars(\$service['description']); ?></p>
      <a href="../form.html" class="btn hero-btn">Apply Now</a>
    </div>
  </header>
  <main>
    <section class="py-5 my-5" id="info">
      <div class="container px-4 px-md-5">
        <ul class="nav nav-tabs mb-5 justify-content-center border-0" id="tabMenu" role="tablist">
HTML;

foreach (\$sections as \$index => \$section) {
    \$activeClass = (\$index == 0) ? 'active' : '';
    echo <<<HTML
          <li class="nav-item" role="presentation">
            <button class="nav-link {$activeClass}" data-bs-toggle="tab" data-bs-target="#tab-{$index}" type="button" role="tab"><?php echo htmlspecialchars(\$section['title']); ?></button>
          </li>
HTML;
}

echo <<<HTML
        </ul>
        <div class="tab-content" style="font-size: 18px; line-height: 1.8; text-align: justify;">
HTML;

foreach (\$sections as \$index => \$section) {
    \$activeClass = (\$index == 0) ? 'show active' : '';
    \$imageHtml = \$section['media_path'] ? "<div class='col-md-6 text-center'><img src='../" . htmlspecialchars(\$section['media_path']) . "' alt='" . htmlspecialchars(\$section['title']) . "' class='img-fluid rounded tab-image' /></div>" : "";
    \$textColClass = \$section['media_path'] ? 'col-md-6' : 'col-md-12';

    echo <<<HTML
          <div class="tab-pane fade {$activeClass}" id="tab-{$index}" role="tabpanel">
            <div class="row align-items-center g-4">
              <div class="{$textColClass} ps-4">
                <p>
                  <?php echo nl2br(htmlspecialchars(\$section['content'])); ?>
                </p>
              </div>
              {$imageHtml}
            </div>
          </div>
HTML;
}

echo <<<HTML
        </div>
      </div>
    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
?>