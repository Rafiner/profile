<?php
$adminTitle = 'Social Links';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $platform = trim((string)($_POST['platform'] ?? ''));
            $url      = trim((string)($_POST['url'] ?? ''));
            $icon     = trim((string)($_POST['icon'] ?? ''));
            $sort     = (int)($_POST['sort_order'] ?? 0);
            if ($platform === '' || !filter_var($url, FILTER_VALIDATE_URL)) throw new RuntimeException('Platform and valid URL required.');
            if ($action === 'create') {
                $pdo->prepare('INSERT INTO social_links (platform,url,icon,sort_order) VALUES (?,?,?,?)')
                    ->execute([$platform,$url,$icon,$sort]);
                flash('success','Social link added.');
            } else {
                $pdo->prepare('UPDATE social_links SET platform=?,url=?,icon=?,sort_order=? WHERE id=?')
                    ->execute([$platform,$url,$icon,$sort,$id]);
                flash('success','Social link updated.');
            }
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM social_links WHERE id=?')->execute([(int)$_POST['id']]);
            flash('success','Deleted.');
        }
    } catch (Throwable $t) { flash('error',$t->getMessage()); }
    redirect('social_links.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM social_links WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows = $pdo->query('SELECT * FROM social_links ORDER BY sort_order, id')->fetchAll();
?>
<form method="post" class="admin-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
  <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
  <div class="form-grid">
    <label>Platform <input name="platform" value="<?= e($edit['platform'] ?? '') ?>" required></label>
    <label>URL <input name="url" value="<?= e($edit['url'] ?? '') ?>" required></label>
    <label>Icon class <input name="icon" value="<?= e($edit['icon'] ?? 'fa-brands fa-github') ?>"></label>
    <label>Sort <input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></label>
  </div>
  <button class="btn btn-primary"><?= $edit?'Update':'Add' ?> Link</button>
  <?php if ($edit): ?><a class="btn btn-ghost" href="social_links.php">Cancel</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Platform</th><th>URL</th><th>Icon</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['platform']) ?></td>
        <td><a href="<?= e($r['url']) ?>" target="_blank" rel="noopener"><?= e($r['url']) ?></a></td>
        <td><i class="<?= e($r['icon']) ?>"></i></td>
        <td class="row-actions">
          <a class="btn btn-small" href="?edit=<?= (int)$r['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete?')">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-small danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>