<?php
include '../includes/db.php'; // Starts session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING); // Used for 'remove'

    if ($product_id === false || !isset($_SESSION['cart'][$product_id])) {
        header("Location: ../pages/cart.php?error=Invalid item.");
        exit();
    }

    if ($action === 'remove') {
        // --- Remove Item ---
        unset($_SESSION['cart'][$product_id]);
        $message = "Item removed from cart.";

    } else {
        // --- Update Quantity ---
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($quantity === false || $quantity < 1) {
            header("Location: ../pages/cart.php?error=Invalid quantity.");
            exit();
        }

        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        $message = "Quantity updated.";
    }

    header("Location: ../pages/cart.php?success=" . urlencode($message));
    exit();
}
// If somehow accessed directly without POST
header("Location: ../pages/cart.php");
exit();
?>