<?php
require_once 'db.php';
require_once 'auth.php';

header('Content-Type: application/json');

try {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)($_POST['quantity'] ?? 1);

        // Check if product exists in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $item = $stmt->fetch();

        if ($item) {
            $new_qty = $item['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_qty, $item['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }

        // Get total cart count
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total = $stmt->fetch()['total'] ?? 0;

        echo json_encode(['success' => true, 'message' => 'Product added to cart!', 'cart_count' => (int)$total]);

    } elseif ($action === 'update') {
        $cart_id = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$quantity, $cart_id, $user_id]);
        }
        echo json_encode(['success' => true]);

    } elseif ($action === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        echo json_encode(['success' => true]);

    } elseif ($action === 'get_count') {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total = $stmt->fetch()['total'] ?? 0;
        echo json_encode(['count' => (int)$total]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
