<?php
include '../../classes/Order.php'; 
include '../../classes/Product.php'; 

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

// Get the order ID from the URL parameters
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id']; 

    // Fetch order details from the database 
    $order = new Order();
    $sql = "SELECT o.*, 
                   p.name AS product_name, 
                   p.price AS product_price, 
                   oi.quantity AS item_quantity, 
                   o.pickup_time, 
                   o.delivery_address,
                   o.final_total AS final_total
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE o.id = ?
            GROUP BY o.id"; 

    $stmt = $order->conn->prepare($sql); 
    $stmt->bind_param("i", $orderId); 
    $stmt->execute();
    $result = $stmt->get_result(); 

    if ($result->num_rows > 0) {
        $orderDetails = $result->fetch_all(MYSQLI_ASSOC); 
        $finalTotal = $orderDetails[0]['final_total']; // Get the final total from the database
    } else {
        echo "Error: Order details not found.";
        exit();
    }

} else {
    echo "Error: Order ID not found in URL.";
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Order Confirmation</title>
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
    <main class="container mx-auto px-4 py-8">
        <section class="confirmation bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">Thank You For Your Order!</h2>
            <p class="text-center text-gray-700">Your order has been placed successfully.</p>
            <p class="text-center text-gray-700 mt-2">For inquiries, please contact us at <a href="mailto:info@bakeeasebakery.com" class="text-primary hover:underline">info@bakeeasebakery.com</a>.</p>

            <div class="order-details mt-8"> 
                <h3 class="text-xl font-semibold mb-4">Order Details</h3>
                <p class="mb-2"><span class="font-bold">Order ID:</span> <?php echo $orderId; ?></p>
                <p class="mb-2"><span class="font-bold">Order Type:</span> <?php echo $orderDetails[0]['order_type']; ?></p>

                <?php if ($orderDetails[0]['order_type'] == 'delivery'): ?>
                    <p class="mb-2"><span class="font-bold">Delivery Address:</span> <?php echo $orderDetails[0]['delivery_address']; ?></p>
                <?php elseif ($orderDetails[0]['order_type'] == 'pickup'): ?>
                    <p class="mb-2"><span class="font-bold">Pickup Time:</span> <?php echo $orderDetails[0]['pickup_time']; ?></p>
                <?php endif; ?>

                <table class="w-full table-auto mt-4">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-left">
                            <th class="py-2 px-4">Product</th>
                            <th class="py-2 px-4">Quantity</th>
                            <th class="py-2 px-4">Price</th>
                            <th class="py-2 px-4">Subtotal</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $item): ?>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-4"><?php echo $item['product_name']; ?></td>
                                <td class="py-2 px-4"><?php echo $item['item_quantity']; ?></td>
                                <td class="py-2 px-4">P<?php echo $item['product_price']; ?></td>
                                <td class="py-2 px-4">P<?php echo number_format($item['item_quantity'] * $item['product_price'], 2); ?></td> 
                            </tr>
                        <?php endforeach; ?>
                        <tr class="font-bold"> 
                            <td colspan="3" class="py-2 px-4 text-right">Final Total:</td>
                            <td class="py-2 px-4">P<?php echo number_format($finalTotal, 2); ?></td> 
                        </tr> 
                    </tbody>
                </table>
            </div>

            <a href="index.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors mt-8 inline-block">Back to Home</a>
        </section>
    </main>

    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>