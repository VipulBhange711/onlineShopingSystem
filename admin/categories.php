<?php
include 'includes/header.php';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $_POST['id']]);
        setFlashMessage('success', 'Category updated!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        setFlashMessage('success', 'Category added!');
    }
    redirect('categories.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    setFlashMessage('success', 'Category deleted!');
    redirect('categories.php');
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-black text-gray-800">Manage Categories</h2>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 w-full max-w-md">
        <form action="categories.php" method="POST" class="flex space-x-2">
            <input type="text" name="name" required placeholder="New Category Name" class="flex-1 px-4 py-2 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition text-sm">Add</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($categories as $cat): ?>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center hover:shadow-md transition">
        <div>
            <p class="font-bold text-gray-900"><?php echo $cat['name']; ?></p>
            <?php 
            $count = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $count->execute([$cat['id']]);
            $num = $count->fetchColumn();
            ?>
            <p class="text-xs text-gray-500"><?php echo $num; ?> Products</p>
        </div>
        <div class="flex space-x-2">
            <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure? This may affect products in this category.')" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
