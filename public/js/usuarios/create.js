/**
 * create.js — Formulario de creación de usuario
 * Hotel Oasis • 2026
 */

document.addEventListener('DOMContentLoaded', function () {
  const passwordInput = document.getElementById('password');
  const toggleBtn    = document.getElementById('togglePass');
  const genBtn       = document.getElementById('genPass');
  const passBar      = document.getElementById('passBar');

  if (!passwordInput) return;

  // ── Toggle password visibility ──────────────────────────────
  toggleBtn?.addEventListener('click', function () {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    this.innerHTML = type === 'password'
      ? '<i class="bi bi-eye"></i>'
      : '<i class="bi bi-eye-slash"></i>';
  });

  // ── Generate secure password ─────────────────────────────────
  genBtn?.addEventListener('click', function () {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    let password = '';
    for (let i = 0; i < 14; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    passwordInput.value = password;
    passwordInput.type  = 'text';
    updatePasswordStrength();
  });

  // ── Password strength indicator ──────────────────────────────
  passwordInput.addEventListener('input', updatePasswordStrength);

  function updatePasswordStrength() {
    const pass = passwordInput.value;
    let strength = 0;

    if (pass.length >= 12)    strength += 25;
    if (/[a-z]/.test(pass))  strength += 25;
    if (/[A-Z]/.test(pass))  strength += 25;
    if (/[0-9]/.test(pass))  strength += 25;

    passBar.style.width = strength + '%';
    passBar.className   = 'progress-bar ' + (
      strength < 50 ? 'bg-danger' :
      strength < 75 ? 'bg-warning' :
      'bg-success'
    );
  }
});
