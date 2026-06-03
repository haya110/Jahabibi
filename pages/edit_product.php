<?php
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
    header("Location: admin_dashboard.php?tab=menu&error=Invalid+product+ID");
    exit();
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: admin_dashboard.php?tab=menu&error=Database+error");
    exit();
}

if (!$product) {
    header("Location: admin_dashboard.php?tab=menu&error=Product+not+found");
    exit();
}

include '../includes/header.php';
?>

<link rel="stylesheet" href="../assets/css/admin.css">

<div class="container" style="max-width:640px; padding:4rem 0;">
    <h2 style="text-align:center; margin-bottom:2rem; font-weight:700; color:var(--text-dark);">Edit Product</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php elseif (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <form action="../actions/update_product_action.php" method="POST" enctype="multipart/form-data"
          style="background:#fff; padding:2rem; border-radius:var(--radius-lg); box-shadow:var(--shadow-md); border:1px solid var(--border-color);">

        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" maxlength="100"
                   value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required><?php
                echo htmlspecialchars($product['description'] ?? '');
            ?></textarea>
        </div>

        <div class="form-group">
            <label>Price (₱)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0"
                   value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <?php
                $categories = ['musubi' => 'Musubi', 'onigiri' => 'Onigiri', 'drinks' => 'Drinks', 'other' => 'Other'];
                foreach ($categories as $value => $label):
                    $selected = ($product['category'] === $value) ? 'selected' : '';
                ?>
                    <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display:flex;align-items:center;gap:10px;">
            <label style="margin:0;">Active (visible to customers)</label>
            <input type="checkbox" name="is_active" value="1"
                   <?php echo $product['is_active'] ? 'checked' : ''; ?>
                   style="width:18px;height:18px;cursor:pointer;accent-color:var(--primary-color);">
        </div>

        <div class="form-group">
            <label>Current Image</label><br>
            <?php if (!empty($product['image_url'])): ?>
                <img src="../<?php echo htmlspecialchars($product['image_url']); ?>"
                     alt="Current product image"
                     style="width:120px;height:120px;object-fit:cover;border-radius:var(--radius);margin-bottom:10px;border:2px solid var(--border-color);">
            <?php else: ?>
                <p style="color:var(--text-muted);margin-bottom:10px;">No image set.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Change Image <small style="color:var(--text-muted);">(Optional — leave empty to keep current)</small></label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div style="display:flex;gap:10px;margin-top:1.5rem;flex-wrap:wrap;">
            <button type="submit" class="submit-btn" style="flex:1;">Update Product</button>
            <a href="admin_dashboard.php?tab=menu" class="order-btn"
               style="flex:1;text-align:center;background:var(--text-light);text-decoration:none;line-height:2.5;box-shadow:none;">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>