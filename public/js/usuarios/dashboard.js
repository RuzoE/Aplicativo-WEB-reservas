/* Dashboard de usuarios: carga y actualizaciones de cards */
var UsuariosDashboard = (function () {
  function init() {
    const cards = document.querySelectorAll('.usuario-stat-card');
    if (!cards.length) {
      return;
    }

    cards.forEach((card) => {
      card.classList.add('usuarios-animated');
    });
  }

  return {
    init,
  };
})();
