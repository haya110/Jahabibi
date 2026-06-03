<?php
include '../includes/db.php';

// Access control: only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php?error=Access Denied: Admin required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (!$user_id) {
        header("Location: ../pages/admin_dashboard.php?tab=users&error=Invalid User ID.");
        exit();
    }
    
    // Safety Check: Prevent admin from managing their own account
    if ($user_id == $_SESSION['user_id']) {
        header("Location: ../pages/admin_dashboard.php?tab=users&error=You cannot manage your own admin account.");
        exit();
    }

    try {
        if ($action == 'delete') {
            // Check for existing orders (prevent accidental deletion of users with history)
            $stmt = $pdo->prepare("SELECT COUNT(id) FROM orders WHERE user_id = ?");
            $stmt->execute([$user_id]);
            if ($stmt->fetchColumn() > 0) {
                 header("Location: ../pages/admin_dashboard.php?tab=users&error=Cannot delete user: They have existing orders.");
                 exit();
            }

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$user_id]);
            $message = "User ID {$user_id} deleted successfully.";
            
        } elseif ($action == 'promote_to_admin') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ? AND role = 'customer'");
            $stmt->execute([$user_id]);
            $message = "User ID {$user_id} promoted to Admin.";
        }

        header("Location: ../pages/admin_dashboard.php?tab=users&success=" . urlencode($message));
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/admin_dashboard.php?tab=users&error=Database Error: Failed to perform action.");
        exit();
    }
}
?>