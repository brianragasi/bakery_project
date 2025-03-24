<?php
include '../../classes/AdminOrder.php';

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: ../login.php");
    exit();
}

$adminOrder = new AdminOrder();

if (!isset($_GET['order_id'])) {
    echo "Order ID is missing.";
    exit;
}

$orderId = $_GET['order_id'];
$orderDetails = $adminOrder->getOrderDetailsWithItems($orderId);

if (empty($orderDetails)) {
    echo "Order not found.";
    exit;
}

// Since getOrderDetailsWithItems returns an array of items, but order details are the same for all, take the first item to display order level details
$order = $orderDetails[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details - BakeEase Bakery Admin</title>
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
<body class="bg-gray-100 text-gray-800 font-sans">
    <header class="bg-primary text-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Order Details</h1>
            <a href="manage_orders.php" class="text-white hover:text-accent">Back to Manage Orders</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="order-details bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Order ID: <?= $order['order_id'] ?></h2>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Customer Information</h3>
                <p><strong>Name:</strong> <?= $order['customer_name'] ?></p>
                <p><strong>Email:</strong> <?= $order['customer_email'] ?></p>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Order Information</h3>
                <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
                <p><strong>Status:</strong> <?= $order['status'] ?></p>
                <p><strong>Payment Method:</strong> <?= $order['payment_method'] ?></p>
                <p><strong>Order Type:</strong> <?= ucfirst($order['order_type']) ?></p>
                <?php if ($order['order_type'] == 'delivery'): ?>
                    <p><strong>Delivery Address:</strong> <?= $order['delivery_address'] ?></p>
                <?php elseif ($order['order_type'] == 'pickup'): ?>
                    <p><strong>Pickup Time:</strong> <?= $order['pickup_time'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-2">Ordered Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Per Item</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orderDetails as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $item['product_name'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $item['item_quantity'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱<?= number_format($item['product_price'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱<?= number_format($item['product_price'] * $item['item_quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="font-semibold">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right">Total Order Price:</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱<?= number_format($order['final_total'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>