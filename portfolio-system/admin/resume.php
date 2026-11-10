<?php
$adminTitle = 'Resume';
require_once __DIR__ . '/_layout_top.php';

$settings = get_settings($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (!empty($_FILES['resume']['name'])) {
            $newFile = save_upload($_FILES['resume'], 'pdf');
            
            // Check if the setting already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM site_settings WHERE setting_key = 'resume_file'");
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'resume_file'")->execute([$newFile]);
            } else {
                $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('resume_file', ?)")->execute([$newFile]);
            }
            
            flash('success','Resume uploaded.');
        } else { 
            flash('error','Please choose a PDF file.'); 
        }
    } catch (Throwable $t) { flash('error',$t->getMessage()); }
    redirect('resume.php');
}
$current = $settings['resume_file'] ?? null;
?>
<div class="card" style="background:var(--card);padding:20px;margin-bottom:20px;border-radius:12px;">
  <h3>Current Resume</h3>
  <?php if ($current): ?>
    <p><i class="fa-solid fa-file-pdf"></i> <a href="<?= e($current) ?>" target="_blank"><?= e($current) ?></a></p>
  <?php else: ?>
    <p class="muted">No resume uploaded yet.</p>
  <?php endif; ?>
</div>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrf_field() ?>
  <label>Upload PDF Resume <input type="file" name="resume" accept="application/pdf" required></label>
  <button class="btn btn-primary">Upload</button>
</form>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>