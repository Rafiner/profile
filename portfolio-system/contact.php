<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Contact';
$errors = []; $success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name    = trim((string)($_POST['name'] ?? ''));
    $email   = trim((string)($_POST['email'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $honey   = trim((string)($_POST['website'] ?? ''));

    if ($honey !== '') $errors['spam'] = 'Submission rejected.';
    if ($name === '' || mb_strlen($name) > 120) $errors['name'] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email.';
    if ($subject === '' || mb_strlen($subject) > 160) $errors['subject'] = 'Please enter a subject.';
    if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) $errors['message'] = 'Message must be 10–5000 characters.';

    if (!$errors) {
        $st = $pdo->prepare('INSERT INTO contact_messages (name,email,subject,message,ip_address) VALUES (?,?,?,?,?)');
        $st->execute([$name,$email,$subject,$message, $_SERVER['REMOTE_ADDR'] ?? null]);
        $success = true; $_POST = [];
    }
}
include __DIR__ . '/includes/header.php';
?>
<style>
  /* Force the contact form to look perfect and stack vertically */
  .contact-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
    align-items: start;
  }
  .contact-grid .card {
    padding: 32px;
    background: var(--card);
    border-radius: 14px;
    border: 1px solid var(--border);
  }
  #contact-form label {
    display: block !important;
    margin-bottom: 20px !important;
    width: 100% !important;
    color: var(--text) !important;
    font-weight: 500 !important;
  }
  #contact-form input[type="text"], 
  #contact-form input[type="email"], 
  #contact-form textarea {
    display: block !important;
    width: 100% !important;
    padding: 14px 16px !important;
    margin-top: 8px !important;
    background-color: var(--bg-alt) !important;
    color: var(--text) !important;
    border: 1px solid var(--border) !important;
    border-radius: 10px !important;
    font-family: inherit !important;
    font-size: 1rem !important;
    box-sizing: border-box !important;
  }
  #contact-form input:focus, 
  #contact-form textarea:focus {
    outline: none !important;
    border-color: var(--accent-2) !important;
    box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.15) !important;
  }
  #contact-form textarea {
    resize: vertical !important;
    min-height: 140px !important;
  }
  /* Hide the anti-spam trap completely */
  .honeypot {
    position: absolute !important;
    left: -9999px !important;
    opacity: 0 !important;
    height: 1px !important;
    width: 1px !important;
  }
  @media (max-width: 820px) {
    .contact-grid { grid-template-columns: 1fr; }
  }
</style>

<section class="section container">
  <h1 class="page-title">Contact Me</h1>
  <div class="contact-grid">
    <div class="card">
      <?php if ($success): ?><div class="alert success"><i class="fa-solid fa-check-circle"></i> Thank you! Your message has been sent.</div><?php endif; ?>
      <?php if (!empty($errors['spam'])): ?><div class="alert error"><?= e($errors['spam']) ?></div><?php endif; ?>
      
      <form method="post" novalidate id="contact-form">
        <?= csrf_field() ?>
        
        <!-- Honeypot field (invisible to users, traps spam bots) -->
        <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off">
        
        <label>Name
          <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required maxlength="120" placeholder="Your Name">
          <?php if (isset($errors['name'])): ?><small class="err"><?= e($errors['name']) ?></small><?php endif; ?>
        </label>
        
        <label>Email
          <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required maxlength="150" placeholder="you@example.com">
          <?php if (isset($errors['email'])): ?><small class="err"><?= e($errors['email']) ?></small><?php endif; ?>
        </label>
        
        <label>Subject
          <input type="text" name="subject" value="<?= e($_POST['subject'] ?? '') ?>" required maxlength="160" placeholder="How can I help?">
          <?php if (isset($errors['subject'])): ?><small class="err"><?= e($errors['subject']) ?></small><?php endif; ?>
        </label>
        
        <label>Message
          <textarea name="message" rows="6" required minlength="10" maxlength="5000" placeholder="Write your message here..."><?= e($_POST['message'] ?? '') ?></textarea>
          <?php if (isset($errors['message'])): ?><small class="err"><?= e($errors['message']) ?></small><?php endif; ?>
        </label>
        
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
      </form>
    </div>
    
    <div class="card">
      <h3>Reach me directly</h3>
      <p><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a></p>
      <?php if (!empty($profile['phone'])): ?><p><i class="fa-solid fa-phone"></i> <?= e($profile['phone']) ?></p><?php endif; ?>
      <?php if (!empty($profile['location'])): ?><p><i class="fa-solid fa-location-dot"></i> <?= e($profile['location']) ?></p><?php endif; ?>
      <h4>Find me online</h4>
      <div class="socials">
        <?php foreach ($socials as $s): ?>
          <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['platform']) ?>"><i class="<?= e($s['icon']) ?>"></i></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>