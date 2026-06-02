/**
 * sessions.js — Gestión de sesiones activas de usuario
 * Hotel Oasis • 2026
 *
 * Lee el userId desde el atributo data-user-id del wrapper.
 */

document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('sessions-wrapper');
  const userId  = wrapper?.dataset?.userId;

  if (!userId) return;

  /**
   * Cierra una sesión específica del usuario.
   * @param {number} sessionId
   */
  window.logoutSession = function (sessionId) {
    if (!confirm('¿Cerrar esta sesión?')) return;

    fetch(`/admin/usuarios/${userId}/sesiones/${sessionId}`, {
      method : 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
      .then(r  => r.json())
      .then(data => {
        if (data.success) {
          location.reload();
        }
      })
      .catch(e => alert('Error: ' + e));
  };
});
