<?php
include_once "../classes/Cart.php";

session_start();

$cart = new Cart(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_to_cart'])) { 
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Validate quantity 
        if (!is_numeric($quantity) || $quantity < 1) {
            $quantity = 1; 
        }

        $success = $cart->addToCart($productId, $quantity);

        if ($success) { 
            $response = [
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => count($_SESSION['cart']) // Update cart count 
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error adding product to cart.' 
            ];
        }
        echo json_encode($response); 
        exit; // Terminate script after sending JSON

    } elseif (isset($_POST['update_quantity'])) {
        $productId = $_POST['product_id'];
        $newQuantity = $_POST['quantity'];

        // Validate quantity 
        if (!is_numeric($newQuantity) || $newQuantity < 1) {
            $newQuantity = 1; 
        }

        $success = $cart->updateCartItemQuantity($productId, $newQuantity);

        if ($success) {
            $cartItems = $cart->getCartItems();
            $updatedItem = array_filter($cartItems, function($item) use ($productId) {
                return $item['product_id'] == $productId;
            }); 
            $updatedItem = reset($updatedItem); 

            $response = [
                'success' => true,
                'message' => 'Cart updated!',
                'cartCount' => count($_SESSION['cart']),
                'subtotal' => $updatedItem['subtotal'], 
                'cartTotal' => $cart->getCartTotal()  
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error updating cart.' 
            ];
        }
        echo json_encode($response);
        exit;  // Terminate script after sending JSON

    } elseif (isset($_POST['remove_item'])) {
        $productId = $_POST['product_id'];

        $success = $cart->removeCartItem($productId); 

        if ($success) {
            $response = [
                'success' => true,
                'message' => 'Item removed from cart.',
                'cartCount' => count($_SESSION['cart']), 
                'cartTotal' => $cart->getCartTotal() 
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error removing item from cart.' 
            ];
        }
        echo json_encode($response);
        exit; // Terminate script after sending JSON
    } 
}
?>