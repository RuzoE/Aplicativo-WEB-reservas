/* Inicialización general del módulo de usuarios */
var Usuarios = (function () {
  function init() {
    if (typeof UsuariosDashboard !== 'undefined' && UsuariosDashboard.init) {
      UsuariosDashboard.init();
    }
    if (typeof UsuariosFiltros !== 'undefined' && UsuariosFiltros.init) {
      UsuariosFiltros.init();
    }
    if (typeof UsuariosTabla !== 'undefined' && UsuariosTabla.init) {
      UsuariosTabla.init();
    }
  }

  return {
    init,
  };
})();

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', Usuarios.init);
} else {
  Usuarios.init();
}
