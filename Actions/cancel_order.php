<?php
include_once '../classes/Database.php';
include_once '../classes/Order.php';
// include_once '../classes/User.php'; // You no longer need the User class in this file

session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: ../views/user/profile.php?error=" . urlencode("Invalid request."));
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id'];

$db = new Database();
$order = new Order();

$orderDetails = $order->getOrder($orderId);

// **Corrected Security and Status Check** 
if (!$orderDetails || $orderDetails['user_id'] != $userId || $orderDetails['status'] != 'pending') {
    header("Location: ../views/user/profile.php?error=" . urlencode("You are not authorized to cancel this order or it's not in a cancellable state."));
    exit();
}

try {
    $db->conn->begin_transaction();

    // Update order status to cancelled
    $updateOrderSql = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $stmt = $db->conn->prepare($updateOrderSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    // Get ALL ordered items for this order
    $getItemsSql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = $db->conn->prepare($getItemsSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $itemsResult = $stmt->get_result();

    // Loop through EACH item and update product quantities
    while ($item = $itemsResult->fetch_assoc()) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];

        $updateProductSql = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
        $stmt = $db->conn->prepare($updateProductSql);
        $stmt->bind_param("ii", $quantity, $productId);
        $stmt->execute();
    }

    $db->conn->commit(); // Commit the transaction

    header("Location: ../views/user/profile.php?success=" . urlencode("Order cancelled successfully."));
    exit();

} catch (Exception $e) {
    $db->conn->rollback();
    header("Location: ../views/user/profile.php?error=" . urlencode("Error cancelling order."));
    exit();
}
?>