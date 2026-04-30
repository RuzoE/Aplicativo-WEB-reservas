import './bootstrap';
import { createApp } from 'vue';
import RoomAssignmentBoard from './components/RoomAssignmentBoard.vue';
import ReservationDateModifier from './components/ReservationDateModifier.vue';

function parseJsonAttribute(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function mountRoomAssignmentBoard() {
    const mountElement = document.getElementById('room-assignment-board-root');

    if (!mountElement) {
        return;
    }

    createApp(RoomAssignmentBoard, {
        rooms: parseJsonAttribute(mountElement.dataset.rooms, []),
        selectedOrder: parseJsonAttribute(mountElement.dataset.selectedOrder, null),
        assignUrl: mountElement.dataset.assignUrl ?? '',
        csrfToken: mountElement.dataset.csrfToken ?? '',
        roomsByDateUrl: mountElement.dataset.roomsByDateUrl ?? ''
    }).mount(mountElement);
}

function mountReservationDateModifier() {
    const mountElement = document.getElementById('reservation-date-modifier-root');

    if (!mountElement) {
        return;
    }

    createApp(ReservationDateModifier, {
        orderId: mountElement.dataset.orderId,
        initialCheckIn: mountElement.dataset.currentCheckIn,
        duration: mountElement.dataset.duration,
        updateUrl: mountElement.dataset.updateUrl,
        csrfToken: mountElement.dataset.csrfToken
    }).mount(mountElement);
}

function mountComponents() {
    mountRoomAssignmentBoard();
    mountReservationDateModifier();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountComponents, { once: true });
} else {
    mountComponents();
}

import '../css/app.css';

