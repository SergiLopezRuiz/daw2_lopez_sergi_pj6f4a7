// explicación: manejadores de los botones de navegación
window.addEventListener('DOMContentLoaded', () => {
  const goAbout = document.getElementById('goAbout');
  const goMenu  = document.getElementById('goMenu');

  // explicación: redirige a las páginas que implementaremos en el siguiente paso
  goAbout?.addEventListener('click', () => {
    // explicación: about.html contendrá la descripción/documentación
    window.location.href = 'about.html';
  });

  goMenu?.addEventListener('click', () => {
    // explicación: menu.html contendrá la selección de operaciones (func. 3)
    window.location.href = 'menu.html';
  });
});
