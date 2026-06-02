/**
 * change-password.js — Cambio de contraseña de usuario (admin)
 * Hotel Oasis • 2026
 */

document.addEventListener('DOMContentLoaded', function () {
  const passwordInput = document.getElementById('password');

  if (!passwordInput) return;

  // ── Toggle password visibility ──────────────────────────────
  window.togglePassword = function (fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
  };

  // ── Generate secure password ─────────────────────────────────
  window.generatePassword = function () {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    let password = '';
    for (let i = 0; i < 16; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    passwordInput.value = password;
    passwordInput.type  = 'text';
    updatePasswordStrength();
  };

  // ── Password strength indicator ──────────────────────────────
  passwordInput.addEventListener('input', updatePasswordStrength);

  function updatePasswordStrength() {
    const pass = passwordInput.value;
    let strength = 0;

    if (pass.length >= 12)                                   strength += 20;
    if (pass.length >= 16)                                   strength += 10;
    if (/[a-z]/.test(pass))                                  strength += 20;
    if (/[A-Z]/.test(pass))                                  strength += 20;
    if (/[0-9]/.test(pass))                                  strength += 15;
    if (/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(pass))      strength += 15;

    const bar       = document.getElementById('passwordStrength');
    bar.style.width = Math.min(strength, 100) + '%';
    bar.className   = 'progress-bar ' + (
      strength < 50 ? 'bg-danger' :
      strength < 75 ? 'bg-warning' :
      'bg-success'
    );
  }
});
