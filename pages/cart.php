<?php
include '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$subtotal = 0;
$delivery_fee = 50.00;

foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$total_amount = $subtotal + $delivery_fee;
?>

<section class="cart-section">
    <h2 class="section-title" style="margin-bottom: 2.5rem;">Your Shopping Cart</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="max-width: 1200px; margin: 0 auto 20px; text-align: center;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: var(--radius-lg); max-width: 600px; margin: 0 auto; box-shadow: var(--shadow);">
            <i class="material-icons" style="font-size: 5rem; color: var(--text-muted);">shopping_cart</i>
            <h3 style="margin-top: 1rem; color: var(--text-dark);">Your cart is empty</h3>
            <p style="color: var(--text-light); margin-bottom: 1.5rem;">Time to fill it up with some delicious musubi!</p>
            <a href="menu.php" class="order-btn" style="display: inline-flex;">Browse Menu</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <h3>Items for Order</h3>
                <?php foreach ($cart as $product_id => $item): ?>
                    <div class="cart-item">
                        <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="item-image"
                             loading="lazy">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p style="color: var(--text-light); font-size: 0.85rem;">₱<?php echo htmlspecialchars(number_format($item['price'], 2)); ?> each</p>
                        </div>
                        <div class="item-controls">
                            <form action="../actions/update_cart_action.php" method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="99" class="quantity-input" onchange="this.form.submit()">
                            </form>
                            <form action="../actions/update_cart_action.php" method="POST" 
                                  onsubmit="return confirm('Remove <?php echo htmlspecialchars(addslashes($item['name'])); ?> from cart?');">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="remove-btn" title="Remove item">
                                    <i class="material-icons">delete_outline</i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-details">
                    <?php foreach ($cart as $item): ?>
                        <div>
                            <span><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>x)</span>
                            <span>₱<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div>
                        <span>Subtotal</span>
                        <span>₱<?php echo htmlspecialchars(number_format($subtotal, 2)); ?></span>
                    </div>
                    <div>
                        <span>Delivery Fee</span>
                        <span>₱<?php echo htmlspecialchars(number_format($delivery_fee, 2)); ?></span>
                    </div>
                </div>

                <div class="summary-total">
                    <span>Total Amount</span>
                    <span>₱<?php echo htmlspecialchars(number_format($total_amount, 2)); ?></span>
                </div>

                <div class="checkout-actions-container">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <p style="color: var(--warning); margin-top: 15px; text-align: center; grid-column: 1 / -1; line-height: 1.5; font-size: 0.9rem;">
                            <i class="material-icons" style="font-size: 16px; vertical-align: middle;">info</i> 
                            Please <a href="login.php" style="color: var(--primary-color); font-weight: 600;">log in</a> to proceed.
                        </p>
                        <button type="button" class="action-btn-disabled" disabled style="grid-column: 1 / -1;">LOG IN TO CHECKOUT</button>
                    <?php else: ?>
                        <a href="checkout.php" class="action-btn btn-checkout">PROCEED TO CHECKOUT</a>
                        <a href="menu.php" class="action-btn btn-back-menu">BACK TO MENU</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

</body>
</html>