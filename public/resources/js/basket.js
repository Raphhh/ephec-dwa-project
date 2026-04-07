
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
    let productId = parseInt(widget.dataset.productId)

    checkButtons(addButton, removeButton, quantity, stock)

    addButton.onclick = function () {
        quantity++
        if (quantity > stock) {
            quantity = stock
        }
        quantityElement.innerText = quantity
        checkButtons(addButton, removeButton, quantity, stock)
        updateRemote(productId, quantity)
    }

    removeButton.onclick = function () {
        quantity--
        if (quantity < 0) {
            quantity = 0
        }
        quantityElement.innerText = quantity
        checkButtons(addButton, removeButton, quantity, stock)
        updateRemote(productId, quantity)
    }
}

function checkButtons(addButton, removeButton, quantity, max) {
    addButton.disabled = quantity >= max
    removeButton.disabled = quantity <= 0
}

function updateRemote(productId, quantity) {
    axios.postForm('api/basket/update.php', {
        product_id: productId,
        quantity: quantity
    })
    .then(response => {
        console.log('panier mis à jour', response.data)
        updateBasketItemDisplay(
            findBasketItemByProductId(response.data.basket.items, productId)
        )
        updateBasketTotalDisplay(response.data.basket.total)
    })
    .catch(error => {
        console.error(error)
    });
}

function updateBasketItemDisplay(item) {
    console.log('item update', item)
    let rowElement = document.getElementById('item-' + item.product.id)
    rowElement.querySelector('.item-total-htva').innerText = item.total_htva
}

function updateBasketTotalDisplay(total) {
    console.log('total update', total)
    document.getElementById('basket-total-count').innerText = total.count
    document.getElementById('basket-total-htva').innerText = total.htva
    document.getElementById('basket-total-tvac').innerText = total.tvac
}

function findBasketItemByProductId(items, productId) {
    for (let i = 0; i < items.length; i++) {
        if (items[i].product.id == productId) {
            return items[i]
        }
    }
    return {
        product: {
            id: productId
        },
        total_htva: '0 €'
    }
}
