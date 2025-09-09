<?php
// This placeholder will be replaced with the actual exam ID.
$exam_id = EXAM_ID_PLACEHOLDER;

require_once __DIR__ . '/db_connect.php';

// Fetch Exam Details from the database
$stmt = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Exam Not Found</h1>";
    exit;
}

// Fetch related sections
$formats = $conn->query("SELECT * FROM exam_formats WHERE exam_id = $exam_id ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
$choice_cards = $conn->query("SELECT * FROM exam_choice_cards WHERE exam_id = $exam_id ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
$infocards = $conn->query("SELECT * FROM exam_infocards WHERE exam_id = $exam_id ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);
$faqs = $conn->query("SELECT * FROM exam_faqs WHERE exam_id = $exam_id ORDER BY display_order ASC")->fetch_all(MYSQLI_ASSOC);

$page_title = htmlspecialchars($exam['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="img/logoulit.png">
  <link rel="stylesheet" href="css/exam-style.css"> </head>
<body class="text-black">
  <a href="index.php" class="btn-back" aria-label="Go Back">
    <i class="bi-arrow-left-short"></i>
  </a>
  <header class="position-relative">
    <div class="position-absolute top-0 end-0 p-3 p-md-4">
      <img src="img/logo_ielts.png" class="img-fluid header-logo" alt="IELTS Logo">
    </div>
    <div class="header-bg-img" style="background-image: url('<?php echo htmlspecialchars($exam['hero_media_path']); ?>');"></div>
    <div class="container d-flex flex-column justify-content-end" style="min-height: 80vh;">
      <div class="text-start mb-5 pb-4">
          <h1 class="display-4 fw-bold text-white header-text-shadow"><?php echo htmlspecialchars($exam['name']); ?></h1>
          <p class="lead mt-3 text-white header-text-shadow"><?php echo htmlspecialchars($exam['description']); ?></p>
          <a href="form.php" class="btn px-4 py-2 text-white" style="background-color: #0C470C;">Book Now</a>
      </div>
    </div>
  </header>
  <main>
    <?php if (!empty($exam['about_content'])): ?>
    <section id="about" class="py-5">
        <div class="container"><div class="row justify-content-center align-items-center g-5"><div class="col-lg-7"><h2 class="display-5 fw-bold">About <?php echo htmlspecialchars($exam['name']); ?></h2><p class="mt-4 fs-5"><?php echo nl2br(htmlspecialchars($exam['about_content'])); ?></p></div><?php if (!empty($exam['about_media_path'])): ?><div class="col-lg-5"><img src="<?php echo htmlspecialchars($exam['about_media_path']); ?>" class="img-fluid rounded shadow-lg" alt="About <?php echo htmlspecialchars($exam['name']); ?>"></div><?php endif; ?></div></div>
    </section>
    <?php endif; ?>

    <?php if (!empty($formats)): ?>
    <section id="test-format" class="py-5 bg-light">
        <div class="container"><div class="text-center mb-5"><h2 class="display-5 fw-bold">Test Format</h2></div><div class="row g-4 justify-content-center"><?php foreach ($formats as $format): ?><div class="col-lg-3 col-md-6"><div class="bg-white rounded-4 text-center h-100 p-4 shadow-sm"><i class="<?php echo htmlspecialchars($format['icon_class']); ?> display-3" style="color: #0C470C;"></i><h3 class="fs-4 fw-bold mt-3"><?php echo htmlspecialchars($format['title']); ?></h3><p class="mt-2"><?php echo htmlspecialchars($format['description']); ?></p></div></div><?php endforeach; ?></div></div>
    </section>
    <?php endif; ?>

<?php if (!empty($choice_cards)): ?>
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-5 display-5 fw-bold">Why should you choose <?php echo htmlspecialchars($exam['name']); ?>?</h2>
    <div class="row g-4">
      <?php foreach ($choice_cards as $card): ?>
      <div class="col-lg-4 col-md-6">
        <div class="card h-100 shadow-sm border-0">
          <img src="<?php echo htmlspecialchars($card['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($card['title']); ?>" style="height: 200px; object-fit: cover;">
          <div class="card-body d-flex flex-column p-4">
            <h4 class="card-title fw-bold"><?php echo htmlspecialchars($card['title']); ?></h4>
            <p class="card-text"><?php echo htmlspecialchars($card['description']); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

    <?php if (!empty($infocards)): ?>
<section id="info-cards" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">How does <?php echo htmlspecialchars($exam['name']); ?> work?</h2>
            <?php if (!empty($exam['infocards_intro'])): ?>
                <p class="mx-auto fs-5 my-4 col-lg-9"><?php echo nl2br(htmlspecialchars($exam['infocards_intro'])); ?></p>
            <?php endif; ?>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($infocards as $card): ?>
            <div class="col-lg-5 col-md-8">
                <div class="bg-white p-4 p-md-5 rounded-4 text-center h-100 border shadow-sm">
                    <h3 class="fw-bold fs-4 mb-3"><?php echo htmlspecialchars($card['title']); ?></h3>
                    <p class="mb-0"><?php echo htmlspecialchars($card['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

    <?php if (!empty($faqs)): ?>
<section id="faqs" class="py-5 bg-light">
    <div class="container my-md-4">
        <div class="text-center mb-5"><h2 class="fw-bold">Frequently Asked Questions</h2></div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item mb-2 border-0 rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed p-3 rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
</main>

  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>