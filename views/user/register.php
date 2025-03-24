<?php
session_start();

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
$success_message = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Register</title>
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
        </div>
    </header>

    <!-- Registration Form Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">Create an Account</h2>
            <p class="text-center mb-6">Join our bakery community to enjoy delicious treats and exclusive offers!</p>

            <!-- Display error or success messages -->
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p><?= $error_message ?></p>
                </div>
            <?php elseif ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <p><?= $success_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                <div>
                    <label for="name" class="block text-lg font-semibold">Name:</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="email" class="block text-lg font-semibold">Email:</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="password" class="block text-lg font-semibold">Password:</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <button type="submit" name="register" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Register</button>
            </form>

            <p class="mt-4 text-center">Already have an account? <a href="../login.php" class="text-primary hover:underline">Login here</a>.</p>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <?php
    // Handle registration form submission
    if (isset($_POST['register'])) {
        include '../../classes/User.php'; 
        $user = new User();

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $registrationResult = $user->register($name, $email, $password);

        // Redirect handling 
        if (strpos($registrationResult, 'Error') === 0) {
            // Registration error
            header("Location: register.php?error=" . urlencode($registrationResult)); 
        } else { 
            // Successful registration
            $_SESSION['user_id'] = $user->conn->insert_id; 
            $_SESSION['user_name'] = $name; 

            // Set new registration flag
            $_SESSION['new_registration'] = true; 

            // Set welcome_shown flag to prevent "Welcome Back" on home page
            $_SESSION['welcome_shown'] = true;

            // Redirect after registration
            $redirect_to = isset($_GET['redirect_to']) ? urldecode($_GET['redirect_to']) : 'index.php';
            header("Location: $redirect_to");
        }
        exit; 
    } 
    ?>
</body>
</html>