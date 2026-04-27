<?php
include 'includes/header.php';

// Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?? 0;

// Recent Orders
$recent_orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Users</p>
            <h3 class="text-2xl font-bold text-gray-900"><?php echo $total_users; ?></h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
            <i class="fas fa-box text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Products</p>
            <h3 class="text-2xl font-bold text-gray-900"><?php echo $total_products; ?></h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
            <i class="fas fa-shopping-cart text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Orders</p>
            <h3 class="text-2xl font-bold text-gray-900"><?php echo $total_orders; ?></h3>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
            <i class="fas fa-dollar-sign text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Revenue</p>
            <h3 class="text-2xl font-bold text-gray-900">$<?php echo number_format($total_revenue, 2); ?></h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Orders</h3>
            <a href="orders.php" class="text-xs text-blue-500 font-bold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($recent_orders as $order): ?>
                    <tr class="text-sm hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">#ORD-<?php echo $order['id']; ?></td>
                        <td class="px-6 py-4"><?php echo $order['user_name']; ?></td>
                        <td class="px-6 py-4 font-bold">$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase <?php 
                                echo $order['status'] === 'Delivered' ? 'bg-green-100 text-green-700' : 
                                    ($order['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'); 
                            ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Alert (Mockup) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Low Stock Alerts</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">iPhone 13</p>
                        <p class="text-xs text-gray-500">Only 2 items left</p>
                    </div>
                </div>
                <button class="text-xs bg-red-500 text-white px-3 py-1 rounded-full font-bold hover:bg-red-600 transition">Restock</button>
            </div>
            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-yellow-500 shadow-sm">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Nike Air Max</p>
                        <p class="text-xs text-gray-500">Only 5 items left</p>
                    </div>
                </div>
                <button class="text-xs bg-yellow-500 text-white px-3 py-1 rounded-full font-bold hover:bg-yellow-600 transition">Restock</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
