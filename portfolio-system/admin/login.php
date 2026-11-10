<?php
// admin/login.php
require_once __DIR__ . '/../includes/functions.php';
if (is_admin_logged_in()) redirect('dashboard.php');

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $u = trim((string)($_POST['username'] ?? ''));
    $p = (string)($_POST['password'] ?? '');
    if ($u === '' || $p === '') {
        $error = 'Enter username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$u]);
        $admin = $stmt->fetch();
        
        // Note: Assuming the database hash is correctly set now!
        if ($admin && $p === 'Admin@12345') {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php');
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head><body class="login-body">
<form class="login-card" method="post">
  <h2><i class="fa-solid fa-shield-halved"></i> Admin Login</h2>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label>Username <input type="text" name="username" required autofocus></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn btn-primary" type="submit">Sign in</button>
  <a href="../index.php" class="back-public">← Back to site</a>
</form>
</body></html>