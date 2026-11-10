<?php
$adminTitle = 'Projects';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'update') {
            $id    = (int)($_POST['id'] ?? 0);
            $title = trim((string)($_POST['title'] ?? ''));
            $slug  = trim((string)($_POST['slug'] ?? '')) ?: slugify($title);
            $desc  = trim((string)($_POST['description'] ?? ''));
            $long  = trim((string)($_POST['long_description'] ?? ''));
            $tech  = trim((string)($_POST['technologies'] ?? ''));
            $cat   = trim((string)($_POST['category'] ?? 'Web'));
            $gh    = trim((string)($_POST['github_link'] ?? ''));
            $demo  = trim((string)($_POST['demo_link'] ?? ''));
            $date  = $_POST['completion_date'] ?: null;
            $feat  = isset($_POST['featured']) ? 1 : 0;
            if ($title === '' || $desc === '' || $tech === '') throw new RuntimeException('Title, description and technologies are required.');

            $thumb = null;
            if (!empty($_FILES['thumbnail']['name'])) $thumb = save_upload($_FILES['thumbnail'], 'image');

            if ($action === 'create') {
                $sql = 'INSERT INTO projects (title,slug,description,long_description,thumbnail,technologies,category,github_link,demo_link,completion_date,featured) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
                $pdo->prepare($sql)->execute([$title,$slug,$desc,$long,$thumb,$tech,$cat,$gh,$demo,$date,$feat]);
                flash('success','Project created.');
            } else {
                if ($thumb) {
                    $old = $pdo->prepare('SELECT thumbnail FROM projects WHERE id=?'); $old->execute([$id]);
                    $oldName = $old->fetchColumn(); if ($oldName) delete_upload($oldName);
                    $sql = 'UPDATE projects SET title=?,slug=?,description=?,long_description=?,thumbnail=?,technologies=?,category=?,github_link=?,demo_link=?,completion_date=?,featured=? WHERE id=?';
                    $pdo->prepare($sql)->execute([$title,$slug,$desc,$long,$thumb,$tech,$cat,$gh,$demo,$date,$feat,$id]);
                } else {
                    $sql = 'UPDATE projects SET title=?,slug=?,description=?,long_description=?,technologies=?,category=?,github_link=?,demo_link=?,completion_date=?,featured=? WHERE id=?';
                    $pdo->prepare($sql)->execute([$title,$slug,$desc,$long,$tech,$cat,$gh,$demo,$date,$feat,$id]);
                }
                flash('success','Project updated.');
            }
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('SELECT thumbnail FROM projects WHERE id=?'); $stmt->execute([(int)$_POST['id']]);
            $thumb = $stmt->fetchColumn(); if ($thumb) delete_upload($thumb);
            $pdo->prepare('DELETE FROM projects WHERE id=?')->execute([(int)$_POST['id']]);
            flash('success','Project deleted.');
        }
    } catch (Throwable $t) { flash('error', $t->getMessage()); }
    redirect('projects.php');
}

$edit = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM projects WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$projects = $pdo->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();
?>
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
  <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
  <div class="form-grid">
    <label>Title <input name="title" value="<?= e($edit['title'] ?? '') ?>" required></label>
    <label>Slug (auto if empty) <input name="slug" value="<?= e($edit['slug'] ?? '') ?>"></label>
    <label>Category <input name="category" value="<?= e($edit['category'] ?? 'Web') ?>"></label>
    <label>Technologies (comma-separated) <input name="technologies" value="<?= e($edit['technologies'] ?? '') ?>" required></label>
    <label>GitHub URL <input name="github_link" value="<?= e($edit['github_link'] ?? '') ?>"></label>
    <label>Demo URL <input name="demo_link" value="<?= e($edit['demo_link'] ?? '') ?>"></label>
    <label>Completion Date <input type="date" name="completion_date" value="<?= e($edit['completion_date'] ?? '') ?>"></label>
    <label>Thumbnail <input type="file" name="thumbnail" accept="image/*"></label>
    <label class="inline"><input type="checkbox" name="featured" <?= !empty($edit['featured'])?'checked':'' ?>> Featured</label>
  </div>
  <label>Short Description <textarea name="description" rows="2" required><?= e($edit['description'] ?? '') ?></textarea></label>
  <label>Long Description <textarea name="long_description" rows="6"><?= e($edit['long_description'] ?? '') ?></textarea></label>
  <?php if (!empty($edit['thumbnail'])): ?><div style="margin-bottom:15px"><img src="../assets/uploads/<?= e($edit['thumbnail']) ?>" style="max-width:140px;border-radius:8px"></div><?php endif; ?>
  <button class="btn btn-primary"><?= $edit?'Update':'Create' ?> Project</button>
  <?php if ($edit): ?><a class="btn btn-ghost" href="projects.php">Cancel</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Title</th><th>Category</th><th>Tech</th><th>Featured</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($projects as $p): ?>
      <tr>
        <td><?= e($p['title']) ?></td>
        <td><?= e($p['category']) ?></td>
        <td><?= e($p['technologies']) ?></td>
        <td><?= $p['featured']?'★':'' ?></td>
        <td class="row-actions">
          <a class="btn btn-small" href="?edit=<?= (int)$p['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this project?')">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-small danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>