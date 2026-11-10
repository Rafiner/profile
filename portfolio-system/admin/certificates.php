<?php
$adminTitle = 'Certificates';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $title = trim((string)($_POST['title'] ?? ''));
            $org   = trim((string)($_POST['organization'] ?? ''));
            $date  = $_POST['date_issued'] ?: null;
            $link  = trim((string)($_POST['verification_link'] ?? ''));
            if ($title === '' || $org === '') throw new RuntimeException('Title and organization required.');

            $image = null;
            if (!empty($_FILES['image']['name'])) $image = save_upload($_FILES['image'], 'image');

            if ($action === 'create') {
                $pdo->prepare('INSERT INTO certificates (title,organization,date_issued,image,verification_link) VALUES (?,?,?,?,?)')
                    ->execute([$title,$org,$date,$image,$link]);
                flash('success','Certificate added.');
            } else {
                if ($image) {
                    $old = $pdo->prepare('SELECT image FROM certificates WHERE id=?'); $old->execute([$id]);
                    $oldName = $old->fetchColumn(); if ($oldName) delete_upload($oldName);
                    $pdo->prepare('UPDATE certificates SET title=?,organization=?,date_issued=?,image=?,verification_link=? WHERE id=?')
                        ->execute([$title,$org,$date,$image,$link,$id]);
                } else {
                    $pdo->prepare('UPDATE certificates SET title=?,organization=?,date_issued=?,verification_link=? WHERE id=?')
                        ->execute([$title,$org,$date,$link,$id]);
                }
                flash('success','Certificate updated.');
            }
        } elseif ($action === 'delete') {
            $old = $pdo->prepare('SELECT image FROM certificates WHERE id=?'); $old->execute([(int)$_POST['id']]);
            $oldName = $old->fetchColumn(); if ($oldName) delete_upload($oldName);
            $pdo->prepare('DELETE FROM certificates WHERE id=?')->execute([(int)$_POST['id']]);
            flash('success','Certificate deleted.');
        }
    } catch (Throwable $t) { flash('error',$t->getMessage()); }
    redirect('certificates.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM certificates WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows = $pdo->query('SELECT * FROM certificates ORDER BY date_issued DESC')->fetchAll();
?>
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit?'update':'create' ?>">
  <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
  <div class="form-grid">
    <label>Title <input name="title" value="<?= e($edit['title'] ?? '') ?>" required></label>
    <label>Organization <input name="organization" value="<?= e($edit['organization'] ?? '') ?>" required></label>
    <label>Date Issued <input type="date" name="date_issued" value="<?= e($edit['date_issued'] ?? '') ?>"></label>
    <label>Verification Link <input name="verification_link" value="<?= e($edit['verification_link'] ?? '') ?>"></label>
    <label>Image <input type="file" name="image" accept="image/*"></label>
  </div>
  <?php if (!empty($edit['image'])): ?><div style="margin-bottom:15px"><img src="../assets/uploads/<?= e($edit['image']) ?>" style="max-width:120px;border-radius:8px"></div><?php endif; ?>
  <button class="btn btn-primary"><?= $edit?'Update':'Add' ?> Certificate</button>
  <?php if ($edit): ?><a class="btn btn-ghost" href="certificates.php">Cancel</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Title</th><th>Organization</th><th>Date</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['title']) ?></td>
        <td><?= e($r['organization']) ?></td>
        <td><?= e($r['date_issued']) ?></td>
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