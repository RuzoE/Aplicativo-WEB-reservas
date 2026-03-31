// Lógica del checkout del minibar
function submitCheckout() {
    // Obtener el método de pago seleccionado
    const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
    const nota = document.getElementById('nota')?.value || '';
    const errorEl = document.getElementById('payment-error');
    const methodsEl = document.getElementById('payment-methods');

    if (!metodoPago) {
        if(errorEl){ errorEl.style.display = 'block'; }
        if(methodsEl){ methodsEl.classList.add('error'); methodsEl.scrollIntoView({behavior:'smooth', block:'center'}); }
        return;
    }

    // Ocultar error si ya seleccionó
    if(errorEl){ errorEl.style.display = 'none'; }
    if(methodsEl){ methodsEl.classList.remove('error'); }

    // Llenar los campos ocultos
    document.getElementById('hidden_metodo_pago').value = metodoPago.value;
    document.getElementById('hidden_nota').value = nota;

    // Enviar el formulario
    document.getElementById('checkout-form').submit();
}

window.submitCheckout = submitCheckout;

// Limpiar error al seleccionar un método
document.querySelectorAll('input[name="metodo_pago"]').forEach(r => {
    r.addEventListener('change', () => {
        const errorEl = document.getElementById('payment-error');
        const methodsEl = document.getElementById('payment-methods');
        if(errorEl) errorEl.style.display = 'none';
        if(methodsEl) methodsEl.classList.remove('error');
    });
});
