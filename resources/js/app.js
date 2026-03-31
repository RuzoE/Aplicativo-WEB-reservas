import './bootstrap';
import { createApp } from 'vue';
import RoomAssignmentBoard from './components/RoomAssignmentBoard.vue';

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
        csrfToken: mountElement.dataset.csrfToken ?? ''
    }).mount(mountElement);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountRoomAssignmentBoard, { once: true });
} else {
    mountRoomAssignmentBoard();
}

import '../css/app.css';

