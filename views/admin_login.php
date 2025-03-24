<?php
session_start();

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Admin Login</title>
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
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Header Section -->
    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="logo flex items-center">
            <img src="https://img.icons8.com/color/48/000000/birthday-cake.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">BakeEase Bakery Admin</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="admin_login.php" class="hover:text-accent transition-colors">Admin Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Admin Login Section -->
    <main class="container mx-auto px-4 py-8 flex-grow"> 
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">Admin Login</h2>

            <!-- Display error message if exists -->
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p><?= $error_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="../actions/admin_login_action.php" class="space-y-4">
                <div>
                    <label for="email" class="block text-lg font-semibold">Email:</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="password" class="block text-lg font-semibold">Password:</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <button type="submit" name="admin_login" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Log In</button>
            </form>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>