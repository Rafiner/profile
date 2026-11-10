<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About';
$education = $pdo->query('SELECT * FROM education ORDER BY start_year DESC')->fetchAll();
$certificates = $pdo->query('SELECT * FROM certificates ORDER BY date_issued DESC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <h1 class="page-title" data-aos="fade-up">About Me</h1>
  
  <!-- Added data-aos here for the animation -->
  <div class="about-grid" data-aos="fade-up" data-aos-delay="100">
    <div class="card"><h3>Introduction</h3><p><?= nl2br(e($profile['about'])) ?></p></div>
    <div class="card"><h3>Career Goals</h3><p><?= nl2br(e($profile['career_goals'])) ?></p></div>
    <div class="card"><h3>Interests</h3><p><?= nl2br(e($profile['interests'])) ?></p></div>
    <div class="card"><h3>Personal Statement</h3><p><?= nl2br(e($profile['personal_statement'])) ?></p></div>
  </div>

  <h2 class="section-title" data-aos="fade-up">Education</h2>
  
  <!-- Added data-aos here for the animation -->
  <div class="timeline" data-aos="fade-up" data-aos-delay="100">
    <?php foreach ($education as $ed): ?>
      <div class="timeline-item card">
        <span class="time"><?= (int)$ed['start_year'] ?> – <?= e($ed['end_year']) ?></span>
        <h3><?= e($ed['degree']) ?></h3>
        <p class="muted"><?= e($ed['school_name']) ?> • <?= e($ed['year_level']) ?></p>
        <p><?= nl2br(e($ed['coursework'])) ?></p>
      </div>
    <?php endforeach; if (!$education): ?><p class="empty">No education records yet.</p><?php endif; ?>
  </div>

  <h2 class="section-title" data-aos="fade-up">Certificates & Achievements</h2>
  
  <!-- Added data-aos here for the animation -->
  <div class="projects-grid" data-aos="fade-up" data-aos-delay="100">
    <?php foreach ($certificates as $c): ?>
      <div class="card">
        <?php if ($c['image']): ?><img src="<?= e($c['image']) ?>" style="width:100%;border-radius:8px;margin-bottom:12px;"><?php endif; ?>
        <h3><?= e($c['title']) ?></h3>
        <p class="muted"><?= e($c['organization']) ?> • <?= $c['date_issued'] ? date('M Y', strtotime($c['date_issued'])) : '' ?></p>
        <?php if ($c['verification_link']): ?><a href="<?= e($c['verification_link']) ?>" target="_blank" class="btn btn-small">Verify <i class="fa-solid fa-arrow-up-right-from-square"></i></a><?php endif; ?>
      </div>
    <?php endforeach; if (!$certificates): ?><p class="empty">No certificates added yet.</p><?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>