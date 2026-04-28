<?php
require_once 'includes/db.php';

$products = [
    1 => [ // Electronics
        'names' => ['Smart Watch Pro', 'Bluetooth Speaker Z', 'Wireless Mouse X', 'Mechanical Keyboard K1', '4K Monitor 27"', 'Noise Cancelling Buds', 'Power Bank 20000mAh', 'USB-C Hub 7-in-1', 'Gaming Headset Pro', 'Smart Home Camera', 'Tablet Air 10', 'Mini Projector HD', 'E-Reader Paper', 'Drone Explorer', 'VR Headset Elite', 'Soundbar 5.1', 'Laptop Stand Pro', 'External SSD 1TB', 'Gaming Controller', 'Smart Bulb Pack'],
        'desc' => 'High-performance electronic device with cutting-edge technology.'
    ],
    2 => [ // Fashion
        'names' => ['Cotton T-Shirt', 'Denim Jacket Classic', 'Slim Fit Chinos', 'Summer Floral Dress', 'Leather Belt Black', 'Wool Sweater Grey', 'Puffer Vest Navy', 'Silk Scarf Red', 'Graphic Hoodie', 'Linen Shirt White', 'Cargo Pants Olive', 'V-Neck Cardigan', 'Polo Shirt Striped', 'Trench Coat Tan', 'Beanie Hat Knit', 'Leather Gloves', 'Denim Shorts', 'Formal Blazer', 'Maxi Skirt Flowy', 'Winter Parka Heavy'],
        'desc' => 'Stylish and comfortable fashion piece for your everyday wardrobe.'
    ],
    3 => [ // Books
        'names' => ['The Great Gatsby', '1984 George Orwell', 'To Kill a Mockingbird', 'The Catcher in the Rye', 'Brave New World', 'The Hobbit', 'Fahrenheit 451', 'Pride and Prejudice', 'The Odyssey', 'Crime and Punishment', 'The Alchemist Pro', 'Sapiens A Brief History', 'Thinking Fast and Slow', 'Atomic Habits', 'The Power of Habit', 'Deep Work', 'Zero to One', 'The Lean Startup', 'Man\'s Search for Meaning', 'Dune'],
        'desc' => 'An essential read for any book lover, offering profound insights and storytelling.'
    ],
    4 => [ // Shoes
        'names' => ['Running Pro X1', 'Casual Sneakers White', 'Leather Boots Brown', 'Formal Oxford Shoes', 'Sport Sandals Blue', 'Trail Runners Elite', 'High Top Sneakers', 'Slip-on Loafers', 'Work Safety Boots', 'Yoga Slippers', 'Basketball Shoes Pro', 'Tennis Shoes Lite', 'Combat Boots Black', 'Chelsea Boots Suede', 'Espadrilles Summer', 'Flip Flops Beach', 'Hiking Boots Pro', 'Cycling Shoes Pro', 'Skateboard Shoes', 'Classic Brogues'],
        'desc' => 'Premium quality footwear designed for maximum comfort and durability.'
    ],
    5 => [ // Accessories
        'names' => ['Polarized Sunglasses', 'Minimalist Wallet', 'Canvas Backpack', 'Stainless Steel Bottle', 'Umbrella Windproof', 'Travel Neck Pillow', 'Yoga Mat Premium', 'Laptop Sleeve 15"', 'Key Organizer Leather', 'Phone Case Rugged', 'Fitness Tracker Band', 'Classic Aviators', 'Leather Card Holder', 'Duffel Bag Sport', 'Watch Box Velvet', 'Pen Set Elegant', 'Notebook Hardcover', 'Tote Bag Eco', 'Wallet Bifold Leather', 'Belt Reversible'],
        'desc' => 'Functional and stylish accessory to complement your lifestyle.'
    ]
];

$count = 0;
$total_to_add = 100;

try {
    $pdo->beginTransaction();

    for ($i = 0; $i < $total_to_add; $i++) {
        $category_id = ($i % 5) + 1; // Cycle through 1-5
        $cat_data = $products[$category_id];
        $name_index = floor($i / 5) % count($cat_data['names']);
        $name = $cat_data['names'][$name_index] . ' (Model ' . ($i + 1) . ')';
        $description = $cat_data['desc'] . ' This is model number ' . ($i + 1) . ' in our collection.';
        $price = rand(10, 1500) + (rand(0, 99) / 100);
        $image = 'product_' . ($i + 1) . '.jpg'; // Placeholder name, the functions.php logic will handle fallback

        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image, $category_id]);
        $count++;
    }

    $pdo->commit();
    echo "Successfully added $count products to the database.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
