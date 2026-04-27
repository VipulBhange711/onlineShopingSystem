<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireAdmin(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ShopEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background: #3b82f6; color: white; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col">
        <div class="p-6 border-b border-gray-800">
            <a href="../index.php" class="text-2xl font-bold text-blue-500">ShopEase <span class="text-xs text-gray-400">Admin</span></a>
        </div>
        
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="dashboard.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-chart-pie w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-box w-5"></i>
                <span>Products</span>
            </a>
            <a href="categories.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-tags w-5"></i>
                <span>Categories</span>
            </a>
            <a href="orders.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-shopping-cart w-5"></i>
                <span>Orders</span>
            </a>
            <a href="users.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-users w-5"></i>
                <span>Users</span>
            </a>
            <a href="settings.php" class="sidebar-link flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-800 transition">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <a href="../logout.php" class="flex items-center space-x-3 p-3 rounded-xl text-red-400 hover:bg-gray-800 transition">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8">
            <h2 class="text-xl font-bold text-gray-800" id="page-title">Dashboard</h2>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900"><?php echo $_SESSION['user_name']; ?></p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="w-10 h-10 rounded-full border border-gray-200">
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8">
