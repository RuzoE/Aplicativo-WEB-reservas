// Auto-ocultar notificaciones globales con efecto de salida
setTimeout(() => {
    const notif = document.getElementById('global-notification');
    if (notif) {
        notif.style.opacity = '0';
        notif.style.transform = 'translateY(-20px)';
        setTimeout(() => notif.remove(), 500);
    }
}, 4000);
