<?php
require_once('Database.php');
require_once "Product.php";

class Cart extends Database {
    public function getCartItems() {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $cartItems = [];
            $product = new Product();

            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $productData = $product->getProduct($productId);
                if ($productData) {
                    $cartItems[] = [
                        'product_id' => $productId,
                        'name' => $productData['name'],
                        'price' => $productData['price'],
                        'quantity' => $quantity,
                        'subtotal' => $productData['price'] * $quantity
                    ];
                }
            }
            return $cartItems;
        } else {
            return []; // Return an empty array if the cart is empty
        }
    }

    public function getCartTotal() {
        $total = 0;
        $cartItems = $this->getCartItems();

        foreach ($cartItems as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public function updateCartItemQuantity($productId, $newQuantity) {
        if (isset($_SESSION['cart'][$productId])) {
            if ($newQuantity > 0) {
                $_SESSION['cart'][$productId] = $newQuantity;
                return true; // Quantity updated successfully
            } else {
                unset($_SESSION['cart'][$productId]); // Remove if quantity is 0 or less
                return true; // Item removed successfully
            }
        }
        return false; // Product not found in cart
    }

    public function removeCartItem($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true; // Item removed
        }
        return false; // Item not found in cart
    }


    public function addToCart($productId, $quantity) {
        // Sanitize input
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        // Get product details from the database
        $product = new Product();
        $productData = $product->getProduct($productId);

        if (!$productData) {
            return ["error" => "Product not found."];
        }

        // Check if there is enough stock
        if ($quantity > $productData['quantity']) {
            return ["error" => "Not enough stock available. Only {$productData['quantity']} left."];
        } 
        
        if ($quantity <= 0 ) {
          return ["error" => "Invalid quantity."];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        return true; // Item added
    }

    public function clearCart() {
        unset($_SESSION['cart']);
    }
}
?>