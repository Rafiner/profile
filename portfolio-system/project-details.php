<?php
require_once __DIR__ . '/includes/functions.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$st = $pdo->prepare('SELECT * FROM projects WHERE slug = ?'); $st->execute([$slug]);
$p = $st->fetch();
if (!$p) { http_response_code(404); $pageTitle='Not Found'; include __DIR__.'/includes/header.php';
  echo '<section class="section container"><h1 class="page-title">Project not found</h1><a class="btn btn-primary" href="projects.php">Back</a></section>';
  include __DIR__.'/includes/footer.php'; exit; }
$pageTitle = $p['title'];
include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <a class="back-link" href="projects"><i class="fa-solid fa-arrow-left"></i> Back to projects</a>
  <div class="details-grid">
    <div>
      <h1 class="page-title"><?= e($p['title']) ?></h1>
      <span class="tag"><?= e($p['category']) ?></span>
      <p class="muted">Completed <?= $p['completion_date'] ? e(date('F j, Y', strtotime($p['completion_date']))) : 'N/A' ?></p>
      <p><?= nl2br(e($p['long_description'] ?: $p['description'])) ?></p>
      <h3>Technologies</h3>
      <div class="tech-tags">
        <?php foreach (array_map('trim', explode(',', $p['technologies'])) as $t): ?>
          <span class="tag"><?= e($t) ?></span>
        <?php endforeach; ?>
      </div>
      <div class="details-actions">
        <?php if ($p['github_link']): ?><a class="btn btn-ghost" href="<?= e($p['github_link']) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-github"></i> GitHub</a><?php endif; ?>
        <?php if ($p['demo_link']): ?><a class="btn btn-primary" href="<?= e($p['demo_link']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Live Demo</a><?php endif; ?>
      </div>
    </div>
    <div>
      <?php if ($p['thumbnail']): ?><img class="details-img" src="<?= e($p['thumbnail']) ?>" alt="<?= e($p['title']) ?>"><?php endif; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>