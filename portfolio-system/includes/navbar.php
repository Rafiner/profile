<?php
$current = basename($_SERVER['PHP_SELF']);
$links = [
    '/'          => 'Home',
    'about'      => 'About',
    'projects'   => 'Projects',
    'resume'     => 'Resume',
    'contact'    => 'Contact',
];
?>
<header class="nav-wrap">
  <nav class="nav container">
    <a href="index.php" class="brand"><?= e($profile['full_name'] ?? 'Portfolio') ?></a>
    <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
      <i class="fa-solid fa-bars"></i>
    </button>
    <ul class="nav-links">
      <?php foreach ($links as $file => $label): ?>
        <li><a href="<?= e($file) ?>" class="<?= $current === $file ? 'active' : '' ?>"><?= e($label) ?></a></li>
      <?php endforeach; ?>
      <li><button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme"><i class="fa-solid fa-moon"></i></button></li>
    </ul>
  </nav>
</header>