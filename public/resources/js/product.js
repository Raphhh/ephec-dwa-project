import axios from "https://cdn.jsdelivr.net/npm/axios@1.6.8/+esm";

        
const form = document.getElementById('basket-form');

form.onsubmit = function(event) {
    if (!form.checkValidity()) {
        return;
    }

    event.preventDefault()

    const productId = form.elements['product-id'].value
    const quantity = form.elements['quantity'].value

    console.log('ajout de ' + quantity + ' produit(s) #' + productId)

    axios.post("api/basket/add.php", {
        product_id: productId,
        quantity: quantity
    })
    .then(response => {
        console.log('panier mis à jour', response.data)
        //form.style. display = 'none'
        document.getElementById('basket-message-added').style.display = 'block'
        document.getElementById('basket-message-error').style.display = 'none'
    })
    .catch(error => {
        console.error(error)
        document.getElementById('basket-message-added').style.display = 'none'
        document.getElementById('basket-message-error').style.display = 'block'
    });


};
