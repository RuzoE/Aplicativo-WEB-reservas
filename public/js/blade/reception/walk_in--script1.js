document.addEventListener('DOMContentLoaded', () => {
    const roomSelect = document.getElementById('room-number-select');
    const priceDisplay = document.getElementById('room-price-display');
    const submitBtn = document.getElementById('submitBtn');
    const stayDaysInput = document.getElementById('stay_days');
    const availabilityResults = document.getElementById('availability-results');
    
    if (!roomSelect || !priceDisplay || !submitBtn) return;

    const form = submitBtn.closest('form');

    const moneyFormatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    });

    const updateRoomSelect = () => {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const statusDisplay = document.getElementById('room-status-display');

        if (!selectedOption || !selectedOption.value) {
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
    };

    roomSelect.addEventListener('change', updateRoomSelect);

    if (roomSelect.value) {
        updateRoomSelect();
    }

    // Dynamic Availability Logic
    let fetchTimeout;
    if (stayDaysInput && availabilityResults) {
        const urlTemplate = stayDaysInput.getAttribute('data-availability-url');
        
        stayDaysInput.addEventListener('input', () => {
            clearTimeout(fetchTimeout);
            const days = parseInt(stayDaysInput.value);
            if (isNaN(days) || days < 1) return;

            // Show loading
            availabilityResults.style.display = 'block';
            availabilityResults.innerHTML = '<div class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-2"></span> Calculando disponibilidad...</div>';
            
            fetchTimeout = setTimeout(async () => {
                try {
                    const url = new URL(urlTemplate, window.location.origin);
                    url.searchParams.append('stay_days', days);
                    
                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) throw new Error('Error de red');
                    
                    const data = await response.json();
                    renderAvailabilityResults(data, days);
                    updateSelectOptions(data.available);
                } catch (error) {
                    console.error('Error fetching availability:', error);
                    availabilityResults.innerHTML = '<div class="alert alert-danger">Error al consultar la disponibilidad. Intente de nuevo.</div>';
                }
            }, 500); // 500ms debounce
        });
        
        // Initial fetch to render results area (wait a bit to let DOM settle)
        setTimeout(() => {
            if (stayDaysInput.value >= 1) {
                stayDaysInput.dispatchEvent(new Event('input'));
            }
        }, 100);
    }

    function renderAvailabilityResults(data, days) {
        let html = '<div class="p-4 bg-light rounded border border-light-subtle">';
        
        if (data.available && data.available.length > 0) {
            html += `<h6 class="text-success fw-bold mb-3"><i class="bi bi-check-circle-fill me-1"></i> Habitaciones disponibles para ${days} noche(s):</h6>`;
            html += '<div class="d-flex flex-wrap gap-2 mb-3">';
            data.available.forEach(room => {
                html += `<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            Hab ${room.number} <span class="fw-normal">(${room.room_type})</span> - ${moneyFormatter.format(room.price)}
                         </span>`;
            });
            html += '</div>';
        } else {
            html += `<div class="alert alert-warning py-2 mb-3"><i class="bi bi-exclamation-triangle-fill me-1"></i> No hay habitaciones disponibles para ${days} noche(s).</div>`;
        }

        if (data.unavailable && data.unavailable.length > 0) {
            html += `<h6 class="text-danger fw-bold mb-3 mt-4"><i class="bi bi-x-circle-fill me-1"></i> Habitaciones no disponibles:</h6>`;
            html += '<div class="d-flex flex-wrap gap-2">';
            data.unavailable.forEach(room => {
                html += `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6 opacity-75">
                            Hab ${room.number} <span class="fw-normal">(${room.reason})</span>
                         </span>`;
            });
            html += '</div>';
        }

        html += '</div>';
        availabilityResults.innerHTML = html;
        availabilityResults.style.display = 'block';
    }

    function updateSelectOptions(availableRooms) {
        const currentSelection = roomSelect.value;
        
        roomSelect.innerHTML = '<option value="">Seleccione una habitación...</option>';
        
        availableRooms.forEach(room => {
            const option = document.createElement('option');
            option.value = room.number;
            option.setAttribute('data-status', room.status || 'Disponible');
            option.setAttribute('data-room-type', room.room_type);
            option.setAttribute('data-room-price', room.price);
            option.textContent = `Habitación ${room.number}`;
            
            if (currentSelection == room.number) {
                option.selected = true;
            }
            
            roomSelect.appendChild(option);
        });

        // Trigger change to update price and status display
        updateRoomSelect();
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
