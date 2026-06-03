<?php
/**
 * get_order_items_action.php
 * 
 * Returns JSON array of line items for a given order_id.
 * Called via fetch() from the admin dashboard order details modal.
 * 
 * Place this file in: /actions/get_order_items_action.php
 */

include '../includes/db.php';

header('Content-Type: application/json');

// Only admins may query this endpoint
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Validate order_id
if (!isset($_GET['order_id']) || !ctype_digit((string)$_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order_id']);
    exit();
}

$order_id = (int)$_GET['order_id'];

try {
    $stmt = $pdo->prepare("
        SELECT
            p.name,
            oi.quantity,
            oi.price_at_time_of_order,
            (oi.quantity * oi.price_at_time_of_order) AS subtotal
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}