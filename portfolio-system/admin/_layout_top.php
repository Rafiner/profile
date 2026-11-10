<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$adminTitle = $adminTitle ?? 'Dashboard';
$current = basename($_SERVER['PHP_SELF']);
$navItems = [
  'dashboard.php'    => ['Dashboard', 'fa-gauge'],
  'profile.php'      => ['Profile',   'fa-user'],
  'skills.php'       => ['Skills',    'fa-code'],
  'projects.php'     => ['Projects',  'fa-diagram-project'],
  'education.php'    => ['Education', 'fa-graduation-cap'],
  'certificates.php' => ['Certificates','fa-certificate'],
  'social_links.php' => ['Social Links','fa-share-nodes'],
  'resume.php'       => ['Resume',    'fa-file-pdf'],
  'messages.php'     => ['Messages',  'fa-envelope'],
  'settings.php'     => ['Settings',  'fa-gear'],
];
$unread = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($adminTitle) ?> — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="admin-brand"><i class="fa-solid fa-shield-halved"></i> Admin</div>
    <nav class="admin-nav">
      <?php foreach ($navItems as $file => [$label, $icon]): ?>
        <a href="<?= e($file) ?>" class="<?= $current === $file ? 'active' : '' ?>">
          <i class="fa-solid <?= e($icon) ?>"></i> <?= e($label) ?>
          <?php if ($file === 'messages.php' && $unread): ?><span class="badge"><?= $unread ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <a class="logout-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-nav-toggle" aria-label="Toggle sidebar"><i class="fa-solid fa-bars"></i></button>
      <h1 class="admin-title"><?= e($adminTitle) ?></h1>
      <span class="muted">Logged in as <strong><?= e($_SESSION['admin_username'] ?? 'admin') ?></strong></span>
    </header>
    <main class="admin-content">
      <?php if ($m = flash('success')): ?><div class="alert success"><?= e($m) ?></div><?php endif; ?>
      <?php if ($m = flash('error')): ?><div class="alert error"><?= e($m) ?></div><?php endif; ?>