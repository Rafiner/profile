<?php
$adminTitle = 'Skills';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim((string)($_POST['name'] ?? ''));
            $cat  = (string)($_POST['category'] ?? 'Other');
            $prof = max(0, min(100, (int)($_POST['proficiency'] ?? 0)));
            $icon = trim((string)($_POST['icon'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);
            if ($name === '') throw new RuntimeException('Name required.');

            // Handle Image Upload to Cloudinary (if a new file is selected)
            if (!empty($_FILES['icon_file']['name'])) {
                $icon = save_upload($_FILES['icon_file'], 'image');
            }

            if ($action === 'create') {
                $pdo->prepare('INSERT INTO skills (name,category,proficiency,icon,sort_order) VALUES (?,?,?,?,?)')
                    ->execute([$name,$cat,$prof,$icon,$sort]);
                flash('success','Skill added.');
            } else {
                $pdo->prepare('UPDATE skills SET name=?,category=?,proficiency=?,icon=?,sort_order=? WHERE id=?')
                    ->execute([$name,$cat,$prof,$icon,$sort,$id]);
                flash('success','Skill updated.');
            }
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM skills WHERE id=?')->execute([(int)$_POST['id']]);
            flash('success','Skill deleted.');
        }
    } catch (Throwable $t) { flash('error', $t->getMessage()); }
    redirect('skills.php');
}

$edit = null;
if (!empty($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM skills WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit = $s->fetch();
}
$skills = $pdo->query('SELECT * FROM skills ORDER BY category, sort_order, id')->fetchAll();
$categories = ['Programming','Web Development','Database','Tools','Other'];
?>
<form method="post" class="admin-form" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit ? 'update':'create' ?>">
  <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
  
  <div class="form-grid">
    <label>Name <input name="name" value="<?= e($edit['name'] ?? '') ?>" required></label>
    <label>Category
      <select name="category">
        <?php foreach ($categories as $c): ?>
          <option <?= ($edit['category'] ?? '')===$c?'selected':'' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Proficiency (0-100) <input type="number" min="0" max="100" name="proficiency" value="<?= (int)($edit['proficiency'] ?? 50) ?>"></label>
    
    <!-- This is where you can upload the image -->
    <label>Upload Icon Image (Cloudinary) 
      <input type="file" name="icon_file" accept="image/*">
    </label>
    
    <!-- Keep this hidden if uploading, or type a FontAwesome class -->
    <label>OR type Font Awesome class (e.g. fa-brands fa-java) 
      <input name="icon" value="<?= e($edit['icon'] ?? '') ?>" placeholder="fa-brands fa-java">
    </label>
    
    <label>Sort Order <input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></label>
  </div>
  <button class="btn btn-primary"><?= $edit?'Update':'Add' ?> Skill</button>
  <?php if ($edit): ?><a class="btn btn-ghost" href="skills.php">Cancel</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Name</th><th>Category</th><th>Proficiency</th><th>Icon</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($skills as $s): ?>
      <tr>
        <td><?= e($s['name']) ?></td>
        <td><?= e($s['category']) ?></td>
        <td><?= (int)$s['proficiency'] ?>%</td>
        <td>
          <?php if (str_starts_with($s['icon'] ?? '', 'http')): ?>
            <img src="<?= e($s['icon']) ?>" alt="icon" style="width: 24px; height: 24px; object-fit: contain;">
          <?php else: ?>
            <i class="<?= e($s['icon']) ?>"></i>
          <?php endif; ?>
        </td>
        <td class="row-actions">
          <a class="btn btn-small" href="?edit=<?= (int)$s['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this skill?')">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="btn btn-small danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>