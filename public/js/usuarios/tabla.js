/* Interacciones de tabla de usuarios */
var UsuariosTabla = (function () {
  let deleteUserId = null;

  function init() {
    initDeleteModal();
  }

  function initDeleteModal() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const modal = document.getElementById('deleteModal');
    const cancelButton = document.getElementById('cancelDelete');
    const confirmButton = document.getElementById('confirmDelete');
    const deleteForm = document.getElementById('deleteForm');

    if (!modal || !cancelButton || !confirmButton || !deleteForm) {
      return;
    }

    deleteButtons.forEach((button) => {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        deleteUserId = this.dataset.id;
        modal.classList.remove('hidden');
      });
    });

    cancelButton.addEventListener('click', function () {
      modal.classList.add('hidden');
      deleteUserId = null;
    });

    confirmButton.addEventListener('click', function () {
      if (!deleteUserId) {
        return;
      }
      deleteForm.action = `/admin/usuarios/${deleteUserId}`;
      deleteForm.submit();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        modal.classList.add('hidden');
        deleteUserId = null;
      }
    });
  }

  return {
    init,
  };
})();
