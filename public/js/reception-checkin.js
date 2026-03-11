// Check-in form: room status sync + currency display + submit spinner
// PHP data injected via window.CheckInConfig (set inline in check_in.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var config = window.CheckInConfig || {};
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
