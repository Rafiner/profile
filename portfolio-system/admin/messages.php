<?php
$adminTitle = 'Messages';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($action === 'mark_read') {
            $pdo->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$id]);
            flash('success','Marked as read.');
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
            flash('success','Message deleted.');
        }
    } catch (Throwable $t) { flash('error',$t->getMessage()); }
    redirect('messages.php');
}

$view = null;
if (!empty($_GET['view'])) {
    $s = $pdo->prepare('SELECT * FROM contact_messages WHERE id=?'); $s->execute([(int)$_GET['view']]);
    $view = $s->fetch();
    if ($view && !$view['is_read']) $pdo->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([(int)$view['id']]);
}

$q = trim((string)($_GET['q'] ?? ''));
$filter = trim((string)($_GET['filter'] ?? ''));
$sql = 'SELECT * FROM contact_messages WHERE 1=1';
$params = [];
if ($q !== '') { $sql .= ' AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)'; $l="%$q%";$params=[$l,$l,$l]; }
if ($filter === 'unread') $sql .= ' AND is_read=0';
$sql .= ' ORDER BY created_at DESC';
$st = $pdo->prepare($sql); $st->execute($params); $rows = $st->fetchAll();
?>
<form method="get" class="filters" style="display:flex;gap:10px;margin-bottom:20px;">
  <input type="search" name="q" placeholder="Search..." value="<?= e($q) ?>" style="padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-alt);color:var(--text);">
  <select name="filter" style="padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-alt);color:var(--text);">
    <option value="">All</option>
    <option value="unread" <?= $filter==='unread'?'selected':'' ?>>Unread only</option>
  </select>
  <button class="btn btn-primary">Filter</button>
</form>

<?php if ($view): ?>
  <div class="card" style="background:var(--card);padding:20px;margin-bottom:20px;border-radius:12px;">
    <h3><?= e($view['subject']) ?></h3>
    <p class="muted">From <?= e($view['name']) ?> &lt;<?= e($view['email']) ?>&gt; • <?= e($view['created_at']) ?></p>
    <p style="margin-bottom: 20px;"><?= nl2br(e($view['message'])) ?></p>
    
    <div style="display: flex; gap: 10px;">
      <a class="btn btn-small" style="background: #ea4335; color: #fff;" href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($view['email']) ?>&su=<?= urlencode('Re: ' . $view['subject']) ?>" target="_blank">
        <i class="fa-solid fa-reply"></i> Reply via Gmail
      </a>
      
      <form method="post" onsubmit="return confirm('Delete this message?')" style="margin: 0;">
        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$view['id'] ?>">
        <button class="btn btn-small danger">Delete</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Status</th><th>From</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['is_read'] ? 'Read' : '<strong>Unread</strong>' ?></td>
        <td><?= e($r['name']) ?><br><span class="muted"><?= e($r['email']) ?></span></td>
        <td><?= e($r['subject']) ?></td>
        <td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td>
        <td class="row-actions">
          <a class="btn btn-small" href="?view=<?= (int)$r['id'] ?>">Read Here</a>
          
          <a class="btn btn-small" style="background: #ea4335; color: #fff;" href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode($r['email']) ?>&su=<?= urlencode('Re: ' . $r['subject']) ?>" target="_blank">
            <i class="fa-brands fa-google"></i> Gmail
          </a>

          <?php if (!$r['is_read']): ?>
            <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="mark_read"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-small">Mark read</button></form>
          <?php endif; ?>
          
          <form method="post" onsubmit="return confirm('Delete?')" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn-small danger">Delete</button></form>
        </td>
      </tr>
    <?php endforeach; if (!$rows): ?>
      <tr><td colspan="5">No messages.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>