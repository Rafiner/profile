<?php
$adminTitle = 'Education';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $school = trim((string)($_POST['school_name'] ?? ''));
            $degree = trim((string)($_POST['degree'] ?? ''));
            $yl     = trim((string)($_POST['year_level'] ?? ''));
            $sy     = (int)($_POST['start_year'] ?? 0);
            $ey     = (int)($_POST['end_year'] ?? 0);
            $cw     = trim((string)($_POST['coursework'] ?? ''));
            if ($school === '' || $degree === '') throw new RuntimeException('School and degree are required.');
            if ($action === 'create') {
                $pdo->prepare('INSERT INTO education (school_name,degree,year_level,start_year,end_year,coursework) VALUES (?,?,?,?,?,?)')
                    ->execute([$school,$degree,$yl,$sy,$ey,$cw]);
                flash('success','Education added.');
            } else {
                $pdo->prepare('UPDATE education SET school_name=?,degree=?,year_level=?,start_year=?,end_year=?,coursework=? WHERE id=?')
                    ->execute([$school,$degree,$yl,$sy,$ey,$cw,$id]);
                flash('success','Education updated.');
            }
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM education WHERE id=?')->execute([(int)$_POST['id']]);
            flash('success','Education deleted.');
        }
    } catch (Throwable $t) { flash('error',$t->getMessage()); }
    redirect('education.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM education WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows = $pdo->query('SELECT * FROM education ORDER BY start_year DESC')->fetchAll();
?>
<form method="post" class="admin-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
  <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
  <div class="form-grid">
    <label>School <input name="school_name" value="<?= e($edit['school_name'] ?? '') ?>" required></label>
    <label>Degree/Course <input name="degree" value="<?= e($edit['degree'] ?? '') ?>" required></label>
    <label>Year Level <input name="year_level" value="<?= e($edit['year_level'] ?? '') ?>"></label>
    <label>Start Year <input type="number" name="start_year" value="<?= (int)($edit['start_year'] ?? date('Y')-1) ?>"></label>
    <label>End Year <input type="number" name="end_year" value="<?= (int)($edit['end_year'] ?? date('Y')+1) ?>"></label>
  </div>
  <label>Coursework / Accomplishments <textarea name="coursework" rows="3"><?= e($edit['coursework'] ?? '') ?></textarea></label>
  <button class="btn btn-primary"><?= $edit?'Update':'Add' ?> Education</button>
  <?php if ($edit): ?><a class="btn btn-ghost" href="education.php">Cancel</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>School</th><th>Degree</th><th>Years</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['school_name']) ?></td>
        <td><?= e($r['degree']) ?></td>
        <td><?= (int)$r['start_year'] ?>–<?= e($r['end_year']) ?></td>
        <td class="row-actions">
          <a class="btn btn-small" href="?edit=<?= (int)$r['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this record?')">
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