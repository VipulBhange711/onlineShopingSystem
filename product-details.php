<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

// Fetch related products
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$stmt->execute([$product['category_id'], $id]);
$related_products = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm text-gray-500 dark:text-gray-400">
        <a href="index.php" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        <a href="products.php?category=<?php echo $product['category_id']; ?>" class="hover:text-primary"><?php echo $product['category_name']; ?></a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 dark:text-white font-medium"><?php echo $product['name']; ?></span>
    </nav>

    <div class="flex flex-col md:flex-row gap-12">
        <!-- Product Image -->
        <div class="w-full md:w-1/2">
            <div class="glass rounded-3xl overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-800 group">
                <img src="<?php echo getProductImage($product['image'], $product['name']); ?>" alt="<?php echo $product['name']; ?>" class="w-full h-[500px] object-cover group-hover:scale-105 transition duration-700">
            </div>
            
            <div class="grid grid-cols-4 gap-4 mt-6">
                <?php for($i=1; $i<=4; $i++): ?>
                <div class="glass rounded-xl overflow-hidden cursor-pointer border-2 border-transparent hover:border-primary transition shadow-md">
                    <img src="https://loremflickr.com/400/300/<?php echo urlencode($product['name'] . ' ' . $i); ?>,product" class="w-full h-24 object-cover">
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="w-full md:w-1/2 space-y-8">
            <div>
                <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-primary text-xs font-bold rounded-full uppercase tracking-widest mb-4">
                    <?php echo $product['category_name']; ?>
                </span>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo $product['name']; ?></h1>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center text-yellow-400">
                        <?php for($i=0; $i<5; $i++): ?>
                            <i class="fas fa-star text-sm"></i>
                        <?php endfor; ?>
                        <span class="ml-2 text-gray-600 dark:text-gray-400 font-medium">(128 Reviews)</span>
                    </div>
                    <span class="text-gray-300">|</span>
                    <span class="text-green-500 font-bold"><i class="fas fa-check-circle mr-1"></i> In Stock</span>
                </div>
                <p class="text-4xl font-black text-gray-900 dark:text-white">$<?php echo number_format($product['price'], 2); ?></p>
            </div>

            <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-lg">
                <?php echo $product['description']; ?>
                Experience the pinnacle of innovation and design. This product is crafted with premium materials to ensure durability and style. Perfect for those who demand excellence in every detail.
            </p>

            <div class="pt-8 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-full p-1 bg-white dark:bg-gray-800">
                        <button onclick="updateQty(-1)" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition"><i class="fas fa-minus text-xs"></i></button>
                        <input type="number" id="qty" value="1" min="1" class="w-12 text-center bg-transparent border-none focus:ring-0 font-bold dark:text-white">
                        <button onclick="updateQty(1)" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition"><i class="fas fa-plus text-xs"></i></button>
                    </div>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('qty').value)" class="flex-1 bg-primary hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-full transition shadow-xl shadow-blue-500/30 flex items-center justify-center space-x-3 transform active:scale-95">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Add to Cart</span>
                    </button>
                    <button class="w-14 h-14 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center hover:bg-red-50 hover:border-red-500 hover:text-red-500 transition text-gray-500">
                        <i class="far fa-heart text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="flex items-center space-x-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-truck text-primary"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Free Delivery</h4>
                        <p class="text-xs text-gray-500">For orders over $100</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-shield-alt text-primary"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">1 Year Warranty</h4>
                        <p class="text-xs text-gray-500">Official manufacturer warranty</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="mt-24">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-8">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($related_products as $rel): ?>
            <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo getProductImage($rel['image'], $rel['name']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate">
                        <a href="product-details.php?id=<?php echo $rel['id']; ?>"><?php echo $rel['name']; ?></a>
                    </h3>
                    <p class="text-primary font-bold mt-1">$<?php echo number_format($rel['price'], 2); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function updateQty(val) {
    const input = document.getElementById('qty');
    let current = parseInt(input.value);
    if (current + val >= 1) {
        input.value = current + val;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
