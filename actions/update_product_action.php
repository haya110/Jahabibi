<?php
include '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    
    // Fetch current image path first
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $current_product = $stmt->fetch();
    $image_url = $current_product['image_url'];

    // --- Image Upload Logic (Only if new image selected) ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/products/";
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Delete old image if it exists
                if (file_exists('../' . $image_url) && !empty($image_url)) {
                    unlink('../' . $image_url);
                }
                $image_url = 'assets/img/products/' . $file_name;
            }
        }
    }

    // Update Database
    try {
        $sql = "UPDATE products SET name=?, description=?, price=?, image_url=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $image_url, $id]);

        header("Location: ../pages/admin_dashboard.php?tab=menu&success=Product updated successfully!");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/edit_product.php?id=$id&error=Update failed: " . $e->getMessage());
        exit();
    }
}
?>