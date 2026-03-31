document.addEventListener('DOMContentLoaded', () => {
    const roomSelect = document.getElementById('room-number-select');
    const priceDisplay = document.getElementById('room-price-display');
    const submitBtn = document.getElementById('submitBtn');
    if (!roomSelect || !priceDisplay || !submitBtn) return;

    const form = submitBtn.closest('form');

    const moneyFormatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    });

    roomSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const statusDisplay = document.getElementById('room-status-display');

        if (!selectedOption.value) {
            priceDisplay.value = '$0,00';
            if (statusDisplay) {
                statusDisplay.value = 'Seleccione una habitación';
                statusDisplay.classList.remove('disponible', 'mantenimiento', 'ocupada');
            }
            submitBtn.disabled = true;
            return;
        }

        const status = selectedOption.getAttribute('data-status');
        if (statusDisplay) {
            statusDisplay.value = status;
            statusDisplay.classList.remove('disponible', 'mantenimiento', 'ocupada');
            if (status === 'Disponible') statusDisplay.classList.add('disponible');
            else if (status === 'Mantenimiento') statusDisplay.classList.add('mantenimiento');
            else if (status === 'Ocupada') statusDisplay.classList.add('ocupada');
        }

        submitBtn.disabled = (status !== 'Disponible');
        submitBtn.style.opacity = (status !== 'Disponible') ? '0.5' : '1';

        const price = parseFloat(selectedOption.getAttribute('data-room-price'));
        if (!isNaN(price)) {
            priceDisplay.value = moneyFormatter.format(price);
        }
    });

    if (roomSelect.value) {
        roomSelect.dispatchEvent(new Event('change'));
    }

    if (form) {
        form.addEventListener('submit', function () {
            if (form.checkValidity()) {
                submitBtn.disabled = true;
                const spinner = document.getElementById('spinner');
                const submitIcon = document.getElementById('submitIcon');
                const submitText = document.getElementById('submitText');
                if (spinner) spinner.style.display = 'inline-block';
                if (submitIcon) submitIcon.style.display = 'none';
                if (submitText) submitText.textContent = 'Procesando...';
            }
        });
    }
});
