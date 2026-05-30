/* Lógica de filtros de usuarios */
var UsuariosFiltros = (function () {
  function init() {
    const resetButton = document.querySelector('.usuarios-reset-filters');
    if (!resetButton) {
      return;
    }

    resetButton.addEventListener('click', function (event) {
      // El enlace ya recarga la página; mantenemos el comportamiento natural.
      const fields = document.querySelectorAll('.usuarios-filter-input, .usuarios-filter-select');
      fields.forEach((field) => {
        field.value = '';
      });
    });
  }

  return {
    init,
  };
})();
