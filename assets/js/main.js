// Add to Cart global function
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(SITE_URL + 'includes/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in UI
            const cartCount = document.getElementById('cart-count');
            if (cartCount) cartCount.innerText = data.cart_count;
            
            // Show success toast
            showToast('success', data.message);
        } else {
            // Show error toast or redirect to login
            if (data.message.includes('login')) {
                window.location.href = SITE_URL + 'login.php';
            } else {
                showToast('error', data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Something went wrong. Please try again.');
    });
}

// Toast notification system
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-20 right-4 z-[100] transform transition-all duration-500 translate-x-full`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    toast.innerHTML = `
        <div class="${bgColor} text-white px-6 py-3 rounded-lg shadow-2xl flex items-center space-x-3">
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="hover:opacity-75"><i class="fas fa-times"></i></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animation
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }, 100);
}

// Initial cart count check
document.addEventListener('DOMContentLoaded', () => {
    if (typeof SITE_URL !== 'undefined') {
        fetch(SITE_URL + 'includes/cart_handler.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'get_count' })
        })
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount && data.count !== undefined) cartCount.innerText = data.count;
        })
        .catch(err => console.error('Cart count error:', err));
    }
});
