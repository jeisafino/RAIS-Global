<?php
// This placeholder will be replaced with the actual blog ID when the file is generated.
$blog_id = BLOG_ID_PLACEHOLDER;

// Establish database connection
require_once __DIR__ . '/../db_connect.php';

// Fetch Blog Details and Sections from the database...
$blog_stmt = $conn->prepare("SELECT title, author, publish_date, hero_media_path FROM blogs WHERE id = ?");
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
// Format publish date
$publish_date_formatted = (new DateTime($blog['publish_date']))->format('M j, Y');
$hero_path = "../" . htmlspecialchars($blog['hero_media_path']); // Prepend ../ to path
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $page_title; ?></title>
  <link rel="icon" href="../img/logoulit.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #0C470C;
      --secondary-color: #F8F9FA; /* Light gray for backgrounds */
      --text-color: #333333;
      --heading-color: #023621;
      --light-gray: #E0E0E0;
      --font-family: 'Poppins', sans-serif;
      --heading-font: 'Archivo Black', sans-serif;
      --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
      --border-radius: 12px;
    }

    body {
      font-family: var(--font-family);
      line-height: 1.7;
      margin: 0;
      padding: 0;
      background-color: var(--secondary-color); /* Light gray background */
      color: var(--text-color);
    }

    .btn-back {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1001;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      background-color: var(--primary-color);
      color: white;
      border-radius: 50%;
      text-decoration: none;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
    }

    .btn-back:hover {
      background-color: var(--heading-color);
      transform: scale(1.1);
    }

    .btn-back i {
      font-size: 1.2rem;
    }

    main {
      width: 90%;
      max-width: 800px;
      margin: 2rem auto;
    }

    /* Main blog post container styling */
    .blog-post-wrapper {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: 0 10px 30px rgba(0,0,0,0.08); /* Stronger shadow for the main container */
      padding: 2.5rem; /* Increased padding */
      margin-top: 2rem;
      border: 1px solid #e9ecef; /* Subtle border */
      /* *** START: ADD THIS LINE *** */
      border-left: 5px solid var(--primary-color); /* Green vertical line on the left */
    }

    .blog-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .blog-title {
      font-family: var(--heading-font);
      font-size: 2.5rem; /* Slightly adjusted size */
      color: var(--heading-color);
      margin-bottom: 0.5rem;
      font-weight: 700;
      line-height: 1.2;
    }

    .blog-meta {
        color: #6c757d; /* Muted gray for author/date */
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
    .blog-meta .author {
        font-weight: 600;
    }

    .hero-media-container {
        width: 100%;
        margin-bottom: 2rem;
        overflow: hidden; /* Ensures rounded corners are applied */
        border-radius: var(--border-radius);
    }

    .hero-media-container img, .hero-media-container video {
        width: 100%;
        height: auto;
        display: block;
        border-radius: var(--border-radius); /* Apply border-radius to media */
    }

    .blog-post p {
      font-size: 1rem;
      margin-bottom: 1.5rem;
      color: var(--text-color);
    }

    /* Section styling */
    .blog-section {
        background-color: var(--secondary-color); /* Light gray background for sections */
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem; /* Spacing between sections */
        box-shadow: var(--card-shadow); /* Card-like shadow for sections */
        border: 1px solid var(--light-gray);
    }

    .blog-section-title {
        font-family: var(--heading-font);
        font-size: 1.6rem;
        color: var(--primary-color); /* Primary color for section titles */
        margin-top: 0; /* Remove top margin from h2 */
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--light-gray); /* Subtle separator */
    }

    .section-media-container {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
        border-radius: var(--border-radius);
    }

    .section-media-container img, .section-media-container video {
        width: 100%;
        height: auto;
        display: block;
        border-radius: var(--border-radius);
    }

    /* Event Highlights section specific styling (if applicable, though general section is used) */
    .event-highlights-section {
        background-color: var(--secondary-color);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: var(--card-shadow);
    }
    .event-highlights-section h2 {
        font-family: var(--heading-font);
        color: var(--primary-color);
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
  </style>
</head>

<body>
  <a href="../blogs.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
  <main>
    <article class="blog-post-wrapper">
      <div class="blog-header">
        <h1 class="blog-title"><?php echo $blog_title; ?></h1>
        <p class="blog-meta">Written By: <span class="author"><?php echo $author_name; ?></span> <span class="separator">|</span> <?php echo $publish_date_formatted; ?></p>
      </div>

      <?php if (!empty($hero_path)): ?>
        <div class="hero-media-container">
            <?php
            $fileExtension = pathinfo($hero_path, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileExtension), ['mp4', 'webm', 'ogg'])) {
                echo '<video controls autoplay loop muted><source src="' . $hero_path . '" type="video/' . $fileExtension . '">Your browser does not support the video tag.</video>';
            } else {
                echo '<img src="' . $hero_path . '" alt="' . $blog_title . '">';
            }
            ?>
        </div>
      <?php endif; ?>

      <?php if (isset($blog['summary']) && !empty($blog['summary'])): ?>
        <p class="lead text-center mb-4"><?php echo nl2br(htmlspecialchars($blog['summary'])); ?></p>
      <?php endif; ?>

      <?php
      $hasEventHighlights = false;
      foreach ($sections as $section) {
          if (str_contains(strtolower($section['title']), 'event highlight')) { // Check for "Event Highlights" in title
              $hasEventHighlights = true;
              break;
          }
      }
      ?>

      <?php if ($hasEventHighlights): ?>
        <section class="event-highlights-section">
            <h2>Event Highlights</h2>
            <?php foreach ($sections as $section): ?>
                <?php if (str_contains(strtolower($section['title']), 'event highlight')): ?>
                    <div class="blog-section">
                        <?php if (!empty($section['title'])): ?>
                            <h3 class="blog-section-title"><?php echo htmlspecialchars($section['title']); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (!empty($section['media_path'])): ?>
                            <div class="section-media-container">
                                <?php
                                $sectionMediaExtension = pathinfo($section['media_path'], PATHINFO_EXTENSION);
                                if (in_array(strtolower($sectionMediaExtension), ['mp4', 'webm', 'ogg'])) {
                                    echo '<video controls autoplay loop muted><source src="../' . htmlspecialchars($section['media_path']) . '" type="video/' . $sectionMediaExtension . '">Your browser does not support the video tag.</video>';
                                } else {
                                    echo '<img src="../' . htmlspecialchars($section['media_path']) . '" alt="' . htmlspecialchars($section['title']) . '">';
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <p><?php echo nl2br(htmlspecialchars($section['content'])); ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
      <?php else: ?>
        <?php foreach ($sections as $section): ?>
            <section class="blog-section">
                <?php if (!empty($section['title'])): ?>
                    <h3 class="blog-section-title"><?php echo htmlspecialchars($section['title']); ?></h3>
                <?php endif; ?>
                
                <?php if (!empty($section['media_path'])): ?>
                    <div class="section-media-container">
                        <?php
                        $sectionMediaExtension = pathinfo($section['media_path'], PATHINFO_EXTENSION);
                        if (in_array(strtolower($sectionMediaExtension), ['mp4', 'webm', 'ogg'])) {
                            echo '<video controls autoplay loop muted><source src="../' . htmlspecialchars($section['media_path']) . '" type="video/' . $sectionMediaExtension . '">Your browser does not support the video tag.</video>';
                        } else {
                            echo '<img src="../' . htmlspecialchars($section['media_path']) . '" alt="' . htmlspecialchars($section['title']) . '">';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <p><?php echo nl2br(htmlspecialchars($section['content'])); ?></p>
            </section>
        <?php endforeach; ?>
      <?php endif; ?>

    </article>
  </main>
  <?php include '../footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>