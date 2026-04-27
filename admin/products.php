<?php
include 'includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        setFlashMessage('success', 'Product deleted successfully!');
    }
    redirect('products.php');
}

// Fetch Products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();

// Fetch Categories for Modal
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-black text-gray-800">Manage Products</h2>
    <button onclick="toggleModal('productModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition flex items-center space-x-2">
        <i class="fas fa-plus text-xs"></i>
        <span>Add New Product</span>
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-8 py-4">Product</th>
                    <th class="px-8 py-4">Category</th>
                    <th class="px-8 py-4">Price</th>
                    <th class="px-8 py-4">Added Date</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($products as $p): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-8 py-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden">
                                <img src="<?php echo getProductImage($p['image'], $p['name'], true); ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-bold text-gray-900"><?php echo $p['name']; ?></p>
                                <p class="text-xs text-gray-500 truncate max-w-xs"><?php echo substr($p['description'], 0, 50); ?>...</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase"><?php echo $p['category_name']; ?></span>
                    </td>
                    <td class="px-8 py-4 font-bold text-gray-900">$<?php echo number_format($p['price'], 2); ?></td>
                    <td class="px-8 py-4 text-sm text-gray-500"><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                    <td class="px-8 py-4 text-right space-x-2">
                        <button class="text-blue-500 hover:bg-blue-50 p-2 rounded-lg transition"><i class="fas fa-edit"></i></button>
                        <a href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-xl font-black text-gray-800">Product Details</h3>
            <button onclick="toggleModal('productModal')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="product_actions.php" method="POST" enctype="multipart/form-data" class="p-8 grid grid-cols-2 gap-6">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Product Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Price ($)</label>
                <input type="number" name="price" step="0.01" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Product Image</label>
                <input type="file" name="image" class="w-full px-4 py-3 border border-dashed border-gray-300 rounded-xl bg-gray-50 text-sm">
            </div>
            <div class="col-span-2 pt-4 flex space-x-4">
                <button type="button" onclick="toggleModal('productModal')" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}
</script>

<?php include 'includes/footer.php'; ?>
