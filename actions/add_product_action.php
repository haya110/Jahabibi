<?php
include '../includes/db.php';

// Access control
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php?error=Access Denied");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = $_POST['category']; // <--- NEW FIELD
    $price = floatval($_POST['price']);
    $image_url = '';

    // --- Image Upload Logic (Unchanged) ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/products/";
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        $uploadOk = 1;
        $imageFileType = strtolower($file_extension);
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = 'assets/img/products/' . $file_name;
            } else {
                header("Location: ../pages/admin_dashboard.php?tab=menu&error=Upload failed.");
                exit();
            }
        } else {
            header("Location: ../pages/admin_dashboard.php?tab=menu&error=File is not an image.");
            exit();
        }
    }

    // Insert Product into Database with Category
    try {
        // Update query to include category
        $sql = "INSERT INTO products (name, description, category, price, image_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $category, $price, $image_url]);

        header("Location: ../pages/admin_dashboard.php?tab=menu&success=Product added successfully!");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/admin_dashboard.php?tab=menu&error=Database Error: " . $e->getMessage());
        exit();
    }
}
?>