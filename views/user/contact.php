<?php 
session_start(); 


// *** SESSION CHECK ***
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI'])); 
    exit;
} 
// *** END SESSION CHECK ***
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Contact Us</title>
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

    <!-- Contact Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="contact bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">Contact Us</h2>
            <p class="text-center mb-6">If you have any questions or inquiries, please feel free to reach out to us.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-semibold">Contact Info</h3>
                    <p class="mt-4"><strong>Email:</strong> info@bakeeasebakery.com</p>
                    <p class="mt-2"><strong>Phone:</strong> 0907873403230</p>
                </div>
                <form method="post" action="../../actions/contact-form-handler.php" class="space-y-4">
                    <div>
                        <label for="name" class="block text-lg font-semibold">Name:</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="email" class="block text-lg font-semibold">Email:</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label for="message" class="block text-lg font-semibold">Message:</label>
                        <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    <button type="submit" name="submit" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Send Message</button>
                </form>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
                <p class="text-green-600 font-bold text-center">Your message has been sent successfully!</p>
            <?php elseif (isset($_GET['error'])) : ?>
                <p class="text-red-600 font-bold text-center"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
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
                        <a href="#" class="hover:text-accent"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg></a>
                        <a href="#" class="hover:text-accent"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg></a>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center">
                <p>© 2023 BakeEase Bakery. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>
