<?php
include '../includes/db.php';
header('Content-Type: application/json');

$response = ['error' => ''];

// 1. Authorization Check
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Authentication required.';
    echo json_encode($response);
    exit();
}

$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$order_id) {
    $response['error'] = 'Invalid Order ID.';
    echo json_encode($response);
    exit();
}

try {
    // 2. Fetch Order Header (Security: Check if the order belongs to the current user)
    $sql_order = "SELECT total_amount, status, created_at FROM orders WHERE id = ? AND user_id = ?";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$order_id, $user_id]);
    $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $response['error'] = 'Order not found or access denied.';
        echo json_encode($response);
        exit();
    }

    // 3. Fetch Order Items
    $sql_items = "
        SELECT 
            oi.quantity, 
            oi.price_at_time_of_order, 
            p.name as product_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([$order_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    // 4. Format Output
    $formatted_items = [];
    foreach ($items as $item) {
        $formatted_items[] = [
            'quantity' => (int)$item['quantity'],
            'product_name' => $item['product_name'],
            'subtotal' => (float)$item['quantity'] * (float)$item['price_at_time_of_order']
        ];
    }

    $response = [
        'total_amount' => (float)$order['total_amount'],
        'status' => $order['status'],
        'created_at' => $order['created_at'],
        'items' => $formatted_items
    ];

} catch (PDOException $e) {
    $response['error'] = 'Database query failed.';
}

echo json_encode($response);
exit();
?>