<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Handle image upload if any
    $image = uploadImage($_FILES['image'], "../assets/images/");
    
    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, description, image) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $category_id, $price, $description, $image])) {
        setFlashMessage('success', 'Product added successfully!');
    } else {
        setFlashMessage('error', 'Failed to add product.');
    }
    
    redirect('products.php');
}
?>
