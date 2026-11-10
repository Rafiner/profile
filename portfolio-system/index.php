<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$skills    = $pdo->query('SELECT * FROM skills ORDER BY sort_order, id')->fetchAll();
$featured  = $pdo->query('SELECT * FROM projects WHERE featured=1 ORDER BY completion_date DESC LIMIT 3')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <span class="pill">Hello, I'm</span>
      <h1><?= e($profile['full_name']) ?></h1>
      <h2 class="typing"><span id="typed"><?= e($profile['title']) ?></span><span class="cursor">|</span></h2>
      <p class="lead"><?= e($profile['intro']) ?></p>
      <div class="socials">
        <?php foreach ($socials as $s): ?>
          <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['platform']) ?>"><i class="<?= e($s['icon']) ?>"></i></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="hero-image">
      <?php if (!empty($profile['profile_image'])): ?>
        <img src="<?= e($profile['profile_image']) ?>" alt="<?= e($profile['full_name']) ?>">
      <?php else: ?>
        <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title" data-aos="fade-up">Skills & Technologies</h2>
  
  <!-- Added data-aos here for the animation -->
  <div class="skills-grid-new" data-aos="fade-up" data-aos-delay="100">
    <?php foreach ($skills as $it): ?>
      <div class="skill-card-new">
        <div class="skill-icon-wrapper">
          <?php 
            $icon = trim($it['icon'] ?? '');
            $isImage = str_starts_with($icon, 'http') || preg_match('/\.(png|jpg|jpeg|svg|webp|gif)$/i', $icon);
          ?>
          
          <?php if ($isImage): ?>
            <img src="<?= str_starts_with($icon, 'http') ? e($icon) : 'assets/uploads/' . e($icon) ?>" alt="<?= e($it['name']) ?>">
          <?php elseif ($icon === ''): ?>
            <i class="fa-solid fa-code"></i>
          <?php else: ?>
            <i class="<?= e($icon) ?>"></i>
          <?php endif; ?>
        </div>
        
        <h3><?= e($it['name']) ?></h3>
        <span class="skill-cat"><?= e($it['category']) ?></span>
        
        <div class="skill-bar-container">
          <div class="skill-bar" style="width:<?= (int)$it['proficiency'] ?>%"></div>
        </div>
        <span class="pct"><?= (int)$it['proficiency'] ?>%</span>
      </div>
    <?php endforeach; if (!$skills): ?>
      <p class="empty">No skills added yet.</p>
    <?php endif; ?>
  </div>
</section>

<?php if ($featured): ?>
<section class="section container">
  <h2 class="section-title" data-aos="fade-up">Featured Projects</h2>
  
  <!-- Added data-aos here for the animation -->
  <div class="projects-grid" data-aos="fade-up" data-aos-delay="100">
    <?php foreach ($featured as $p): ?>
      <article class="card project-card">
        <div class="thumb">
          <?php if ($p['thumbnail']): ?>
            <img src="<?= e($p['thumbnail']) ?>" alt="<?= e($p['title']) ?>">
          <?php else: ?>
            <div class="thumb-placeholder"><i class="fa-solid fa-diagram-project"></i></div>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <span class="tag"><?= e($p['category']) ?></span>
          <h3><?= e($p['title']) ?></h3>
          <p><?= e($p['description']) ?></p>
          <a class="btn btn-small" href="project-details?slug=<?= e($p['slug']) ?>">Details <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>