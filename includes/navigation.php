<header class="bg-primary text-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="logo flex items-center">
            <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
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
                    <li><a href="login.php" class="hover:text-accent transition-colors">Login</a></li>
                    <li><a href="register.php" class="hover:text-accent transition-colors">Register</a></li>
                <?php endif; ?>
                <li>
                    <a href="cart.php" class="relative hover:text-accent transition-colors">
                        Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>