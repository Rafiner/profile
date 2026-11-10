<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Resume';
$education = $pdo->query('SELECT * FROM education ORDER BY start_year DESC')->fetchAll();
$skills    = $pdo->query('SELECT * FROM skills ORDER BY category, sort_order')->fetchAll();
$projects  = $pdo->query('SELECT * FROM projects ORDER BY completion_date DESC')->fetchAll();

// Fetch the resume URL from the database
$settings = get_settings($pdo);
$cloudinaryPdfUrl = $settings['resume_file'] ?? null; 

include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <h1 class="page-title">Resume / CV</h1>
  
  <div class="resume-actions">
    <?php if ($cloudinaryPdfUrl): ?>
      
      <!-- View Button: Opens in a new tab -->
      <a class="btn btn-ghost" href="<?= e($cloudinaryPdfUrl) ?>" target="_blank" rel="noopener">
        <i class="fa-solid fa-eye"></i> View
      </a>
    <?php else: ?>
      <p class="empty">Resume file not uploaded yet.</p>
    <?php endif; ?>
  </div>

  <div class="resume-grid">
    <div class="card">
      <h3>Education</h3>
      <?php foreach ($education as $ed): ?>
        <div class="resume-item"><strong><?= e($ed['degree']) ?></strong><br>
        <span class="muted"><?= e($ed['school_name']) ?> • <?= (int)$ed['start_year'] ?>–<?= e($ed['end_year']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <div class="card"><h3>Skills</h3>
      <div class="tech-tags"><?php foreach ($skills as $s): ?><span class="tag"><?= e($s['name']) ?></span><?php endforeach; ?></div>
    </div>
    <div class="card"><h3>Projects</h3>
      <?php foreach ($projects as $p): ?>
        <div class="resume-item"><strong><?= e($p['title']) ?></strong><br><span class="muted"><?= e($p['technologies']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>