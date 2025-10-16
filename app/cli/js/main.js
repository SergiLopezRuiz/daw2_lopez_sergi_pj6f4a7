window.addEventListener('DOMContentLoaded', () => {
  const goAbout = document.getElementById('goAbout');
  const goMenu  = document.getElementById('goMenu');

  goAbout?.addEventListener('click', () => {
    window.location.href = 'about.html';
  });

  goMenu?.addEventListener('click', () => {
    window.location.href = 'menu.html';
  });
});
