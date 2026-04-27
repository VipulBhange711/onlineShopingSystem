<?php 
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Shopping Website</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .dark .glass { background: rgba(17, 24, 39, 0.7); }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#10b981',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Navbar -->
    <nav class="glass sticky top-0 z-50 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo SITE_URL; ?>index.php" class="text-2xl font-bold text-primary">Shop<span class="text-gray-900 dark:text-white">Ease</span></a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo SITE_URL; ?>index.php" class="text-gray-600 dark:text-gray-300 hover:text-primary transition">Home</a>
                    <a href="<?php echo SITE_URL; ?>products.php" class="text-gray-600 dark:text-gray-300 hover:text-primary transition">Products</a>
                    <div class="relative group">
                        <button class="text-gray-600 dark:text-gray-300 hover:text-primary transition">Categories <i class="fas fa-chevron-down text-xs"></i></button>
                        <div class="absolute hidden group-hover:block w-48 bg-white dark:bg-gray-800 shadow-xl rounded-lg py-2 mt-0 border border-gray-100 dark:border-gray-700">
                            <!-- Categories will be dynamic later -->
                            <a href="<?php echo SITE_URL; ?>products.php?category=1" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Electronics</a>
                            <a href="<?php echo SITE_URL; ?>products.php?category=2" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Fashion</a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button id="darkModeToggle" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block"></i>
                    </button>
                    
                    <a href="<?php echo SITE_URL; ?>cart.php" class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
                    </a>

                    <?php if(isLoggedIn()): ?>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 p-1 focus:outline-none">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random" class="w-8 h-8 rounded-full border-2 border-primary">
                                <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $_SESSION['user_name']; ?></span>
                            </button>
                            <div class="absolute right-0 hidden group-hover:block w-48 bg-white dark:bg-gray-800 shadow-xl rounded-lg py-2 mt-0 border border-gray-100 dark:border-gray-700">
                                <a href="<?php echo SITE_URL; ?>user/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                                <a href="<?php echo SITE_URL; ?>user/profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"><i class="fas fa-user mr-2"></i> Profile</a>
                                <?php if(isAdmin()): ?>
                                    <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-gray-700"><i class="fas fa-user-shield mr-2"></i> Admin Panel</a>
                                <?php endif; ?>
                                <hr class="my-1 border-gray-100 dark:border-gray-700">
                                <a href="<?php echo SITE_URL; ?>logout.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>login.php" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary transition">Login</a>
                        <a href="<?php echo SITE_URL; ?>register.php" class="bg-primary hover:bg-blue-600 text-white px-5 py-2 rounded-full text-sm font-medium transition shadow-lg shadow-blue-500/30">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php 
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    if ($flash): 
        unset($_SESSION['flash']);
    ?>
    <div id="flash-message" class="fixed top-20 right-4 z-[100] transform transition-all duration-500 translate-x-full">
        <div class="<?php echo $flash['type'] === 'success' ? 'bg-green-500' : ($flash['type'] === 'error' ? 'bg-red-500' : 'bg-blue-500'); ?> text-white px-6 py-3 rounded-lg shadow-2xl flex items-center space-x-3">
            <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"></i>
            <span><?php echo $flash['message']; ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="hover:opacity-75"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const msg = document.getElementById('flash-message');
            if(msg) {
                msg.classList.remove('translate-x-full');
                setTimeout(() => {
                    msg.classList.add('translate-x-full');
                    setTimeout(() => msg.remove(), 500);
                }, 4000);
            }
        }, 100);
    </script>
    <?php endif; ?>
