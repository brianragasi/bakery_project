<?php
include '../../classes/Product.php';

session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// *** SESSION CHECK ***
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI'])); 
    exit;
} 
// *** END SESSION CHECK ***

$productObj = new Product();
$products = $productObj->getProducts();

// Base URL for images (Centralized)
$imageBaseUrl = 'http://localhost/bakery_oop/assets/images/'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Products</title>
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
        .success-message { /* Style the success message (optional) */
            color: green; 
            font-weight: bold;
            margin-top: 0.5rem; 
        }
        .hidden { /* Class to hide the message initially */
            display: none;
        }
    </style>
</head>

<body class="bg-green-50 text-gray-800 font-sans h-screen flex flex-col"> 

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

    <!-- Product Gallery Section -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <section class="product-gallery">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-center">Our Delicious Products</h2>
            </div>

            <?php if (isset($_GET['message'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?= htmlspecialchars($_GET['message']) ?></p>
                </div>
            <?php endif; ?> 

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-12">
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        ?>
                        <div class='bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300'>
                            <a href="product_details.php?id=<?= $product['id'] ?>" class="block"  onclick="updateNavigationStack(this.href); return true;">
                                <img src="<?= $imageBaseUrl . $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-full h-64 object-cover">
                                <div class="p-8">
                                    <h3 class="text-2xl font-semibold mb-4"><?= $product['name'] ?></h3>
                                    <p class="text-gray-600 mb-6"><?= $product['description'] ?></p>
                                    <div class="flex justify-between items-center mb-6">
                                        <p class="text-primary font-bold text-xl">P<?= $product['price'] ?></p>
                                        <p class="text-sm">
                                            <?php if ($product['quantity'] > 0): ?>
                                                <span class="text-green-600 bg-green-100 px-3 py-1 rounded-full">In Stock (<?= $product['quantity'] ?>)</span>
                                            <?php else: ?>
                                                <span class="text-red-600 bg-red-100 px-3 py-1 rounded-full">Out of Stock</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </a> 
                            <div class="p-8">
                            <?php if ($product['quantity'] > 0): ?>
                                <div class="flex items-center"> 
                                    <input type='hidden' name='product_id' value='<?= $product['id'] ?>' id="product-id-<?= $product['id'] ?>">
                                    <input type='number' name='quantity' value='1' min='1' max='<?= $product['quantity'] ?>' 
                                           class="w-20 px-3 py-2 border rounded-l focus:outline-none focus:ring-2 focus:ring-primary" 
                                           id="quantity-<?= $product['id'] ?>"
                                           onchange="validateQuantity(<?= $product['id'] ?>, <?= $product['quantity'] ?>)"> 
                                    <button type='button' onclick="addToCart(<?= $product['id'] ?>)" class='flex-grow bg-primary text-white font-bold py-2 px-4 rounded-r hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary'>
                                        Buy Product
                                    </button> 
                                </div>
                                <p class="add-to-cart-message-<?= $product['id'] ?> success-message hidden"></p> 
                            <?php else: ?>
                                <p class="text-red-600 font-bold text-center">Out of Stock</p>
                            <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center text-gray-600 text-xl'>No products available at the moment. Check back soon!</p>"; 
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                </div>
        </div>
    </footer>
    <script src="../../assets/js/NavigationStack.js"></script>
    <script>
        function updateNavigationStack(url) {
            navStack.push(url);
            return true; 
        }

        function validateQuantity(productId, maxQuantity) {
          let quantityInput = document.getElementById('quantity-' + productId);
          let quantity = parseInt(quantityInput.value);
          if (quantity > maxQuantity) {
            alert(`Oh no! It seems we only have ${maxQuantity} of those delicious treats left.`);
            quantityInput.value = maxQuantity; 
          } else if (quantity < 1) {
            alert('Please enter a valid quantity (at least 1).');
            quantityInput.value = 1; 
          }
        }
        
        function addToCart(productId) {
            var quantity = document.getElementById('quantity-' + productId).value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../actions/cart-actions.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        // Update cart count in the header
                        document.querySelector('.cart-count').textContent = response.cartCount;
                        
                        // Display a success message
                        var messageContainer = document.querySelector('.add-to-cart-message-' + productId);
                        messageContainer.textContent = response.message; 
                        messageContainer.classList.remove('hidden'); 

                        // Hide the message after a few seconds (optional)
                        setTimeout(function() {
                            messageContainer.classList.add('hidden'); 
                        }, 3000); 

                    } else {
                        // Display an error message if adding to cart fails (e.g., not enough stock)
                        alert(response.error); 
                    }
                } else {
                    alert("Error: " + this.status); 
                }
            };
            xhr.send('product_id=' + productId + '&quantity=' + quantity + '&add_to_cart=1'); 
        }
    </script> 

</body>
</html>