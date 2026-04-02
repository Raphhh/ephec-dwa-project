
let elements = document.getElementsByClassName('basket-quantity-widget');
for (let i = 0; i < elements.length; i++) {
    manageWidget(elements[i])
}

function manageWidget(widget) {

    let quantityElement = widget.querySelector('.basket-quantity-widget-quantity')
    let addButton = widget.querySelector('.basket-quantity-widget-add-button')
    let removeButton = widget.querySelector('.basket-quantity-widget-remove-button')

    let quantity = parseInt(quantityElement.innerText)
    let stock = parseInt(widget.dataset.productStock)

    checkButtons(addButton, removeButton, quantity, stock)

    addButton.onclick = function () {
        quantity++
        if (quantity > stock) {
            quantity = stock
        }
        quantityElement.innerText = quantity
        checkButtons(addButton, removeButton, quantity, stock)
    }

    removeButton.onclick = function () {
        quantity--
        if (quantity < 0) {
            quantity = 0
        }
        quantityElement.innerText = quantity
        checkButtons(addButton, removeButton, quantity, stock)
    }
}

function checkButtons(addButton, removeButton, quantity, max) {
    addButton.disabled = quantity >= max
    removeButton.disabled = quantity <= 0
}
