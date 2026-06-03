<?php
include '../includes/db.php';

// Access control: only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php?error=Access Denied: Admin required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $new_status = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);

    if (!$order_id || !in_array($new_status, ['pending', 'completed', 'cancelled'])) {
        header("Location: ../pages/admin_dashboard.php?tab=orders&error=Invalid status or Order ID.");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        header("Location: ../pages/admin_dashboard.php?tab=orders&success=Order #{$order_id} status updated to " . ucfirst($new_status) . ".");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/admin_dashboard.php?tab=orders&error=Database Error: Failed to update order status.");
        exit();
    }
}
?>