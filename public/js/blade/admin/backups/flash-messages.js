/**
 * flash-messages.js — Toast notifications para Backups
 * Hotel Oasis • 2026
 *
 * Lee los mensajes de sesión desde data-* attributes del wrapper
 * para evitar mezclar variables Blade dentro de archivos JS.
 *
 * Atributos esperados en #backup-flash-data:
 *   data-success   — mensaje de éxito (opcional)
 *   data-error     — mensaje de error (opcional)
 *   data-has-errors — "1" si hay errores de validación (opcional)
 *   data-first-error — primer mensaje de error de validación
 */

document.addEventListener('DOMContentLoaded', function () {
  if (typeof Swal === 'undefined') return;

  const el = document.getElementById('backup-flash-data');
  if (!el) return;

  const success    = el.dataset.success    || '';
  const error      = el.dataset.error      || '';
  const hasErrors  = el.dataset.hasErrors  === '1';
  const firstError = el.dataset.firstError || '';

  const toastConfig = {
    timer           : 3500,
    showConfirmButton: false,
    toast           : true,
    position        : 'top-end'
  };

  if (success) {
    Swal.fire({ ...toastConfig, icon: 'success', title: '¡Éxito!', text: success });
  }

  if (hasErrors && firstError) {
    Swal.fire({ ...toastConfig, timer: 4500, icon: 'error', title: 'Atención', text: firstError });
  }

  if (error) {
    Swal.fire({ ...toastConfig, timer: 5000, icon: 'error', title: 'Error', text: error });
  }
});
