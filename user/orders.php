<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Stats
$total_orders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$total_orders->execute([$user_id]);
$total_orders = $total_orders->fetchColumn();

$total_spent = $pdo->prepare("SELECT SUM(total_price) FROM orders WHERE user_id = ? AND status != 'Cancelled'");
$total_spent->execute([$user_id]);
$total_spent = $total_spent->fetchColumn() ?? 0;
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); }
        .glass-dark { background: rgba(15,23,42,0.85); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 min-h-screen">
    <!-- Navigation -->
    <nav class="glass sticky top-0 z-50 border-b border-white/20 dark:border-slate-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="../index.php" class="text-2xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"><?php echo SITE_NAME; ?></a>
                <div class="flex space-x-6 items-center">
                    <a href="../index.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition"><i class="fas fa-home mr-1"></i> Home</a>
                    <a href="dashboard.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition"><i class="fas fa-box mr-1"></i> My Orders</a>
                    <a href="profile.php" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 transition"><i class="fas fa-user mr-1"></i> Profile</a>
                    <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl transition text-sm font-bold"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Orders</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white"><?php echo $total_orders; ?></p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Spent</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white">$<?php echo number_format($total_spent, 2); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Account Status</p>
                        <p class="text-3xl font-black text-green-600">Active</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-slate-700">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Order History</h2>
            </div>
            <?php if(empty($orders)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">No orders yet. Start shopping!</p>
                <a href="../index.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition">Browse Products</a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="px-6 py-4 font-bold">Order ID</th>
                            <th class="px-6 py-4 font-bold">Items</th>
                            <th class="px-6 py-4 font-bold">Total</th>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold">Date</th>
                            <th class="px-6 py-4 font-bold">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        <?php foreach($orders as $order): ?>
                        <?php
                        $items_count = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                        $items_count->execute([$order['id']]);
                        $count = $items_count->fetchColumn();
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?php echo $order['id']; ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300"><?php echo $count; ?> items</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td class="px-6 py-4">
                                <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View Details</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-700/50">
                <h3 class="text-xl font-black text-gray-800 dark:text-white">Order Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><i class="fas fa-times"></i></button>
            </div>
            <div id="modalContent" class="p-6 max-h-96 overflow-y-auto"></div>
        </div>
    </div>

    <script>
    function viewOrderDetails(orderId) {
        fetch(`../get_order_details.php?order_id=${orderId}`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('orderModal').classList.remove('hidden');
            });
    }
    function closeModal() {
        document.getElementById('orderModal').classList.add('hidden');
    }
    </script>
</body>
</html>
