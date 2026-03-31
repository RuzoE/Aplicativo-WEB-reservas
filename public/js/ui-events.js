document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (event) => {
        const dismissBtn = event.target.closest('[data-dismiss-parent]');
        if (dismissBtn) {
            const parentSelector = dismissBtn.getAttribute('data-dismiss-parent');
            const parent = parentSelector ? dismissBtn.closest(parentSelector) : null;
            if (parent) parent.remove();
            return;
        }

        const reserveBtn = event.target.closest('[data-reserve-room-id]');
        if (reserveBtn && typeof window.prepareReservation === 'function') {
            window.prepareReservation(reserveBtn.getAttribute('data-reserve-room-id'));
            return;
        }

        const resetImageBtn = event.target.closest('[data-action="reset-image-upload"]');
        if (resetImageBtn && typeof window.resetImageUpload === 'function') {
            window.resetImageUpload();
            return;
        }

        const toggleImageBtn = event.target.closest('[data-action="toggle-image-field"]');
        if (toggleImageBtn && typeof window.toggleImageField === 'function') {
            window.toggleImageField();
            return;
        }

        const openOrderBtn = event.target.closest('.js-open-create-order-general');
        if (openOrderBtn && typeof window.openCreateOrderModalGeneral === 'function') {
            window.openCreateOrderModalGeneral();
            return;
        }

        const completeOrderBtn = event.target.closest('.js-complete-order');
        if (completeOrderBtn && typeof window.completeOrder === 'function') {
            const orderId = completeOrderBtn.getAttribute('data-order-id');
            if (orderId) window.completeOrder(orderId);
            return;
        }

        const historyBtn = event.target.closest('.js-view-history');
        if (historyBtn && typeof window.viewHistory === 'function') {
            window.viewHistory(
                historyBtn.getAttribute('data-room-id'),
                historyBtn.getAttribute('data-room-number')
            );
            return;
        }

        const closeModalBtn = event.target.closest('.js-close-modal');
        if (closeModalBtn && typeof window.closeModal === 'function') {
            const modalId = closeModalBtn.getAttribute('data-modal-id');
            if (modalId) window.closeModal(modalId);
            return;
        }

        const confirmCancelBtn = event.target.closest('.js-confirm-cancel');
        if (confirmCancelBtn && typeof window.closeConfirmModal === 'function') {
            window.closeConfirmModal();
            return;
        }

        const confirmAcceptBtn = event.target.closest('.js-confirm-accept');
        if (confirmAcceptBtn && typeof window.confirmAction === 'function') {
            window.confirmAction();
            return;
        }

        const qtyIncBtn = event.target.closest('.js-qty-inc');
        if (qtyIncBtn && typeof window.increaseQty === 'function') {
            window.increaseQty(qtyIncBtn);
            return;
        }

        const qtyDecBtn = event.target.closest('.js-qty-dec');
        if (qtyDecBtn && typeof window.decreaseQty === 'function') {
            window.decreaseQty(qtyDecBtn);
            return;
        }

        const pdIncBtn = event.target.closest('.js-pd-inc');
        if (pdIncBtn && typeof window.pdInc === 'function') {
            window.pdInc();
            return;
        }

        const pdDecBtn = event.target.closest('.js-pd-dec');
        if (pdDecBtn && typeof window.pdDec === 'function') {
            window.pdDec();
            return;
        }

        const checkoutBtn = event.target.closest('.js-checkout-submit');
        if (checkoutBtn && typeof window.submitCheckout === 'function') {
            window.submitCheckout();
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm-message]');
        if (!form) return;

        const message = form.getAttribute('data-confirm-message') || '¿Confirmar acción?';
        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });

    document.addEventListener('input', (event) => {
        const input = event.target.closest('input[data-phone-sanitize]');
        if (!input) return;

        input.value = input.value
            .replace(/[^0-9+]/g, '')
            .replace(/(?!^)\+/g, '')
            .slice(0, 16);
    });

    document.addEventListener('change', (event) => {
        const select = event.target.closest('.js-sync-room-type');
        if (select && typeof window.syncSelectedRoomTypeId === 'function') {
            window.syncSelectedRoomTypeId();
        }
    });
});
