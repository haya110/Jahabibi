<?php
session_start();
include '../includes/db.php';

// Access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php?error=Access Denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['id'];

    try {
        // Soft Delete: Just set is_active to 0
        $sql = "UPDATE products SET is_active = 0 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);

        // Success message
        header("Location: ../pages/admin_dashboard.php?tab=menu&success=Product archived successfully!");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/admin_dashboard.php?tab=menu&error=Database Error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: ../pages/admin_dashboard.php?tab=menu");
    exit();
}
?>