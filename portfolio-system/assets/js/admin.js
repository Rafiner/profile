(function () {
  const btn  = document.querySelector('.admin-nav-toggle');
  const side = document.querySelector('.admin-sidebar');
  if (btn && side) {
    btn.addEventListener('click', () => side.classList.toggle('open'));
  }
})();