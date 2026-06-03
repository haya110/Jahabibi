<?php
include '../includes/db.php'; // This starts the session if it hasn't started

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($product_id === false || $price === false || $quantity === false || $quantity < 1) {
        header("Location: ../pages/menu.php?error=Invalid product data.");
        exit();
    }

    // Initialize the cart array if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Fetch the product name and image from the DB to store in the session (optional but helpful)
    try {
        $stmt = $pdo->prepare("SELECT name, image_url FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            header("Location: ../pages/menu.php?error=Product not found.");
            exit();
        }
        
        $item_details = [
            'id' => $product_id,
            'name' => $product['name'],
            'image_url' => $product['image_url'],
            'price' => $price,
            'quantity' => $quantity,
        ];

        // Check if the product is already in the cart
        if (array_key_exists($product_id, $_SESSION['cart'])) {
            // If yes, update the quantity
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // If no, add the new item
            $_SESSION['cart'][$product_id] = $item_details;
        }

        header("Location: ../pages/menu.php?success=" . urlencode($quantity . " x " . $product['name'] . " added to cart successfully!"));
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/menu.php?error=Database error during cart processing.");
        exit();
    }
}
?>