<?php
include 'includes/header.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $order_id])) {
        setFlashMessage('success', 'Order status updated!');
    }
    redirect('orders.php');
}

$orders = $pdo->query("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-black text-gray-800">Manage Orders</h2>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-8 py-4">Order ID</th>
                    <th class="px-8 py-4">Customer</th>
                    <th class="px-8 py-4">Total</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Date</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($orders as $order): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-8 py-4 font-bold text-gray-900">#<?php echo $order['id']; ?></td>
                    <td class="px-8 py-4">
                        <p class="font-bold text-gray-900"><?php echo $order['user_name']; ?></p>
                        <p class="text-xs text-gray-500"><?php echo $order['user_email']; ?></p>
                    </td>
                    <td class="px-8 py-4 font-bold text-gray-900">$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td class="px-8 py-4">
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
                    <td class="px-8 py-4 text-sm text-gray-500"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td class="px-8 py-4 text-right">
                        <button onclick="toggleModal('orderModal<?php echo $order['id']; ?>')" class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>

                <!-- Order Details Modal -->
                <div id="orderModal<?php echo $order['id']; ?>" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="text-xl font-black text-gray-800">Order #<?php echo $order['id']; ?></h3>
                            <button onclick="toggleModal('orderModal<?php echo $order['id']; ?>')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-500 mb-4">Customer: <span class="text-gray-900 font-bold"><?php echo $order['user_name']; ?></span></p>
                            <p class="text-sm text-gray-500 mb-4">Total: <span class="text-gray-900 font-bold">$<?php echo number_format($order['total_price'], 2); ?></span></p>
                            <form action="orders.php" method="POST" class="space-y-4">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Update Status</label>
                                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">Update Status</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleModal(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>

<?php include 'includes/footer.php'; ?>
