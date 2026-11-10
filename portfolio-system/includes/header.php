<?php
require_once __DIR__ . '/functions.php';
$settings  = get_settings($pdo);
$siteTitle = $settings['site_title'] ?? 'Portfolio';
$pageTitle = $pageTitle ?? $siteTitle;
$profile   = $pdo->query('SELECT * FROM profile WHERE id=1')->fetch() ?: [];
$socials   = $pdo->query('SELECT * FROM social_links ORDER BY sort_order, id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($settings['theme'] ?? 'dark') ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> — <?= e($profile['full_name'] ?? $siteTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main></main>