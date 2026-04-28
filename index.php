<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Fetch featured products (latest 8)
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8");
$featured_products = $stmt->fetchAll();

// Fetch categories for the category section
$stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
$home_categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-white dark:bg-gray-900 overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white dark:bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white dark:text-gray-900 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Upgrade Your Lifestyle</span>
                        <span class="block text-primary xl:inline">with ShopEase</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Discover the latest trends in technology, fashion, and home essentials. Premium quality products delivered right to your doorstep.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="products.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-primary hover:bg-blue-600 md:py-4 md:text-lg md:px-10 transition shadow-lg shadow-blue-500/30">
                                Shop Now
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="#featured" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-primary bg-blue-100 hover:bg-blue-200 dark:bg-gray-800 dark:text-blue-400 md:py-4 md:text-lg md:px-10 transition">
                                View Featured
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="<?php echo getProductImage('hero.jpg', 'Shopping Hero'); ?>" alt="Online Shopping">
    </div>
</div>

<!-- Category Section -->
<div class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Shop by Category</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Explore our wide range of products across different categories.</p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-4 xl:gap-x-8">
            <?php foreach($home_categories as $cat): ?>
            <div class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative w-full h-64 bg-white dark:bg-gray-700 overflow-hidden group-hover:opacity-75 sm:aspect-w-2 sm:aspect-h-1 sm:h-64 lg:aspect-w-1 lg:aspect-h-1">
                    <img src="<?php echo getProductImage($cat['name'] . '.jpg', $cat['name']); ?>" alt="<?php echo $cat['name']; ?>" class="w-full h-full object-center object-cover">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">
                            <a href="products.php?category=<?php echo $cat['id']; ?>">
                                <span class="absolute inset-0"></span>
                                <?php echo $cat['name']; ?>
                            </a>
                        </h3>
                        <p class="text-sm text-gray-300">Shop Now <i class="fas fa-arrow-right ml-1 text-xs"></i></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div id="featured" class="bg-white dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Featured Products</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Handpicked items just for you.</p>
            </div>
            <a href="products.php" class="text-primary font-semibold hover:underline">View All <i class="fas fa-chevron-right ml-1 text-xs"></i></a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
            <?php foreach($featured_products as $product): ?>
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 aspect-w-1 aspect-h-1 group-hover:opacity-75 transition-opacity">
                    <img src="<?php echo getProductImage($product['image'], $product['name']); ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-center object-cover">
                    <!-- Overlay with Add to Cart Button -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 backdrop-blur-[2px]">
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-white text-gray-900 px-6 py-2 rounded-full font-semibold shadow-xl hover:bg-primary hover:text-white transition transform translate-y-4 group-hover:translate-y-0 duration-300">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-primary uppercase tracking-wider"><?php echo $product['category_name']; ?></p>
                            <h3 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                <a href="product-details.php?id=<?php echo $product['id']; ?>">
                                    <?php echo $product['name']; ?>
                                </a>
                            </h3>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">$<?php echo number_format($product['price'], 2); ?></p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center text-yellow-400 text-sm">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span class="ml-2 text-gray-500 dark:text-gray-400 text-xs">(4.5)</span>
                        </div>
                        <button class="text-gray-400 hover:text-red-500 transition"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-primary py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center text-white">
            <div class="p-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shipping-fast text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Free Shipping</h3>
                <p class="text-blue-100">On all orders over $100</p>
            </div>
            <div class="p-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-undo text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">30-Day Returns</h3>
                <p class="text-blue-100">Hassle-free money back guarantee</p>
            </div>
            <div class="p-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">24/7 Support</h3>
                <p class="text-blue-100">Dedicated support team always ready</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
