<?php
include 'includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent self-deletion
    if ($id === $_SESSION['user_id']) {
        setFlashMessage('error', 'You cannot delete your own account!');
        redirect('users.php');
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    if ($stmt->execute([$id])) {
        setFlashMessage('success', 'User deleted successfully!');
    } else {
        setFlashMessage('error', 'Cannot delete admin users!');
    }
    redirect('users.php');
}

// Fetch Users (exclude current admin)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

// Stats
$total_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$active_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-black text-gray-800">Manage Users</h2>
    <div class="flex space-x-4">
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-black text-blue-600"><?php echo $total_customers; ?></p>
            <p class="text-xs text-gray-500 uppercase font-bold">Total Customers</p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-black text-green-600"><?php echo $active_customers; ?></p>
            <p class="text-xs text-gray-500 uppercase font-bold">New This Month</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-8 py-4">User</th>
                    <th class="px-8 py-4">Email</th>
                    <th class="px-8 py-4">Role</th>
                    <th class="px-8 py-4">Joined</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($users as $user): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-8 py-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <p class="font-bold text-gray-900"><?php echo $user['name']; ?></p>
                        </div>
                    </td>
                    <td class="px-8 py-4 text-sm text-gray-600"><?php echo $user['email']; ?></td>
                    <td class="px-8 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td class="px-8 py-4 text-sm text-gray-500"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td class="px-8 py-4 text-right">
                        <?php if($user['role'] === 'user'): ?>
                        <a href="users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition"><i class="fas fa-trash-alt"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
