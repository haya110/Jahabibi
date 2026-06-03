<?php
include '../includes/db.php'; 
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=You must be logged in to view your account.");
    exit();
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ongoing_orders = [];
$past_orders = [];
$fetch_error = '';

try {
    $stmt = $pdo->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_orders as $order) {
        if ($order['status'] === 'pending') {
            $ongoing_orders[] = $order;
        } else {
            $past_orders[] = $order;
        }
    }
} catch (PDOException $e) {
    $fetch_error = "Could not retrieve order history.";
}

function get_status_class($status) {
    switch ($status) {
        case 'completed': return 'status-completed';
        case 'cancelled': return 'status-cancelled';
        default: return 'status-pending';
    }
}
?>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<section class="dashboard-container">
    
    <div class="dashboard-header">
        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?> 👋</h2>
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="display: inline-block; margin-top: 0.5rem;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-grid">
        
        <div class="left-column">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="material-icons">person</i>
                    </div>
                    <div class="profile-info">
                        <h3>My Profile</h3>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Customer</span>
                    </div>
                </div>
                
                <div class="profile-details">
                    <div>
                        <div class="profile-label">Full Name</div>
                        <div class="profile-value"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                    </div>
                    <div>
                        <div class="profile-label">Email</div>
                        <div class="profile-value">
                            <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'user@example.com'; ?>
                        </div>
                    </div>
                </div>

                <a href="<?php echo $rootPath; ?>includes/logout.php" class="order-btn">
                    <i class="material-icons">logout</i> LOG OUT
                </a>
            </div>

            <h3>
                <span class="material-icons">history</span>
                History
            </h3>
        </div>

        <div class="orders-section">
            
            <?php if ($fetch_error): ?>
                <div class="alert alert-error"><?php echo $fetch_error; ?></div>
            <?php endif; ?>

            <?php if (!empty($ongoing_orders)): ?>
                <?php foreach($ongoing_orders as $order): ?>
                <div class="ongoing-order-card">
                    <div class="order-card-header">
                        <span class="order-id">Order #<?php echo $order['id']; ?></span>
                        <span class="status-badge status-pending">Ongoing</span>
                    </div>
                    
                    <div class="order-row">
                        <span>Date Placed</span>
                        <span><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="order-row">
                        <span>Items</span>
                        <span><a href="#" onclick="showOrderDetails(<?php echo $order['id']; ?>); return false;" style="color: var(--primary-color); font-weight: 600;">View Items →</a></span>
                    </div>

                    <div class="order-total-row">
                        <span>Total Amount</span>
                        <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>

                    <div class="delivery-banner">
                        🔥 PREPARING YOUR ORDER
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="past-orders-grid">
                <?php if (empty($past_orders) && empty($ongoing_orders)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                        <i class="material-icons" style="font-size: 3rem; color: var(--text-muted);">receipt_long</i>
                        <p style="margin-top: 1rem; color: var(--text-light);">No orders yet. <a href="menu.php" style="color: var(--primary-color); font-weight: 600;">Start ordering!</a></p>
                    </div>
                <?php endif; ?>

                <?php foreach($past_orders as $order): ?>
                <div class="past-order-card">
                    <div class="order-card-header">
                        <span class="order-id">#<?php echo $order['id']; ?></span>
                        <span class="status-badge <?php echo get_status_class($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>

                    <div class="order-row">
                        <span>Date</span>
                        <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    
                    <div class="order-total-row" style="margin-top: 0.5rem; padding-top: 0.5rem; font-size: 1rem;">
                        <span>Total</span>
                        <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>

                    <div class="order-actions">
                        <button class="action-btn btn-view" onclick="showOrderDetails(<?php echo $order['id']; ?>)">
                            DETAILS
                        </button>
                        <a href="menu.php" class="action-btn btn-reorder" style="text-decoration: none;">
                            RE-ORDER
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>

<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="document.getElementById('orderDetailsModal').style.display='none'">&times;</span>
        <h3>Order Details: <span id="modalOrderId"></span></h3>
        <p><strong>Status:</strong> <span id="modalOrderStatus"></span></p>
        <p><strong>Date:</strong> <span id="modalOrderDate"></span></p>
        <hr>
        <h4>Items:</h4>
        <ul id="modalOrderItems" class="order-detail-list">
        </ul>
        <hr>
        <p class="summary-total">Total: <span id="modalOrderTotal"></span></p>
    </div>
</div>

<script src="../assets/js/dashboard.js"></script>
</body>
</html>