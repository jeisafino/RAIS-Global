<?php
// This placeholder will be replaced with the actual blog ID when the file is generated.
$blog_id = BLOG_ID_PLACEHOLDER;

// Establish database connection
require_once __DIR__ . '/../db_connect.php';

// Fetch Blog Details and Sections from the database...
$blog_stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$blog_stmt->bind_param("i", $blog_id);
$blog_stmt->execute();
$blog_result = $blog_stmt->get_result();
$blog = $blog_result->fetch_assoc();

$sections_stmt = $conn->prepare("SELECT title, content, media_path FROM blog_sections WHERE blog_id = ? ORDER BY display_order ASC");
$sections_stmt->bind_param("i", $blog_id);
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();
$sections = [];
while ($row = $sections_result->fetch_assoc()) {
    $sections[] = $row;
}

if (!$blog) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Blog Not Found</h1>";
    exit;
}

// Prepare variables safely for the template
$page_title = "RCIS Blog | " . htmlspecialchars($blog['title']);
$blog_title = htmlspecialchars($blog['title']);
$author_name = htmlspecialchars($blog['author']);
$publish_date_formatted = !empty($blog['publish_date']) ? (new DateTime($blog['publish_date']))->format('M j, Y') : '';
$hero_path = !empty($blog['hero_media_path']) ? "../" . htmlspecialchars($blog['hero_media_path']) : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $page_title; ?></title>
  <link rel="icon" href="../img/logoulit.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #0C470C;
      --secondary-color: #F8F9FA;
      --text-color: #333333;
      --heading-color: #023621;
      --light-gray: #E0E0E0;
      --font-family: 'Poppins', sans-serif;
      --heading-font: 'Archivo Black', sans-serif;
      --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
      --border-radius: 12px;
    }
    body { font-family: var(--font-family); line-height: 1.7; margin: 0; padding: 0; background-color: var(--secondary-color); color: var(--text-color); }
    .btn-back { position: fixed; top: 20px; left: 20px; z-index: 1001; display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background-color: var(--primary-color); color: white; border-radius: 50%; text-decoration: none; box-shadow: var(--card-shadow); transition: all 0.3s ease; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease;}
    .btn-back.visible { opacity: 1; visibility: visible;}
    .btn-back:hover { background-color: var(--heading-color); transform: scale(1.1); }
    .btn-back i { font-size: 1.2rem; }
    main { width: 90%; max-width: 800px; margin: 2rem auto; }
    .blog-post-wrapper { background: white; border-radius: var(--border-radius); box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2.5rem; margin-top: 2rem; border: 1px solid #e9ecef; border-left: 5px solid var(--primary-color); }
    .blog-header { text-align: center; margin-bottom: 2rem; }
    .blog-title { font-family: var(--heading-font); font-size: 2.5rem; color: var(--heading-color); margin-bottom: 0.5rem; font-weight: 700; line-height: 1.2; }
    .blog-meta { color: #6c757d; font-size: 0.95rem; margin-bottom: 1.5rem; }
    .blog-meta .author { font-weight: 600; }
    .hero-media-container { width: 100%; margin-bottom: 2rem; overflow: hidden; border-radius: var(--border-radius); }
    .hero-media-container img, .hero-media-container video { width: 100%; height: auto; display: block; border-radius: var(--border-radius); }
    .blog-post p { font-size: 1rem; margin-bottom: 1.5rem; color: var(--text-color); }
    .blog-section { background-color: var(--secondary-color); border-radius: var(--border-radius); padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--card-shadow); border: 1px solid var(--light-gray); }
    .blog-section-title { font-family: var(--heading-font); font-size: 1.6rem; color: var(--primary-color); margin-top: 0; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--light-gray); }
    .section-media-container { margin-top: 1.5rem; margin-bottom: 1.5rem; overflow: hidden; border-radius: var(--border-radius); }
    .section-media-container img, .section-media-container video { width: 100%; height: auto; display: block; border-radius: var(--border-radius); }
    .event-highlights-section { background-color: transparent; border-radius: var(--border-radius); padding: 0; margin-top: 2rem; box-shadow: none; border: none; }
    .event-highlights-section > h2 { font-family: var(--heading-font); color: var(--heading-color); font-size: 1.8rem; margin-bottom: 1.5rem; text-align: center; }
    header {
      background: var(--surface-color);
      padding: 1rem 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--light-gray);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      top: 0;
      z-index: 1000;
    }

    .logo-image {
      max-height: 65px; /* Increased from 55px */
      width: auto;
    }

    header nav a {
      margin: 0 1rem;
      text-decoration: none;
      color: var(--primary-color);
      font-weight: 600;
      transition: color 0.3s ease;
    }

    header nav a:hover {
      color: var(--heading-color);
    }

    .menu-toggle {
      display: none;
      flex-direction: column;
      cursor: pointer;
    }

    .menu-toggle .bar {
      width: 25px;
      height: 3px;
      background-color: var(--primary-color);
      margin: 4px 0;
      transition: 0.4s;
    }
  </style>
</head>
<body>
  <header>
    <div>
      <img src="img/logo.png" alt="RAIS Events Logo" class="logo-image">
    </div>
    <div class="menu-toggle" id="mobile-menu">
      <div class="bar"></div>
      <div class="bar"></div>
      <div class="bar"></div>
    </div>
    <nav id="nav-links">
      <a href="#map">Event</a>
      <a href="#blogs">Blogs</a>
      <a href="#footer-placeholder">Contact</a>
    </nav>
  </header>
  <a href="../blogs.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
  <main>
    <article class="blog-post-wrapper">
      <div class="blog-header">
        <h1 class="blog-title"><?php echo $blog_title; ?></h1>
        <p class="blog-meta">Written By: <span class="author"><?php echo $author_name; ?></span><?php if($publish_date_formatted) echo ' | ' . $publish_date_formatted; ?></p>
      </div>

      <?php if (!empty($hero_path)): ?>
        <div class="hero-media-container">
            <img src="<?php echo $hero_path; ?>" alt="<?php echo $blog_title; ?>">
        </div>
      <?php endif; ?>

      <?php if (!empty($blog['summary'])): ?>
          <p class="lead"><?php echo nl2br(htmlspecialchars($blog['summary'])); ?></p>
          <hr>
      <?php endif; ?>

      <?php
      $eventSections = [];
      $generalSections = [];
      foreach ($sections as $section) {
          if (stripos($section['title'], 'event highlight') !== false) {
              $eventSections[] = $section;
          } else {
              $generalSections[] = $section;
          }
      }

      foreach ($generalSections as $section): ?>
          <section class="blog-section">
              <?php if (!empty($section['title'])): ?>
                  <h3 class="blog-section-title"><?php echo htmlspecialchars($section['title']); ?></h3>
              <?php endif; ?>
              
              <?php if (!empty($section['media_path'])): ?>
                  <div class="section-media-container">
                      <img src="../<?php echo htmlspecialchars($section['media_path']); ?>" alt="<?php echo htmlspecialchars($section['title']); ?>">
                  </div>
              <?php endif; ?>

              <div><?php echo nl2br(htmlspecialchars($section['content'])); ?></div>
          </section>
      <?php endforeach; ?>

      <?php if (!empty($eventSections)): ?>
        <section class="event-highlights-section">
            <h2>Event Highlights</h2>
            <?php foreach ($eventSections as $section): ?>
                <div class="blog-section">
                    <h3 class="blog-section-title"><?php echo htmlspecialchars($section['title']); ?></h3>
                    <?php if (!empty($section['media_path'])): ?>
                        <div class="section-media-container">
                           <img src="../<?php echo htmlspecialchars($section['media_path']); ?>" alt="<?php echo htmlspecialchars($section['title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div><?php echo nl2br(htmlspecialchars($section['content'])); ?></div>
                </div>
            <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </article>
  </main>
  <script>
    // Back button visibility toggle
    const backButton = document.querySelector('.btn-back');
    const showButtonThreshold = 200; // Show button after scrolling 200px

    window.addEventListener('scroll', () => {
      if (window.scrollY > showButtonThreshold) {
        backButton.classList.add('visible');
      } else {
        backButton.classList.remove('visible');
      }
    });
  </script>
  <?php include '../footer.php'; ?>
</body>
</html>