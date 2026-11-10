<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Projects';
$q   = trim((string)($_GET['q'] ?? ''));
$cat = trim((string)($_GET['category'] ?? ''));

$sql = "SELECT * FROM projects WHERE 1=1"; $params = [];
if ($q !== '') { $sql .= " AND (title LIKE ? OR description LIKE ? OR technologies LIKE ?)"; $l="%$q%"; $params=[$l,$l,$l]; }
if ($cat !== '') { $sql .= " AND category = ?"; $params[] = $cat; }
$sql .= " ORDER BY completion_date DESC, id DESC";
$st = $pdo->prepare($sql); $st->execute($params); $projects = $st->fetchAll();

$cats = $pdo->query('SELECT DISTINCT category FROM projects ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
include __DIR__ . '/includes/header.php';
?>
<section class="section container">
  <h1 class="page-title">Projects</h1>
  <form class="filters card" method="get">
    <input type="search" name="q" placeholder="Search projects..." value="<?= e($q) ?>">
    <select name="category">
      <option value="">All Categories</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= e($c) ?>" <?= $c===$cat?'selected':'' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
  </form>
  <div class="projects-grid">
    <?php foreach ($projects as $p): ?>
      <article class="card project-card">
        <div class="thumb">
          <?php if ($p['thumbnail']): ?><img src="<?= e($p['thumbnail']) ?>" alt="<?= e($p['title']) ?>">
          <?php else: ?><div class="thumb-placeholder"><i class="fa-solid fa-diagram-project"></i></div><?php endif; ?>
        </div>
        <div class="card-body">
          <span class="tag"><?= e($p['category']) ?></span>
          <h3><?= e($p['title']) ?></h3>
          <p><?= e($p['description']) ?></p>
          <a class="btn btn-small" href="project-details?slug=<?= e($p['slug']) ?>">Details <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>
    <?php endforeach; if (!$projects): ?><p class="empty">No projects match your search.</p><?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>