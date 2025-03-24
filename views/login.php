<?php
include '../Classes/User.php';

session_start(); 

// Check for error or success messages and display them
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Login</title>
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
        </div>
    </header>

    <!-- Login Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">Login</h2>

            <!-- Display error or success messages -->
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p><?= $error_message ?></p>
                </div>
            <?php elseif (isset($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <p><?= $success_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-4">
                <div>
                    <label for="email" class="block text-lg font-semibold">Email:</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="password" class="block text-lg font-semibold">Password:</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <button type="submit" name="login" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Log In</button>
            </form>

            <p class="mt-4 text-center">Don't have an account? <a href="user/register.php" class="text-primary hover:underline">Register here</a>.</p>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <?php
    // Handle login form submission
    if (isset($_POST['login'])) {
        $user = new User(); 

        $email = $_POST['email'];
        $password = $_POST['password'];

        $loginResult = $user->login($email, $password);

        if (is_string($loginResult)) { 
            header("Location: login.php?error=" . urlencode($loginResult)); 
            exit;
        }
        // If login is successful, redirect is handled in User->login()
    }
    ?>
</body>
</html>
