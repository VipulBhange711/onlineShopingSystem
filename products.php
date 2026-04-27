<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Pagination setup
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter and Search logic
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.price BETWEEN ? AND ?";
$params = [$min_price, $max_price];

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Count total for pagination
$count_query = str_replace("p.*, c.name as category_name", "COUNT(*) as total", $query);
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_products = $stmt->fetch()['total'];
$total_pages = ceil($total_products / $limit);

// Get products
$query .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for sidebar
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-1/4 space-y-8">
            <div class="glass p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-filter mr-2 text-primary"></i> Filters
                </h3>
                
                <form action="products.php" method="GET" class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="What are you looking for?" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-primary focus:border-primary">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="<?php echo $min_price; ?>" placeholder="Min" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                            <input type="number" name="max_price" value="<?php echo $max_price; ?>" placeholder="Max" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-blue-500/30">
                        Apply Filters
                    </button>
                    
                    <a href="products.php" class="block text-center text-sm text-gray-500 hover:text-primary transition">Clear All</a>
                </form>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="w-full lg:w-3/4">
            <div class="flex justify-between items-center mb-8">
                <p class="text-gray-600 dark:text-gray-400">Showing <span class="font-bold text-gray-900 dark:text-white"><?php echo count($products); ?></span> of <span class="font-bold text-gray-900 dark:text-white"><?php echo $total_products; ?></span> products</p>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Sort by:</span>
                    <select class="bg-transparent border-none text-sm font-bold text-gray-900 dark:text-white focus:ring-0">
                        <option>Newest First</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-20 glass rounded-3xl">
                    <div class="bg-blue-50 dark:bg-gray-800 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-3xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">No products found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters or search terms.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach($products as $product): ?>
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="relative w-full h-56 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <img src="<?php echo getProductImage($product['image'], $product['name']); ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-center object-cover group-hover:scale-110 transition duration-500">
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="bg-white text-gray-900 px-6 py-2 rounded-full font-bold shadow-xl hover:bg-primary hover:text-white transition transform translate-y-4 group-hover:translate-y-0 duration-300">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                        <div class="p-5">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo $product['category_name']; ?></p>
                            <h3 class="mt-1 text-lg font-bold text-gray-900 dark:text-white truncate">
                                <a href="product-details.php?id=<?php echo $product['id']; ?>"><?php echo $product['name']; ?></a>
                            </h3>
                            <div class="mt-4 flex justify-between items-center">
                                <p class="text-xl font-black text-gray-900 dark:text-white">$<?php echo number_format($product['price'], 2); ?></p>
                                <div class="flex items-center text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i>
                                    <span class="ml-1 text-gray-500 dark:text-gray-400 font-bold">4.8</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="products.php?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&search=<?php echo urlencode($search); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium <?php echo $page == $i ? 'text-primary z-10 bg-blue-50 border-primary' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700'; ?> transition">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
