let cart = [];
let total = 0;

function addToCart(id, name, price) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    updateCart();
}

function updateCart() {
    const cartItemsElement = document.getElementById('cart-items');
    const totalElement = document.getElementById('cart-total');
    
    cartItemsElement.innerHTML = '';
    total = 0;
    
    cart.forEach(item => {
        const listItem = document.createElement('li');
        listItem.textContent = `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
        cartItemsElement.appendChild(listItem);
        
        total += item.price * item.quantity;
    });
    
    totalElement.textContent = total.toFixed(2);
}

document.getElementById('checkout-btn').addEventListener('click', function() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    alert('Thank you for your purchase!');
    cart = [];
    updateCart();
});