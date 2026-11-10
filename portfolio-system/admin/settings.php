<?php
$adminTitle = 'Settings';
require_once __DIR__ . '/_layout_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $siteTitle = trim((string)($_POST['site_title'] ?? ''));
    $theme     = $_POST['theme'] === 'light' ? 'light' : 'dark';
    $pdo->prepare("REPLACE INTO site_settings (setting_key, setting_value) VALUES ('site_title', ?)")->execute([$siteTitle]);
    $pdo->prepare("REPLACE INTO site_settings (setting_key, setting_value) VALUES ('theme', ?)")->execute([$theme]);

    if (!empty($_POST['new_password'])) {
        $new = (string)$_POST['new_password'];
        if (strlen($new) < 8) flash('error','Password must be at least 8 characters.');
        else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([$hash, $_SESSION['admin_id']]);
            flash('success','Password changed.');
            redirect('settings.php');
        }
    }
    flash('success','Settings saved.');
    redirect('settings.php');
}
$settings = get_settings($pdo);
?>
<form method="post" class="admin-form">
  <?= csrf_field() ?>
  <div class="form-grid">
    <label>Site Title <input name="site_title" value="<?= e($settings['site_title'] ?? '') ?>"></label>
    <label>Default Theme
      <select name="theme">
        <option value="dark" <?= ($settings['theme']??'dark')==='dark'?'selected':'' ?>>Dark</option>
        <option value="light" <?= ($settings['theme']??'')==='light'?'selected':'' ?>>Light</option>
      </select>
    </label>
  </div>
  <h3 style="margin-top:20px;">Change Password</h3>
  <label>New Password (leave blank to keep) <input type="password" name="new_password" minlength="8"></label>
  <button class="btn btn-primary">Save Settings</button>
</form>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>