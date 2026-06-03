<?php 
include '../includes/db.php'; 
include '../includes/header.php'; 
?>

<link rel="stylesheet" href="../assets/css/menu.css">
<div class="auth-container">
    <h2>Create Account</h2>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="../actions/register_action.php" method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="Juan Dela Cruz" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" placeholder="your address" required>
        </div>
        <button type="submit" class="order-btn btn-block">Sign Up</button>
    </form>
    
    <div style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem; color: var(--text-light);">
        Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Sign In</a>
    </div>
</div>

<?php 
    include '../includes/footer.php'; 
?>
</body>
</html>