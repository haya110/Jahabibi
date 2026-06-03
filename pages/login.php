<?php 
include '../includes/db.php'; 
include '../includes/header.php'; 
?>

<div class="auth-container">
    <h2>Welcome Back</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="../actions/login_action.php" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="order-btn btn-block">Login</button>
    </form>

    <div style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem; color: var(--text-light);">
        Don't have an account? <a href="signup.php" style="color: var(--primary-color); font-weight: 600;">Create one</a>
    </div>
</div>

<?php 
    include '../includes/footer.php'; 
?>
</body>
</html>