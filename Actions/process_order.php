<?php
require_once "../classes/Cart.php";
require_once "../classes/Order.php";
require_once "../classes/User.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/user/login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    try {
        // 1. Retrieve form data
        $orderType = $_POST['order_type'];
        $paymentMethod = $_POST['payment_method'];
        $address = isset($_POST['address']) ? $_POST['address'] : ""; // Set a default empty string for address
        $pickupTime = isset($_POST['pickup_time']) ? $_POST['pickup_time'] : null;

        // 2. Validate data 
        if ($orderType !== 'delivery' && $orderType !== 'pickup') {
            throw new Exception("Invalid order type.");
        }

        // 3. Create the order using your Order class
        $cart = new Cart();
        $cartItems = $cart->getCartItems();

        $order = new Order();
        $orderId = $order->createOrder(
            $_SESSION['user_id'],
            $_SESSION['user_email'], // Pass the customer email 
            $cartItems,
            $cart->getCartTotal(),
            $paymentMethod,
            $address, // Pass the address (can be an empty string now)
            $orderType,
            $pickupTime
        );

        // 4. Handle the order creation result
        if (is_int($orderId)) {
            // 5. Deduct loyalty points if applicable
            if (isset($_POST['redeem_points'])) {
                $user = new User();
                $user->deductLoyaltyPoints($_SESSION['user_id'], (int)$_POST['redeem_points']);
            }

            // 6. Clear the shopping cart
            $cart->clearCart();

            // 7. Redirect to order confirmation page
            header("Location: ../views/user/order_confirmation.php?order_id=$orderId");
            exit;
        } else {
            // Handle order creation errors
            echo "Error creating order: " . $orderId; 
            exit;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: ../views/user/checkout.php");
    exit;
}
?>