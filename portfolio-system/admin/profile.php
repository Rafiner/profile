<?php
$adminTitle = 'Profile';
require_once __DIR__ . '/_layout_top.php';

$stmt = $pdo->query('SELECT * FROM profile WHERE id=1');
$row  = $stmt->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $fields = ['full_name','title','intro','about','career_goals','interests','personal_statement','email','phone','location'];
    $data = [];
    foreach ($fields as $f) $data[$f] = trim((string)($_POST[$f] ?? ''));
    $errors = [];
    if ($data['full_name'] === '') $errors[] = 'Full name is required.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    $newImage = $row['profile_image'] ?? null;
    try {
        if (!empty($_FILES['profile_image']['name'])) {
            $newImage = save_upload($_FILES['profile_image'], 'image');
            if ($row['profile_image'] ?? null) delete_upload($row['profile_image']);
        }
    } catch (Throwable $t) { $errors[] = $t->getMessage(); }

    if (!$errors) {
        $sql = 'UPDATE profile SET full_name=?, title=?, intro=?, about=?, career_goals=?, interests=?, personal_statement=?, email=?, phone=?, location=?, profile_image=? WHERE id=1';
        $pdo->prepare($sql)->execute([
            $data['full_name'],$data['title'],$data['intro'],$data['about'],
            $data['career_goals'],$data['interests'],$data['personal_statement'],
            $data['email'],$data['phone'],$data['location'],$newImage
        ]);
        flash('success','Profile updated.');
        redirect('profile.php');
    }
    foreach ($errors as $er) flash('error',$er);
    $row = array_merge($row, $data);
}
?>
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrf_field() ?>
  <div class="form-grid">
    <label>Full Name <input name="full_name" value="<?= e($row['full_name'] ?? '') ?>" required></label>
    <label>Professional Title <input name="title" value="<?= e($row['title'] ?? '') ?>"></label>
    <label>Email <input type="email" name="email" value="<?= e($row['email'] ?? '') ?>" required></label>
    <label>Phone <input name="phone" value="<?= e($row['phone'] ?? '') ?>"></label>
    <label>Location <input name="location" value="<?= e($row['location'] ?? '') ?>"></label>
    <label>Profile Image <input type="file" name="profile_image" accept="image/*"></label>
  </div>
  <label>Short Intro <textarea name="intro" rows="3"><?= e($row['intro'] ?? '') ?></textarea></label>
  <label>About Me <textarea name="about" rows="5"><?= e($row['about'] ?? '') ?></textarea></label>
  <label>Career Goals <textarea name="career_goals" rows="3"><?= e($row['career_goals'] ?? '') ?></textarea></label>
  <label>Interests <textarea name="interests" rows="3"><?= e($row['interests'] ?? '') ?></textarea></label>
  <label>Personal Statement <textarea name="personal_statement" rows="3"><?= e($row['personal_statement'] ?? '') ?></textarea></label>
  <?php if (!empty($row['profile_image'])): ?>
    <div style="margin-bottom:15px"><img src="../assets/uploads/<?= e($row['profile_image']) ?>" alt="" style="max-width:120px;border-radius:8px"></div>
  <?php endif; ?>
  <button class="btn btn-primary" type="submit">Save Changes</button>
</form>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>