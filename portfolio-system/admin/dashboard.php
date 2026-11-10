<?php
$adminTitle = 'Dashboard';
require_once __DIR__ . '/_layout_top.php';
$stats = [
  'Projects'    => (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
  'Skills'      => (int)$pdo->query('SELECT COUNT(*) FROM skills')->fetchColumn(),
  'Certificates'=> (int)$pdo->query('SELECT COUNT(*) FROM certificates')->fetchColumn(),
  'Unread'      => (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn(),
];
$recent = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
?>
<div class="stat-grid">
  <?php foreach ($stats as $k => $v): ?>
    <div class="stat-card">
      <span class="stat-value"><?= (int)$v ?></span>
      <span class="stat-label"><?= e($k) ?></span>
    </div>
  <?php endforeach; ?>
</div>
<h2 class="block-title">Recent Messages</h2>
<div class="table-wrap">
<table class="data-table">
  <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($recent as $m): ?>
    <tr>
      <td><?= e($m['name']) ?></td>
      <td><?= e($m['email']) ?></td>
      <td><?= e($m['subject']) ?></td>
      <td><?= e(date('M j, Y H:i', strtotime($m['created_at']))) ?></td>
      <td><?= $m['is_read'] ? 'Read' : '<strong>Unread</strong>' ?></td>
    </tr>
  <?php endforeach; if (!$recent): ?><tr><td colspan="5">No messages yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>