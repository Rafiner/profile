(function () {
  const toggle = document.querySelector('.nav-toggle');
  const links  = document.querySelector('.nav-links');
  if (toggle && links) {
    toggle.addEventListener('click', () => {
      const open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  const root = document.documentElement;
  const btn  = document.getElementById('theme-toggle');
  const applyTheme = (t) => { root.setAttribute('data-theme', t); localStorage.setItem('theme', t); };
  if (btn) {
    const saved = localStorage.getItem('theme');
    if (saved) applyTheme(saved);
    btn.addEventListener('click', () => {
      applyTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  }
  const target = document.getElementById('typed');
  if (target) {
    const titles = ['Web Developer', 'Full-Stack Developer', 'Computer Science Student'];
    let i = 0, j = 0, dir = 1;
    const step = () => {
      const word = titles[i];
      j += dir;
      target.textContent = word.slice(0, j);
      if (j === word.length) { dir = -1; setTimeout(step, 1400); return; }
      if (j === 0) { dir = 1; i = (i + 1) % titles.length; }
      setTimeout(step, dir === 1 ? 90 : 45);
    };
    target.textContent = ''; step();
  }
})();