// Shopping cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

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
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showNotification(`${name} added to cart!`);
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
}

function updateQuantity(id, quantity) {
    const item = cart.find(item => item.id === id);
    if (item) {
        if (quantity <= 0) {
            removeFromCart(id);
        } else {
            item.quantity = quantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        }
    }
}

function updateCartUI() {
    const cartCount = document.getElementById('cart-count');
    const cartItems = document.getElementById('cart-items');
    const totalElement = document.getElementById('total');
    
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
    
    if (cartItems) {
        if (cart.length === 0) {
            cartItems.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">Your cart is empty</p>';
        } else {
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${escapeHtml(item.name)}</div>
                        <div class="cart-item-quantity">
                            Qty: <input type="number" min="1" value="${item.quantity}" 
                                       onchange="updateQuantity(${item.id}, this.value)" 
                                       style="width: 50px; padding: 4px;">
                        </div>
                    </div>
                    <div class="cart-item-price">$${(item.price * item.quantity).toFixed(2)}</div>
                    <button class="cart-item-remove" onclick="removeFromCart(${item.id})">Remove</button>
                </div>
            `).join('');
        }
    }
    
    if (totalElement) {
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        totalElement.textContent = total.toFixed(2);
    }
}

function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }
    
    // Create a form and submit it to checkout.php
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout.php';
    
    const cartInput = document.createElement('input');
    cartInput.type = 'hidden';
    cartInput.name = 'cart';
    cartInput.value = JSON.stringify(cart);
    
    form.appendChild(cartInput);
    document.body.appendChild(form);
    form.submit();
}

function clearCart() {
    cart = [];
    localStorage.removeItem('cart');
    updateCartUI();
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function switchTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Initialize cart UI on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});