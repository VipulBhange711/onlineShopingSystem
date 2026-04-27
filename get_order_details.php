<?php
require_once 'includes/db.php';

if (!isset($_GET['order_id'])) {
    echo '<p class="text-red-500">Invalid order.</p>';
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo '<p class="text-red-500">Order not found.</p>';
    exit;
}

$stmt_items = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
require_once 'includes/functions.php';
?>

<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Order ID</p>
            <p class="font-bold text-gray-900">#<?php echo $order['id']; ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Status</p>
            <?php
            $statusClass = match($order['status']) {
                'Pending' => 'bg-yellow-100 text-yellow-700',
                'Shipped' => 'bg-blue-100 text-blue-700',
                'Delivered' => 'bg-green-100 text-green-700',
                'Cancelled' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700'
            };
            ?>
            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>"><?php echo $order['status']; ?></span>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Customer</p>
            <p class="font-bold text-gray-900"><?php echo $order['name']; ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Date</p>
            <p class="font-bold text-gray-900"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="border-t border-gray-100 pt-4 mt-4">
        <h4 class="font-bold text-gray-800 mb-3">Order Items</h4>
        <?php foreach($items as $item): ?>
        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                    <img src="<?php echo getProductImage($item['image'], $item['product_name']); ?>" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="font-medium text-gray-900"><?php echo $item['product_name']; ?></p>
                    <p class="text-xs text-gray-500">Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></p>
                </div>
            </div>
            <p class="font-bold text-gray-900">$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="bg-gray-50 rounded-xl p-4 mt-4">
        <div class="flex justify-between items-center">
            <p class="font-bold text-gray-900">Total</p>
            <p class="text-2xl font-black text-blue-600">$<?php echo number_format($order['total_price'], 2); ?></p>
        </div>
    </div>
</div>
