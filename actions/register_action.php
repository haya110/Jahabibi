<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (empty($full_name) || empty($email) || empty($password)) {
        header("Location: ../pages/signup.php?error=All fields are required");
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/signup.php?error=Email already exists");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert User
    try {
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'customer')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$full_name, $email, $hashed_password]);

        // Login the user immediately after signup
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['full_name'] = $full_name;
        $_SESSION['address'] = $address; 
        $_SESSION['role'] = 'customer';

        header("Location: ../pages/menu.php");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/signup.php?error=Registration failed");
        exit();
    }
}
?>