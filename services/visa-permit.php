<?php
// This placeholder will be replaced with the actual service ID when the file is generated.
$service_id = 8;

// Establish database connection
require_once __DIR__ . '/../db_connect.php';

// Fetch Service Details and Sections from the database...
$service_stmt = $conn->prepare("SELECT name, description, hero_media_path FROM services WHERE id = ?");
$service_stmt->bind_param("i", $service_id);
$service_stmt->execute();
$service_result = $service_stmt->get_result();
$service = $service_result->fetch_assoc();

$sections_stmt = $conn->prepare("SELECT title, content, media_path FROM service_sections WHERE service_id = ? ORDER BY display_order ASC");
$sections_stmt->bind_param("i", $service_id);
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();
$sections = [];
while ($row = $sections_result->fetch_assoc()) {
    $sections[] = $row;
}

if (!$service) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Service Not Found</h1>";
    exit;
}

// Prepare variables safely for the template
$page_title = htmlspecialchars($service['name']);
$hero_path = htmlspecialchars($service['hero_media_path']);
$hero_title = htmlspecialchars($service['name']);
$hero_desc = htmlspecialchars($service['description']);

// --- Dynamic HTML Generation ---
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{$page_title}</title>
  <link rel="icon" href="../img/logoulit.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  
  <link rel="stylesheet" href="../css/service-style.css" />

  </head>
<body>
  <header style="background: url('../{$hero_path}') no-repeat center center/cover;">
    <div class="overlay-box"></div>
    <div class="logo">
      <a href="../index.php"><img src="../img/logo1.png" alt="logo" /></a>
    </div>
    <div class="hero-content text-white text-center">
      <h1>{$hero_title}</h1>
      <p>{$hero_desc}</p>
      <a href="../form.html" class="btn hero-btn">Apply Now</a>
    </div>
  </header>
  <main>
    <section class="py-5 my-5" id="info">
      <div class="container fixed-container px-4 px-md-5">
        <ul class="nav nav-tabs mb-5 justify-content-center border-0" id="tabMenu" role="tablist">
HTML;

// Loop for the navigation tabs
foreach ($sections as $index => $section) {
    $activeClass = ($index == 0) ? 'active' : '';
    $tab_title = htmlspecialchars($section['title']);
    echo <<<HTML
          <li class="nav-item" role="presentation">
            <button class="nav-link {$activeClass}" data-bs-toggle="tab" data-bs-target="#tab-{$index}" type="button" role="tab">{$tab_title}</button>
          </li>
HTML;
}

echo <<<HTML
        </ul>
        <div class="tab-content" style="font-size: 18px; line-height: 1.8; text-align: justify;">
HTML;

// Loop for the tab content panes
foreach ($sections as $index => $section) {
    $activeClass = ($index == 0) ? 'show active' : '';
    $section_title = htmlspecialchars($section['title']);
    $section_content = nl2br(htmlspecialchars($section['content']));
    $imageHtml = $section['media_path'] ? "<div class='col-md-6 text-center'><img src='../" . htmlspecialchars($section['media_path']) . "' alt='{$section_title}' class='img-fluid rounded tab-image' /></div>" : "";
    $textColClass = $section['media_path'] ? 'col-md-6' : 'col-md-12';

    echo <<<HTML
          <div class="tab-pane fade {$activeClass}" id="tab-{$index}" role="tabpanel">
            <div class="row align-items-center g-4">
              <div class="{$textColClass} ps-4">
                <p>{$section_content}</p>
              </div>
              {$imageHtml}
            </div>
          </div>
HTML;
}

echo <<<HTML
        </div>
        <div class="tab-navigation-buttons d-lg-none d-flex justify-content-center gap-3 mt-3">
          <button id="prevBtn" class="btn rounded-circle shadow-sm nav-btn"><i class="bi bi-chevron-left fs-4"></i></button>
          <button id="nextBtn" class="btn rounded-circle shadow-sm nav-btn"><i class="bi bi-chevron-right fs-4"></i></button>
        </div>
      </div>
    </section>
  </main>
HTML;

include '../footer.php';

echo <<<HTML
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const tabs = document.querySelectorAll('#tabMenu .nav-link');
      const tabList = Array.from(tabs);
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      if(prevBtn && nextBtn) {
          function updateButtons() {
            const activeTab = document.querySelector('#tabMenu .nav-link.active');
            const activeIndex = tabList.indexOf(activeTab);
            prevBtn.disabled = activeIndex === 0;
            nextBtn.disabled = activeIndex === tabList.length - 1;
          }
          prevBtn.addEventListener('click', () => {
            const activeTab = document.querySelector('#tabMenu .nav-link.active');
            const activeIndex = tabList.indexOf(activeTab);
            if (activeIndex > 0) tabList[activeIndex - 1].click();
          });
          nextBtn.addEventListener('click', () => {
            const activeTab = document.querySelector('#tabMenu .nav-link.active');
            const activeIndex = tabList.indexOf(activeTab);
            if (activeIndex < tabList.length - 1) tabList[activeIndex + 1].click();
          });
          tabs.forEach(tab => tab.addEventListener('shown.bs.tab', updateButtons));
          updateButtons();
      }
    });
  </script>
</body>
</html>
HTML;

?>