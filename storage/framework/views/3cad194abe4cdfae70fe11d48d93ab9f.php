<style>
    .checkin-section {
        padding: 40px 60px;
        max-width: 100%;
    }

    .checkin-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .checkin-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .checkin-header p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .checkin-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .checkin-card h3 {
        color: #333;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .btn-search {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: white;
    }

    .btn-back {
        background: linear-gradient(135deg, #757575 0%, #424242 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(117, 117, 117, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(117, 117, 117, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Estilos mejorados para la tabla */
    .table-reservations {
        font-size: 0.9rem;
        width: 100%;
        table-layout: auto;
    }

    .table-reservations th {
        background: #f8f9fa;
        font-weight: 700;
        white-space: nowrap;
        padding: 14px 10px;
        font-size: 0.85rem;
    }

    .table-reservations td {
        vertical-align: middle;
        padding: 14px 10px;
        font-size: 0.9rem;
    }

    .table-reservations .badge {
        font-size: 0.8rem;
        padding: 5px 10px;
    }

    /* Columnas específicas */
    .table-reservations th:nth-child(1),
    .table-reservations td:nth-child(1) {
        width: 10%;
    }

    .table-reservations th:nth-child(2),
    .table-reservations td:nth-child(2) {
        width: 12%;
    }

    .table-reservations th:nth-child(3),
    .table-reservations td:nth-child(3) {
        width: 16%;
        font-size: 0.8rem;
    }

    .table-reservations th:nth-child(4),
    .table-reservations td:nth-child(4) {
        width: 10%;
    }

    .table-reservations th:nth-child(5),
    .table-reservations td:nth-child(5) {
        width: 10%;
    }

    .table-reservations th:nth-child(6),
    .table-reservations td:nth-child(6) {
        width: 10%;
    }

    .table-reservations th:nth-child(7),
    .table-reservations td:nth-child(7) {
        width: 10%;
    }

    .table-reservations th:nth-child(8),
    .table-reservations td:nth-child(8) {
        width: 8%;
    }

    .table-reservations th:nth-child(9),
    .table-reservations td:nth-child(9) {
        width: 14%;
        text-align: center;
    }

    .btn-process {
        background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
        text-decoration: none;
        display: inline-block;
    }

    .btn-process:hover {
        background: linear-gradient(135deg, #F57C00 0%, #E65100 100%);
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }
</style>

<div class="checkin-section">
    <div class="checkin-header">
        <h2><i class="bi bi-door-open"></i> Check-in</h2>
        <p>Selecciona una reserva pendiente para completar el check-in</p>
    </div>

    <div class="checkin-card">
        <h3>Reservas Pendientes de Check-in</h3>

        <div id="ci-sim-result" class="mt-4">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando reservas...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultDiv = document.getElementById('ci-sim-result');

    // Cargar reservas automáticamente al cargar la página
    function loadReservations() {
        fetch('<?php echo e(route('reception.checkin.search')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="table-responsive"><table class="table table-hover table-striped table-reservations">';
                html += '<thead><tr><th>Código</th><th>Huésped</th><th>Email</th><th>Habitación</th><th>Tipo</th><th>Check-in</th><th>Check-out</th><th>Total</th><th style="text-align:center">Acción</th></tr></thead><tbody>';

                data.reservations.forEach(reservation => {
                    // Color del badge según si tiene habitación asignada
                    let roomBadge = reservation.room === 'Sin asignar'
                        ? '<span class="badge bg-warning text-dark">Sin asignar</span>'
                        : `<span class="badge bg-info">${reservation.room}</span>`;

                    html += `<tr>
                        <td><strong>${reservation.codigo}</strong></td>
                        <td>${reservation.guest_name}</td>
                        <td><small>${reservation.guest_email}</small></td>
                        <td>${roomBadge}</td>
                        <td>${reservation.room_type}</td>
                        <td>${reservation.check_in}</td>
                        <td>${reservation.check_out}</td>
                        <td><strong>$${reservation.total}</strong></td>
                        <td style="text-align:center"><a href="<?php echo e(url('reception/check-in')); ?>/${reservation.id}" class="btn btn-process"><i class="bi bi-box-arrow-in-right"></i> Procesar</a></td>
                    </tr>`;
                });

                html += '</tbody></table></div>';
                resultDiv.innerHTML = html;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-info"><i class="bi bi-info-circle"></i> ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al cargar reservas. Intente nuevamente.</div>';
        });
    }

    // Cargar reservas al iniciar
    loadReservations();

    // Recargar cada 30 segundos
    setInterval(loadReservations, 30000);
});
</script>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/reception/partials/checkin-form.blade.php ENDPATH**/ ?>