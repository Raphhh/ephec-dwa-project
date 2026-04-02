
const productIdInput = document.getElementById('product-id')
const quantityInput = document.getElementById('quantity')
const validMessageElement = document.getElementById('basket-message-added')
const errorMessageElement = document.getElementById('basket-message-error')

function updateBasket () {

    const productId = productIdInput.value
    const quantity = quantityInput.value

    console.log('ajout de ' + quantity + ' produit(s) #' + productId)

    axios.postForm('api/basket/update.php', {
        product_id: productId,
        quantity: quantity
    })
    .then(response => {
        console.log('panier mis à jour', response.data)
        displayMessage(true)
    })
    .catch(error => {
        console.error(error)
        displayMessage(false)
    });

}

function displayMessage(isValid)
{
    validMessageElement.style.display = isValid ? 'block' : 'none'
    errorMessageElement.style.display = isValid ? 'none' : 'block'
}
