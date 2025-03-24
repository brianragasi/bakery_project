<?php 
require_once '../../classes/Cart.php';
include_once '../../classes/Order.php';
include_once '../../classes/User.php'; // Include the User class

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user = new User();
$cart = new Cart();
$cartItems = $cart->getCartItems();
$finalTotal = $cart->getCartTotal(); // Final total is now the cart total 

// Handle order placement 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'];
    $orderType = $_POST['order_type'];
    $address = $_POST['address']; // Get delivery address
    $pickupTime = ($orderType === 'pickup') ? $_POST['pickup_time'] : null;

    // Address Validation for Delivery Orders
    if ($orderType === 'delivery' && empty(trim($address))) {
        echo "<p class='text-red-500'>Please enter your delivery address.</p>"; 
    } else {
        // Proceed with order creation 
        $order = new Order();
        $orderId = $order->createOrder($userId, $_SESSION['user_email'], $cartItems, $finalTotal, $paymentMethod, $address, $orderType, $pickupTime); 

        if (strpos($orderId, 'Error:') === 0) {
            echo "<p class='text-red-500'>$orderId</p>";
        } elseif ($orderId) {
            // Redirect to order confirmation 
            header("Location: order_confirmation.php?order_id=$orderId&final_total=" . urlencode($finalTotal)); 
            exit;
        } else {
            echo "<p class='text-red-500'>Error: There was a problem placing your order. Please try again.</p>";
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Checkout</title>
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
</head>
<body class="bg-green-50 text-gray-800 font-sans">

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

    <!-- Add min-h-screen here -->
    <main class="container mx-auto px-4 py-8 min-h-screen">
        <section class="checkout bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-8">Checkout</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="order-summary">
                    <h3 class="text-xl font-semibold mb-4">Order Summary</h3>
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-left">
                                <th class="py-2 px-4">Product</th>
                                <th class="py-2 px-4">Price</th>
                                <th class="py-2 px-4">Quantity</th>
                                <th class="py-2 px-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr class="border-b border-gray-200">
                                    <td class="py-2 px-4"><?= $item['name']; ?></td>
                                    <td class="py-2 px-4">P<?= $item['price']; ?></td> 
                                    <td class="py-2 px-4"><?= $item['quantity']; ?></td>
                                    <td class="py-2 px-4">P<?= number_format($item['subtotal'], 2); ?></td>  
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="text-lg font-bold mt-4">Total: P<span id="total-price"><?= number_format($finalTotal, 2); ?></span></p> 

                </div> 

                <div class="shipping-details">
                    <h3 class="text-xl font-semibold mb-4">Shipping Details</h3> 
                    <form method="post" action="" class="space-y-4" name="place_order">
                        <div>
                            <label for="order_type" class="block text-gray-700 font-bold">Order Type:</label>
                            <div class="mt-2">
                                <label for="delivery" class="inline-flex items-center"> 
                                    <input type="radio" id="delivery" name="order_type" value="delivery" required
                                        class="form-radio text-primary"
                                    >
                                    <span class="ml-2">Delivery</span>
                                </label> 
                                <label for="pickup" class="inline-flex items-center ml-6">
                                    <input type="radio" id="pickup" name="order_type" value="pickup" required
                                        class="form-radio text-primary"
                                    >
                                    <span class="ml-2">Pickup</span>
                                </label>
                            </div>
                        </div>

                        <div id="delivery_address_fields" style="display: none;" class="mt-4">
                            <label for="address" class="block text-gray-700 font-bold">Delivery Address*:</label> 
                            <textarea id="address" name="address" 
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                            ></textarea> 
                        </div>

                        <div id="pickup_time_fields" style="display: none;" class="mt-4">
                            <label for="pickup_time" class="block text-gray-700 font-bold">Pickup Time:</label>
                            <input type="datetime-local" id="pickup_time" name="pickup_time"
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                            >
                        </div>

                        <div class="mt-4">
                            <label for="payment_method" class="block text-gray-700 font-bold">Payment Method:</label>
                            <select id="payment_method" name="payment_method" required
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                            >
                                <option value="cod">Cash on Delivery</option> 
                                <option value="credit_card">Credit Card</option>
                            </select> 
                        </div>

                        <button type="submit" name="place_order" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors mt-6">Place Order</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <script>
      const checkoutForm = document.querySelector('form[method="post"][name="place_order"]'); 
      const deliveryRadio = document.getElementById('delivery');
      const pickupRadio = document.getElementById('pickup');
      const deliveryAddressFields = document.getElementById('delivery_address_fields');
      const pickupTimeFields = document.getElementById('pickup_time_fields');
      const addressField = document.getElementById('address'); 

      deliveryRadio.addEventListener('change', () => {
        if (deliveryRadio.checked) {
          deliveryAddressFields.style.display = 'block';
          pickupTimeFields.style.display = 'none';
        }
      });

      pickupRadio.addEventListener('change', () => {
        if (pickupRadio.checked) {
          deliveryAddressFields.style.display = 'none';
          pickupTimeFields.style.display = 'block';
        }
      });

      checkoutForm.addEventListener('submit', function(event) {
        if (deliveryRadio.checked && addressField.value.trim() === "") {
          event.preventDefault(); 
          alert("Please enter your delivery address."); 
        }
      });
    </script>
</body>
</html>
