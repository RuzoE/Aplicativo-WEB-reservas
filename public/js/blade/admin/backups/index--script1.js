document.addEventListener('DOMContentLoaded', function () {
    const generateForm = document.getElementById('backup-generate-form');
    const generateButton = document.getElementById('generate-backup-btn');

    // -------- MANUAL BACKUP GENERATION (POLLING) --------
    let pollingInterval = null;
    let backupRequestInFlight = false;

    if (generateForm && generateButton) {
        generateForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (generateButton.classList.contains('disabled') || backupRequestInFlight) return;

            backupRequestInFlight = true;
            setGenerateButtonState(true, 'Generando respaldo...');

            // Bloqueo total con SweetAlert2 para evitar interrupciones
            Swal.fire({
                title: 'Generando Respaldo',
                html: 'Estamos preparando el archivo SQL y subiéndolo a Google Drive.<br><br><b>Por favor, no cierres esta ventana.</b>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(generateForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(data?.message || 'No se pudo completar el backup.');
                }

                return data;
            })
            .then(data => {
                if (data?.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Respaldo completado correctamente.',
                        confirmButtonText: 'Genial'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data?.message || 'No se pudo completar el backup.');
                }
            })
            .catch(error => {
                console.error('Error in backup:', error);
                setGenerateButtonState(false);
                Swal.fire('Error', error.message || 'El servidor tardó demasiado o hubo un fallo de conexión.', 'error');
            })
            .finally(() => {
                backupRequestInFlight = false;
            });
        });
    }

    function setGenerateButtonState(isLoading, text = null) {
        if (!generateButton) return;
        if (isLoading) {
            generateButton.classList.add('disabled');
            generateButton.setAttribute('disabled', 'disabled');
            generateButton.style.opacity = '0.75';
            generateButton.innerHTML = `<i class="bi bi-arrow-repeat"></i><span>${text || 'Backup en proceso...'}</span>`;
        } else {
            generateButton.classList.remove('disabled');
            generateButton.removeAttribute('disabled');
            generateButton.style.opacity = '1';
            generateButton.innerHTML = '<i class="bi bi-cloud-arrow-up"></i><span>Respaldar Base de Datos</span>';
        }
    }

    function startStatusPolling() {
        if (pollingInterval) clearInterval(pollingInterval);

        let attempts = 0;
        const maxAttempts = 120; // 6 minutes max (3s * 120)

        pollingInterval = setInterval(() => {
            attempts++;
            if (attempts > maxAttempts) {
                stopPolling();
                Swal.fire('Atención', 'El backup está tomando más tiempo de lo esperado. Revisa el panel en unos minutos.', 'info');
                return;
            }

            fetch('/admin/backups/status', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const status = data.summary.last_status;
                const message = data.summary.last_message;
                const lastRunAt = data.settings.last_run_at;

                // Si detectamos que ya no está "En proceso", significa que terminó
                if (status === 'Correcto') {
                    stopPolling();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Respaldo Exitoso!',
                        text: 'La base de datos se ha respaldado y subido a Google Drive correctamente.',
                        confirmButtonText: 'Genial',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload(); // Recarga automática de la lista
                    });
                } else if (status === 'Error') {
                    stopPolling();
                    Swal.fire({
                        icon: 'error',
                        title: 'Fallo en el Respaldo',
                        text: message || 'Hubo un inconveniente al generar la copia.',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            })
            .catch(err => {
                console.error('Polling error:', err);
            });
        }, 3000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
        setGenerateButtonState(false);
    }

    // Auto-resume polling if page is loaded while "En proceso"
    const initialStateText = generateButton?.innerText || '';
    if (initialStateText.includes('proceso')) {
        startStatusPolling();
    }

    // -------- DELETE LOGIC --------
    document.querySelectorAll('.delete-backup-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const backupName = form.getAttribute('data-backup-name') || 'este backup';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar el backup: ${backupName}. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // -------- RESTORE MODAL & LOGIC --------
    const restoreBtns = document.querySelectorAll('.restore-backup-btn');
    const restoreModalEl = document.getElementById('restoreModal');
    const modalRestore = restoreModalEl ? new bootstrap.Modal(restoreModalEl) : null;
    const confirmBtn = document.getElementById('confirm-restore-btn');
    const confirmInput = document.getElementById('restore-confirmation');
    const overlay = document.getElementById('restore-overlay');

    let currentRestorePath = null;
    let restoreInProgress = false;

    restoreBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentRestorePath = this.getAttribute('data-path');
            document.getElementById('modal-backup-name').textContent = this.getAttribute('data-name');
            document.getElementById('modal-backup-date').textContent = this.getAttribute('data-date');
            document.getElementById('modal-backup-size').textContent = this.getAttribute('data-size');

            if (confirmInput) confirmInput.value = '';
            if (confirmBtn) confirmBtn.classList.add('disabled');

            if (modalRestore) {
                modalRestore.hide();
                setTimeout(() => modalRestore.show(), 150);
            }
        });
    });

    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            if (this.value.trim() === 'CONFIRMAR') {
                confirmBtn.classList.remove('disabled');
            } else {
                confirmBtn.classList.add('disabled');
            }
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (confirmInput.value.trim() !== 'CONFIRMAR') return;
            if (!currentRestorePath || restoreInProgress) return;

            restoreInProgress = true;
            confirmBtn.classList.add('disabled');
            confirmBtn.setAttribute('disabled', 'disabled');
            restoreBtns.forEach(btn => btn.classList.add('disabled'));

            if (modalRestore) modalRestore.hide();
            if (overlay) overlay.style.display = 'flex';

            fetch('/admin/backups/restore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
                },
                body: JSON.stringify({ path: currentRestorePath })
            })
            .then(async response => {
                const data = await response.json().catch(() => null);
                if (!response.ok) {
                    throw new Error(data?.message || 'Error del servidor. (timeout o fallo)');
                }
                return data;
            })
            .then(data => {
                if (overlay) overlay.style.display = 'none';
                if (data && data.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Restaurado',
                        text: data.message || 'La base de datos ha sido restaurada con éxito.',
                        confirmButtonText: data?.redirect ? 'Ir a iniciar sesión' : 'OK'
                    }).then(() => {
                        if (data?.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data?.message || 'Hubo un error inesperado.'
                    });
                }
            })
            .catch(error => {
                if (overlay) overlay.style.display = 'none';
                restoreInProgress = false;
                confirmBtn.classList.remove('disabled');
                confirmBtn.removeAttribute('disabled');
                restoreBtns.forEach(btn => btn.classList.remove('disabled'));

                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: error.message || 'Se perdió la conexión o la operación tomó demasiado tiempo.',
                });
                console.error('Error:', error);
            });
        });
    }
});
