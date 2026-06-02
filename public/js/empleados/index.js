/**
 * index.js — Gestión de interfaz de empleados (interacciones, visibilidad de tablas y botones)
 * Hotel Oasis • 2026
 */
document.addEventListener('DOMContentLoaded', function() {
  const btnCancel = document.getElementById('btnCancelarForm2');
  const btnCerrar = document.getElementById('btnCerrarForm');
  const btnNuevo = document.getElementById('btnNuevoEmpleado');
  const wrapper = document.getElementById('empleadoFormWrapper');
  const tableWrapper = document.getElementById('empleadosTableWrapper');
  const headerWrapper = document.getElementById('empleadosHeaderWrapper');
  
  function showTable() {
    if (wrapper) wrapper.classList.add('d-none');
    if (tableWrapper) tableWrapper.classList.remove('d-none');
    if (headerWrapper) headerWrapper.classList.remove('d-none');
    if (btnNuevo) btnNuevo.classList.remove('d-none');
  }

  if (btnCancel) {
    btnCancel.addEventListener('click', showTable);
  }
  
  if (btnCerrar) {
    btnCerrar.addEventListener('click', showTable);
  }

  if (btnNuevo) {
    btnNuevo.addEventListener('click', () => {
      if (wrapper) wrapper.classList.remove('d-none');
      if (tableWrapper) tableWrapper.classList.add('d-none');
      btnNuevo.classList.add('d-none');
      window.scrollTo({top:0, behavior:'smooth'});
    });
  }

  // Ocultar botón al iniciar si el formulario está abierto (por ejemplo, por errores de validación)
  if (wrapper && !wrapper.classList.contains('d-none')) {
    if (btnNuevo) btnNuevo.classList.add('d-none');
  }
});
