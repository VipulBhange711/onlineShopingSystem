<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.*, cat.name as category_name 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       JOIN categories cat ON p.category_id = cat.id
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$subtotal = 0;
foreach($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 100 ? 0 : 15;
$total = $subtotal + $shipping;

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-10 flex items-center">
        <i class="fas fa-shopping-basket mr-4 text-primary"></i> Your Shopping Cart
    </h1>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-20 glass rounded-3xl">
            <div class="bg-gray-100 dark:bg-gray-800 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your cart is empty</h2>
            <p class="text-gray-500 mt-2 mb-8">Looks like you haven't added anything to your cart yet.</p>
            <a href="products.php" class="inline-flex items-center px-8 py-4 bg-primary text-white font-bold rounded-full hover:bg-blue-600 transition shadow-lg shadow-blue-500/30">
                Start Shopping <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Cart Items -->
            <div class="w-full lg:w-2/3 space-y-6">
                <?php foreach($cart_items as $item): ?>
                <div class="glass p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-lg flex flex-col sm:flex-row items-center gap-6 group">
                    <div class="w-full sm:w-32 h-32 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                        <img src="<?php echo getProductImage($item['image'], $item['name']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo $item['category_name']; ?></p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate"><?php echo $item['name']; ?></h3>
                        <p class="text-primary font-black text-lg mt-1">$<?php echo number_format($item['price'], 2); ?></p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-full p-1 bg-white dark:bg-gray-800 shadow-sm">
                            <button onclick="updateCart(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition"><i class="fas fa-minus text-[10px]"></i></button>
                            <span class="w-10 text-center font-bold dark:text-white"><?php echo $item['quantity']; ?></span>
                            <button onclick="updateCart(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition"><i class="fas fa-plus text-[10px]"></i></button>
                        </div>
                        <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="pt-6 flex justify-between">
                    <a href="products.php" class="text-primary font-bold hover:underline flex items-center">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-900 dark:text-white">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span class="font-bold text-gray-900 dark:text-white"><?php echo $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2); ?></span>
                        </div>
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                            <span class="text-2xl font-black text-primary">$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <a href="checkout.php" class="block w-full text-center bg-primary hover:bg-blue-600 text-white font-bold py-4 rounded-full transition shadow-xl shadow-blue-500/30 transform active:scale-95 mb-4">
                        Proceed to Checkout
                    </a>
                    
                    <div class="flex items-center justify-center space-x-2 text-gray-400 text-xs">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure Checkout powered by ShopEase</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateCart(cartId, qty) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_id', cartId);
    formData.append('quantity', qty);

    fetch('includes/cart_handler.php', {
        method: 'POST',
        body: formData
    }).then(() => location.reload());
}

function removeFromCart(cartId) {
    if (confirm('Remove this item from cart?')) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('cart_id', cartId);

        fetch('includes/cart_handler.php', {
            method: 'POST',
            body: formData
        }).then(() => location.reload());
    }
}
</script>

<?php include 'includes/footer.php'; ?>
