<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch cart items to verify
$stmt = $pdo->prepare("SELECT c.*, p.price, p.name, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    redirect('cart.php');
}

$subtotal = 0;
foreach($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 100 ? 0 : 15;
$total = $subtotal + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // 2. Create Order Items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach($cart_items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }

        // 3. Clear Cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        setFlashMessage('success', 'Order placed successfully!');
        redirect('user/dashboard.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage('error', 'Something went wrong. Please try again.');
    }
}

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-10">Checkout</h1>

    <form action="checkout.php" method="POST" class="flex flex-col lg:flex-row gap-10">
        <!-- Billing Details -->
        <div class="w-full lg:w-2/3 space-y-8">
            <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Shipping Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" required value="<?php echo $_SESSION['user_name']; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" required value="<?php echo $_SESSION['user_email']; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Street Address</label>
                        <input type="text" required placeholder="123 Street Name" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">ZIP Code</label>
                        <input type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Payment Method</h2>
                <div class="space-y-4">
                    <label class="flex items-center p-4 border border-primary bg-blue-50 dark:bg-blue-900/20 rounded-2xl cursor-pointer">
                        <input type="radio" name="payment" value="cod" checked class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-4">
                            <span class="block font-bold text-gray-900 dark:text-white">Cash on Delivery</span>
                            <span class="text-xs text-gray-500">Pay when you receive your order</span>
                        </div>
                        <i class="fas fa-money-bill-wave ml-auto text-primary text-xl"></i>
                    </label>
                    <label class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-2xl cursor-pointer opacity-50 grayscale">
                        <input type="radio" name="payment" value="card" disabled class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-4">
                            <span class="block font-bold text-gray-900 dark:text-white">Credit / Debit Card</span>
                            <span class="text-xs text-gray-500">Secure online payment (Coming Soon)</span>
                        </div>
                        <i class="fas fa-credit-card ml-auto text-gray-400 text-xl"></i>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="w-full lg:w-1/3">
            <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl sticky top-24">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Your Order</h2>
                
                <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                    <?php foreach($cart_items as $item): ?>
                    <div class="flex justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex-shrink-0">
                                <img src="<?php echo getProductImage($item['image'], $item['name']); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo $item['name']; ?></p>
                                <p class="text-xs text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-3 pt-6 border-t border-gray-100 dark:border-gray-700 mb-8">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span class="font-bold text-gray-900 dark:text-white">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Shipping</span>
                        <span class="font-bold text-gray-900 dark:text-white"><?php echo $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2); ?></span>
                    </div>
                    <div class="pt-4 flex justify-between">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                        <span class="text-2xl font-black text-primary">$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>

                <button type="submit" class="block w-full text-center bg-primary hover:bg-blue-600 text-white font-bold py-4 rounded-full transition shadow-xl shadow-blue-500/30 transform active:scale-95">
                    Place Order <i class="fas fa-lock ml-2 text-xs opacity-50"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
