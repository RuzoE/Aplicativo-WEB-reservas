// Check-in form: room status sync + currency display + submit spinner
// PHP data injected via window.CheckInConfig (set inline in check_in.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var configEl = document.getElementById('checkin-page-config');
    var config = {
        stayNights: configEl ? Number(configEl.dataset.stayNights || 0) : 0,
        defaultRoomType: configEl ? (configEl.dataset.defaultRoomType || 'N/A') : 'N/A',
        defaultRoomPrice: configEl ? Number(configEl.dataset.defaultRoomPrice || 0) : 0,
    };
    var stayNights     = config.stayNights     || 0;
    var defaultRoomType  = config.defaultRoomType  || 'N/A';
    var defaultRoomPrice = config.defaultRoomPrice || 0;

    var roomNumberSelect          = document.getElementById('room-number-select');
    var roomStatusDisplay         = document.getElementById('room-status-display');
    var reservationRoomDisplay    = document.getElementById('reservation-room-display');
    var reservationRoomTypeDisplay = document.getElementById('reservation-room-type-display');
    var reservationRateDisplay    = document.getElementById('reservation-rate-display');
    var reservationTotalDisplay   = document.getElementById('reservation-total-display');

    if (!roomNumberSelect) return;

    function formatCurrencyCOP(value) {
        var amount = Number(value || 0);
        return amount.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function syncRoomStatus() {
        var selectedOption = roomNumberSelect.options[roomNumberSelect.selectedIndex];
        var status = selectedOption ? selectedOption.dataset.status : null;
        roomStatusDisplay.value = status || 'Seleccione una habitación';

        // Update colors
        roomStatusDisplay.classList.remove('disponible', 'mantenimiento', 'ocupada');
        if (status === 'Disponible') {
            roomStatusDisplay.classList.add('disponible');
        } else if (status === 'Mantenimiento') {
            roomStatusDisplay.classList.add('mantenimiento');
        } else if (status === 'Ocupada') {
            roomStatusDisplay.classList.add('ocupada');
        }

        var roomNumber = selectedOption && selectedOption.value ? selectedOption.value : '';
        var roomType = selectedOption && selectedOption.value
            ? (selectedOption.dataset.roomType || 'N/A')
            : defaultRoomType;
        var roomPrice = selectedOption && selectedOption.value
            ? Number(selectedOption.dataset.roomPrice || 0)
            : defaultRoomPrice;

        if (reservationRoomDisplay)     reservationRoomDisplay.textContent     = roomNumber;
        if (reservationRoomTypeDisplay) reservationRoomTypeDisplay.textContent = roomType;
        if (reservationRateDisplay)     reservationRateDisplay.textContent     = formatCurrencyCOP(roomPrice);
        if (reservationTotalDisplay)    reservationTotalDisplay.textContent    = formatCurrencyCOP(roomPrice * stayNights);

        // Disable submit button if room is not Disponible
        var submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            if (selectedOption && selectedOption.value && status !== 'Disponible') {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.title = 'Habitación no disponible';
            } else if (selectedOption && selectedOption.value) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.title = '';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
            }
        }
    }

    roomNumberSelect.addEventListener('change', syncRoomStatus);
    syncRoomStatus();

    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            var submitBtn  = document.getElementById('submitBtn');
            var spinner    = document.getElementById('spinner');
            var submitIcon = document.getElementById('submitIcon');
            var submitText = document.getElementById('submitText');

            if (!submitBtn) return;
            submitBtn.disabled = true;
            if (spinner)    spinner.style.display    = 'inline-block';
            if (submitIcon) submitIcon.style.display = 'none';
            if (submitText) submitText.textContent   = 'Procesando...';
            submitBtn.style.opacity = '0.7';
        });
    }
});
