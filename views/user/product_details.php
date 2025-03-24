<?php
include '../../classes/Product.php';
require_once '../../classes/User.php';

session_start();

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $productObj = new Product();
    $product = $productObj->getProduct($productId);

    if ($product) {
        $imageBaseUrl = 'http://localhost/bakery_oop/assets/images/';
        $reviews = $productObj->getReviewsForProduct($productId);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>BakeEase Bakery - <?= htmlspecialchars($product['name']) ?></title>
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

                /* Styles to fix the footer overlapping */
                body {
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh; /* Make the body at least the viewport height */
                }

                main {
                    flex: 1; /* Allow main content to grow and push the footer down */
                }
            </style>
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


            <!-- Product Details Section -->
            <main class="container mx-auto px-4 py-8">
                <section class="product-details bg-white p-8 rounded-lg shadow-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="product-image">
                            <img src="<?= $imageBaseUrl . trim($product['image'], '/') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-80 object-cover rounded-lg">
                        </div>

                        <div class="product-info">
                            <h2 class="text-3xl font-bold mb-4"><?= htmlspecialchars($product['name']) ?></h2>
                            <p class="text-gray-700 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="text-2xl font-bold text-primary mb-4">Price: P<?= number_format($product['price'], 2) ?></p>

                            <p class="text-lg mb-4">Availability: 
                                <?php if ($product['quantity'] > 0): ?>
                                    <span class="text-green-600">In Stock (<?= $product['quantity'] ?> available)</span>
                                <?php else: ?>
                                    <span class="text-red-600">Out of Stock</span>
                                <?php endif; ?> 
                            </p> 

                            <!-- Add to Cart Section -->
                            <?php if ($product['quantity'] > 0): ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="flex items-center space-x-4"> 
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>" id="product-id-<?= $product['id'] ?>">
                                        <label for="quantity" class="text-lg">Quantity:</label>
                                        <input type="number" name="quantity" id="quantity-<?= $product['id'] ?>" value="1" min="1" 
                                        data-max-quantity="<?= $product['quantity'] ?>"
                                        class="w-16 px-2 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                                        <button type="button" onclick="addToCart(<?= $product['id'] ?>)" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Add to Cart</button>
                                    </div>
                                    <p class="add-to-cart-message-<?= $product['id'] ?> success-message hidden"></p> 
                                    <button onclick="goBack()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center mt-4">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                                        Back
                                    </button>
                                <?php else: ?>
                                    <a href="../login.php?redirect_to=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors inline-block text-center mt-4">
                                        Login to Add to Cart
                                    </a>
                                <?php endif; ?> 
                            <?php else: ?>
                                <p class="text-red-600 font-bold mt-4">Out of Stock</p>
                            <?php endif; ?> 
                        </div>
                    </div> 
                </section> 

              <!-- Reviews Section -->
              <section class="reviews mt-12">
                    <h3 class="text-2xl font-bold mb-4">Reviews</h3>

                    <?php if (!empty($reviews)): ?>
                        <div class="space-y-4">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review bg-white p-4 rounded-lg shadow-md">
                                    <p><strong><?= htmlspecialchars($review['user_name']) ?></strong> - Rating: <?= $review['rating'] ?>/5</p>
                                    <p class="text-gray-700"><?= htmlspecialchars($review['review']) ?></p>
                                </div>
                            <?php endforeach; ?> 
                        </div>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to leave a review!</p>
                    <?php endif; ?> 

                    <!-- Write a Review Section -->
                    <?php if (isset($_SESSION['user_id'])): 
                        $userObj = new User();
                        $hasPurchased = $productObj->hasPurchasedProduct($_SESSION['user_id'], $productId);

                        if ($hasPurchased): ?>
                            <h4 class="text-xl font-bold mt-8">Write a Review</h4>
                            <form method="post" action="../../actions/review-handler.php" class="space-y-4 mt-4">
                                <input type="hidden" name="product_id" value="<?= $productId ?>">
                                <div>
                                    <label for="rating" class="block text-lg font-semibold">Rating (1-5):</label>
                                    <input type="number" name="rating" id="rating" min="1" max="5" required class="w-16 px-2 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label for="review" class="block text-lg font-semibold">Review:</label>
                                    <textarea name="review" id="review" rows="4" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Submit Review</button>
                            </form>
                        <?php else: ?>
                            <p class="mt-4">You need to purchase this product to leave a review.</p>
                        <?php endif;  // Closing bracket for if ($hasPurchased) ?>
                    <?php else: ?>
                        <p class="mt-4">Please <a href="../login.php" class="text-primary hover:underline">log in</a> to write a review.</p>
                    <?php endif; ?> 
                </section> 
            </main> 

         <footer class="bg-primary text-white mt-12 py-8">
                <div class="container mx-auto px-4">
                    <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
                </div>
            </footer>
            

            <script src="../../assets/js/script.js"></script>
            <script> 
               
               document.addEventListener('DOMContentLoaded', function() {
                   var quantityInputs = document.querySelectorAll('input[name="quantity"]');

                   quantityInputs.forEach(function(input) {
                       input.addEventListener('change', function() {
                           var maxQuantity = parseInt(this.getAttribute('data-max-quantity'));
                           var enteredQuantity = parseInt(this.value);

                           if (enteredQuantity > maxQuantity) {
                               alert(`Oh no! It seems we only have ${maxQuantity} of those delicious treats left.`);
                               this.value = maxQuantity; // Reset the input to the maximum quantity
                           } else if (enteredQuantity < 1) {
                               alert('Please enter a valid quantity (at least 1).');
                               this.value = 1; // Reset the input to 1
                           }
                       });
                   });
               });

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

                                // Optional: Hide the message after a few seconds
                                setTimeout(function() {
                                    messageContainer.classList.add('hidden'); 
                                }, 3000); 
                            } else {
                                alert(response.message); 
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
        <?php
    } else {
        echo "<p>Product not found.</p>"; 
    } 
} else {
    header("Location: products.php"); 
    exit();
}
?>