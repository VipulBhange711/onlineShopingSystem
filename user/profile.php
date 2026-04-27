<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $user_id])) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = "Profile updated successfully!";
            $user['name'] = $name;
            $user['email'] = $email;
        } else {
            $error = "Something went wrong.";
        }
    }
}

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- User Sidebar -->
        <aside class="w-full md:w-1/4">
            <div class="glass p-8 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <div class="text-center mb-8">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=random&size=128" class="w-24 h-24 rounded-full border-4 border-primary mx-auto mb-4 shadow-lg">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo $user['name']; ?></h2>
                    <p class="text-sm text-gray-500"><?php echo $user['email']; ?></p>
                </div>
                
                <nav class="space-y-2">
                    <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="profile.php" class="flex items-center space-x-3 p-3 rounded-xl bg-primary text-white font-bold transition">
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
        <main class="w-full md:w-3/4">
            <div class="glass p-10 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-8">Profile Settings</h2>

                <?php if($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo $success; ?></span>
                    </div>
                <?php endif; ?>

                <form action="profile.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div class="pt-6">
                        <button type="submit" class="bg-primary hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition shadow-lg shadow-blue-500/30">
                            Update Profile
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-12 border-t border-gray-100 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Security</h3>
                    <p class="text-gray-500 mb-6">To change your password, please contact support or use the password recovery feature.</p>
                    <button class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold py-3 px-8 rounded-full transition hover:bg-gray-200 dark:hover:bg-gray-600">
                        Change Password
                    </button>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
