<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Statistics
$total_orders = count($orders);
$total_spent = 0;
foreach($orders as $o) {
    if($o['status'] !== 'Cancelled') $total_spent += $o['total_price'];
}

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- User Sidebar -->
        <aside class="w-full md:w-1/4">
            <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <div class="text-center mb-8">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random&size=128" class="w-24 h-24 rounded-full border-4 border-primary mx-auto mb-4 shadow-lg">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo $_SESSION['user_name']; ?></h2>
                    <p class="text-sm text-gray-500"><?php echo $_SESSION['user_email']; ?></p>
                </div>
                
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-xl bg-primary text-white font-bold transition">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="profile.php" class="flex items-center space-x-3 p-3 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-user"></i>
                        <span>Profile Settings</span>
                    </a>
                    <hr class="my-4 border-gray-100 dark:border-gray-800">
                    <a href="../logout.php" class="flex items-center space-x-3 p-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4 space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg flex items-center space-x-6">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-2xl text-primary"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Orders</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?php echo $total_orders; ?></h3>
                    </div>
                </div>
                <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg flex items-center space-x-6">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-wallet text-2xl text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Spent</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white">$<?php echo number_format($total_spent, 2); ?></h3>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="glass rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-8 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Orders</h2>
                    <a href="../products.php" class="text-sm text-primary font-bold hover:underline">Shop More</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-500">You haven't placed any orders yet.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($orders as $order): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-8 py-6 font-bold text-gray-900 dark:text-white">#ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td class="px-8 py-6 text-sm text-gray-600 dark:text-gray-400"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td class="px-8 py-6 font-bold text-gray-900 dark:text-white">$<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td class="px-8 py-6">
                                        <?php 
                                        $statusClass = [
                                            'Pending' => 'bg-yellow-100 text-yellow-700',
                                            'Shipped' => 'bg-blue-100 text-blue-700',
                                            'Delivered' => 'bg-green-100 text-green-700',
                                            'Cancelled' => 'bg-red-100 text-red-700'
                                        ];
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?php echo $statusClass[$order['status']]; ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button class="text-primary hover:text-blue-700 font-bold text-sm">View Details</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
