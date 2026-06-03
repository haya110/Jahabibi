<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {

    // 1. Retrieve Main Order Details
    $user_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method']; // 'cod', 'gcash', or 'card'
    $notes = $_POST['notes'];

    // 2. Handle Dynamic Payment Details
    // We check which method was chosen and grab the corresponding input field
    $payment_details = '';
    
    if ($payment_method === 'gcash') {
        $payment_details = $_POST['gcash_number'] ?? ''; // Get the 11-digit number
    } elseif ($payment_method === 'card') {
        $payment_details = $_POST['card_number'] ?? '';  // Get the 16-digit number
    }

    try {
        // Start Transaction for safety
        $pdo->beginTransaction();

        // 3. Insert Order Record
        // Make sure your database has the 'payment_details' column!
        $sql_order = "INSERT INTO orders (user_id, total_amount, status, address, phone, payment_method, payment_details, notes, created_at) 
                      VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, NOW())";
        
        $stmt_order = $pdo->prepare($sql_order);
        $stmt_order->execute([
            $user_id, 
            $total_amount, 
            $address, 
            $phone, 
            $payment_method, 
            $payment_details, 
            $notes
        ]);

        $order_id = $pdo->lastInsertId(); // Get the ID of the new order

        // 4. Insert Items belonging to this order
        $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_at_time_of_order) 
                      VALUES (?, ?, ?, ?)";
        $stmt_items = $pdo->prepare($sql_items);

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt_items->execute([
                $order_id,
                $product_id, // Ensure your cart session uses 'id' or key as product_id
                $item['quantity'],
                $item['price']
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        // 5. Clear cart after successful order
        unset($_SESSION['cart']);

        header("Location: ../pages/customer_dashboard.php?success=Order #$order_id placed successfully!");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../pages/checkout.php?error=Order failed: " . $e->getMessage());
        exit();
    }

} else {
    header("Location: ../pages/cart.php");
    exit();
}
?>