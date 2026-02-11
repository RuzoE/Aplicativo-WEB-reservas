// Auto-ocultar notificaciones globales
setTimeout(() => {
    const notif = document.getElementById('global-notification');
    if(notif) notif.remove();
}, 3000);
