</main>
<footer class="footer">
  <div class="container footer-inner">
    <p>&copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? '') ?>. All rights reserved.</p>
    <div class="footer-socials">
      <?php foreach ($socials as $s): ?>
        <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($s['platform']) ?>">
          <i class="<?= e($s['icon']) ?>"></i>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</footer>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800, // Animation speed
    once: true,    // Only animate once
    offset: 100    // Start animating 100px before the element comes into view
  });
</script>
<script src="assets/js/script.js"></script>
</body>
</html>