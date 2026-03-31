function prepareReservation(roomId) {
    const input = document.getElementById('modal_room_id');
    if (input) {
        input.value = roomId;
    }
}
