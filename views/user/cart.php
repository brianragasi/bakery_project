<?php
require_once '../../classes/Cart.php';
require_once '../../classes/User.php'; // To get user details 

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$cart = new Cart();
$cartItems = $cart->getCartItems();
$cartTotal = $cart->getCartTotal();

$user = new User(); // Instantiate the User class
$userDetails = $user->getUserDetails($_SESSION['user_id']); // Get user details

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4CAF50',
                        secondary: '#8BC34A',
                        accent: '#FFC107',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        main {
            flex-grow: 1;
        }
    </style>
</head>

<body class="bg-green-50 text-gray-800 font-sans">

    <!-- Header Section -->
    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/color/48/000000/birthday-cake.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">BakeEase Bakery</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="index.php" class="hover:text-accent transition-colors">Home</a></li>
                    <li><a href="products.php" class="hover:text-accent transition-colors">Products</a></li>
                    <li><a href="contact.php" class="hover:text-accent transition-colors">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php" class="hover:text-accent transition-colors">Profile</a></li>
                        <li><a href="../../actions/actions.logout.php" class="hover:text-accent transition-colors">Logout</a></li>
                    <?php else: ?>
                        <li><a href="../login.php" class="hover:text-accent transition-colors">Login</a></li>
                        <li><a href="register.php" class="hover:text-accent transition-colors">Register</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="cart.php" class="relative hover:text-accent transition-colors">
                            Cart (<span class="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Cart Section -->
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-center mb-8">Your Cart</h2>

        <div class="cart-content bg-white p-8 rounded-lg shadow-md">
            <?php if (!empty($cartItems)): ?>
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-left">
                            <th class="py-4 px-6">Product</th>
                            <th class="py-4 px-6">Price</th>
                            <th class="py-4 px-6">Quantity</th>
                            <th class="py-4 px-6">Subtotal</th>
                            <th class="py-4 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr class="border-b border-gray-200" id="cart-item-<?= $item['product_id'] ?>">
                                <td class="py-4 px-6"><?= $item['name'] ?></td>
                                <td class="py-4 px-6">₱<?= number_format($item['price'], 2) ?></td>
                                <td class="py-4 px-6">
                                    <input type="number" name="quantity" 
                                           value="<?= $item['quantity'] ?>" min="1" 
                                           onchange="updateCartItem(<?= $item['product_id'] ?>, this.value)"
                                           class="w-16 px-2 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="py-4 px-6 subtotal-<?= $item['product_id'] ?>">₱<?= number_format($item['subtotal'], 2) ?></td>
                                <td class="py-4 px-6">
                                    <button onclick="removeItem(<?= $item['product_id'] ?>)" class="text-red-600 hover:underline">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="flex justify-between items-center mt-8">
                    <p class="text-xl font-bold">Total: ₱<span id="cart-total"><?= number_format($cartTotal, 2) ?></span></p>
                    <a href="checkout.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Proceed to Checkout</a>
                </div>

            <?php else: ?>
                <p class="text-center text-gray-600">Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="mt-8 text-center">
            <a href="products.php" class="text-primary hover:underline">Continue Shopping</a>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">About Us</h3>
                    <p>BakeEase Bakery is your go-to place for delicious, freshly baked goods. We take pride in our quality ingredients and passionate bakers.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-accent">FAQ</a></li>
                        <li><a href="#" class="hover:text-accent">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-accent">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-accent">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.018 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                            </svg>
                        </a>
                        <a href="#" class="hover:text-accent">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M16 8a6 6 0 100-12 6 6 0 000 12zM22.124 16.219l-1.612-6.712C20.207 8.521 19.579 8 18.836 8h-9.672c-.742 0-1.37.522-1.676 1.307l-1.612 6.712C5.588 17.605 5.987 18 6.494 18h11.012c.507 0 .906-.395.618-1.219z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        function updateCartItem(productId, newQuantity) {
            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../actions/cart-actions.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        // Update the subtotal
                        document.querySelector('.subtotal-' + productId).textContent = '₱' + response.subtotal.toFixed(2); 

                        // Update the cart total
                        document.getElementById('cart-total').textContent = response.cartTotal.toFixed(2);

                        // Update cart count in the header
                        document.querySelector('.cart-count').textContent = response.cartCount; 
                    } else {
                        alert(response.message); 
                    }
                } else {
                    alert('Error: ' + this.status); 
                }
            };
            xhr.send('product_id=' + productId + '&quantity=' + newQuantity + '&update_quantity=1'); 
        }

        function removeItem(productId) {
            if (confirm("Are you sure you want to remove this item from your cart?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../../actions/cart-actions.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        if (response.success) {
                            // Remove the item row from the table
                            var rowToRemove = document.getElementById('cart-item-' + productId);
                            if (rowToRemove) {
                                rowToRemove.remove();
                            }

                            // Update the cart total
                            document.getElementById('cart-total').textContent = response.cartTotal.toFixed(2);

                            // Update cart count in the header
                            document.querySelector('.cart-count').textContent = response.cartCount; 
                        } else {
                            alert(response.message); 
                        }
                    } else {
                        alert('Error: ' + this.status);
                    }
                };
                xhr.send('product_id=' + productId + '&remove_item=1');
            }
        }
    </script>

</body>
</html>