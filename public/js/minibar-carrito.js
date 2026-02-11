// Funciones para el carrito del minibar
function increaseQty(button) {
    const quantitySpan = button.parentElement.querySelector('.quantity-value');
    const currentQty = parseInt(quantitySpan.textContent);
    quantitySpan.textContent = currentQty + 1;
}

function decreaseQty(button) {
    const quantitySpan = button.parentElement.querySelector('.quantity-value');
    const currentQty = parseInt(quantitySpan.textContent);
    if (currentQty > 1) {
        quantitySpan.textContent = currentQty - 1;
    }
}
