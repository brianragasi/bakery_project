<?php
require_once '../../classes/User.php';
include '../../classes/Order.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User();
$order = new Order();
$userId = $_SESSION['user_id'];
$userDetails = $user->getUserDetails($userId);
$orders = $order->getOrdersForUserWithProducts($userId);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if ($user->updateProfile($userId, $name, $email, $password)) {
        echo "Profile updated successfully!";
        $userDetails = $user->getUserDetails($userId);
    } else {
        echo "Error: There was a problem updating your profile. Please try again.";
    }
}

// Handle success and error messages
if (isset($_GET['success'])) {
    $successMessage = htmlspecialchars($_GET['success']);
} elseif (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Profile</title>
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
        // Toggle function for collapsing/expanding product details
        function toggleProducts(id) {
            var el = document.getElementById(id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
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
        <section class="profile bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">User Profile</h2>

            <?php if (isset($successMessage)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?= $successMessage; ?></p>
                </div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= $errorMessage; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($userDetails): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Profile Details</h3>
                    <p><strong>Name:</strong> <?= $userDetails['name'] ?></p>
                    <p><strong>Email:</strong> <?= $userDetails['email'] ?></p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Update Your Profile</h3>
                    <form method="post" action="" class="space-y-4">
                        <div>
                            <label for="name" class="block text-lg font-semibold">Name:</label>
                            <input type="text" id="name" name="name" value="<?= $userDetails['name'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="email" class="block text-lg font-semibold">Email:</label>
                            <input type="email" id="email" name="email" value="<?= $userDetails['email'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="password" class="block text-lg font-semibold">New Password (optional):</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <button type="submit" name="update_profile" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Update Profile</button>
                    </form>
                </div>

                <div class="profile-section mb-8">
                    <h3 class="text-xl font-semibold mb-4">Order History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                            <thead class="bg-gray-50 text-gray-700 font-bold">
                                <tr>
                                    <th class="py-3 px-6 text-left">Order ID</th>
                                    <th class="py-3 px-6 text-left">Products</th>
                                    <th class="py-3 px-6 text-left">Total Quantity</th>
                                    <th class="py-3 px-6 text-left">Total Price</th>
                                    <th class="py-3 px-6 text-left">Payment Method</th>
                                    <th class="py-3 px-6 text-left">Address</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-left">Order Date</th>
                                    <th class="py-3 px-6 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr id="order-row-<?= $order['id'] ?>" class="hover:bg-gray-100 border-b">
                                            <td class="py-3 px-6"><?= $order['id'] ?></td>
                                            <td class="py-3 px-6">
                                                <!-- Toggle link for viewing products -->
                                                <a href="javascript:void(0);" onclick="toggleProducts('products-<?= $order['id'] ?>');" class="text-blue-500 hover:text-blue-700 underline text-sm">
                                                    View Products
                                                </a>
                                                <!-- Collapsible product subtable -->
                                                <div id="products-<?= $order['id'] ?>" class="hidden mt-2">
                                                    <table class="w-full border border-gray-300 rounded-lg text-sm">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-2 py-1 text-left">Product</th>
                                                                <th class="px-2 py-1 text-left">Qty</th>
                                                                <th class="px-2 py-1 text-left">Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (isset($order['products']) && is_array($order['products'])): ?>
                                                                <?php foreach ($order['products'] as $product): ?>
                                                                    <tr class="border-t">
                                                                        <td class="px-2 py-1"><?= $product['name'] ?></td>
                                                                        <td class="px-2 py-1"><?= $product['quantity'] ?></td>
                                                                        <td class="px-2 py-1">P<?= number_format($product['price'], 2) ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="3" class="px-2 py-1 text-center text-gray-500">No products</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6"><?= $order['total_quantity'] ?></td>
                                            <td class="py-3 px-6">P<?= number_format($order['final_total'], 2) ?></td>
                                            <td class="py-3 px-6"><?= $order['payment_method'] ?></td>
                                            <td class="py-3 px-6"><?= $order['address'] ?></td>
                                            <td class="py-3 px-6">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?=
                                                    $order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($order['status'] == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                                                ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-6"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                            <td class="py-3 px-6">
                                                <?php if ($order['status'] == 'pending'): ?>
                                                    <a href="../../actions/cancel_order.php?order_id=<?= $order['id'] ?>" class="text-red-500 hover:text-red-700 font-medium">Cancel</a>
                                                <?php elseif ($order['status'] == 'cancelled' || $order['status'] == 'delivered'): ?>
                                                    <a href="../../actions/admin-order-actions.php?delete_user_order=<?= $order['id'] ?>"
                                                       class="text-red-500 hover:text-red-700 font-medium"
                                                       onclick="return confirm('Are you sure you want to delete this order from your history?')">Delete</a>
                                                <?php endif; ?>

                                                <?php if ($order['status'] == 'delivered'): ?>
                                                    <!-- Replace the path below with the correct location of your PDF script -->
                                                    <a href="../../actions/download_receipt.php?order_id=<?= $order['id'] ?>"
                                                       class="text-blue-500 hover:text-blue-700 font-medium ml-2">Download Receipt</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="py-3 px-6 text-center text-gray-600">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <p class="text-center text-red-600">User details not found.</p>
            <?php endif; ?>

        </section>
    </main>
</body>
</html>
