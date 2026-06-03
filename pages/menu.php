<?php
include '../includes/db.php';
include '../includes/header.php';

$products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fetch_error = "We are currently unable to load the menu. Please check back soon.";
}
?>

<link rel="stylesheet" href="../assets/css/menu.css">

<section class="menu-section">
    <h2>Menu List</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 20px; text-align: center;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($fetch_error)): ?>
        <div class="alert alert-error" style="max-width: 800px; margin: 0 auto 20px; text-align: center;">
            <?php echo $fetch_error; ?>
        </div>
    <?php elseif (empty($products)): ?>
        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: var(--radius-lg); max-width: 700px; margin: 0 auto;">
            <i class="material-icons" style="font-size: 4rem; color: var(--text-muted);">restaurant</i>
            <h3 style="margin-top: 1rem; color: var(--text-dark);">The kitchen is setting up!</h3>
            <p style="color: var(--text-light);">No menu items are available yet. Please check back soon.</p>
        </div>
    <?php else: ?>
        <div class="menu-grid">
            <?php foreach ($products as $product): ?>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="card-footer">
                            <span class="product-price">₱<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span>
                            <form action="../actions/add_to_cart_action.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="99" aria-label="Quantity">
                                <button type="submit" class="order-btn" title="Add to Cart">
                                    <i class="material-icons">add_shopping_cart</i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
include '../includes/footer.php';
?>