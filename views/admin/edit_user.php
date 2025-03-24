<?php
include '../../classes/AdminUser.php'; 

session_start(); 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id']; 
$adminUser = new AdminUser();
$userToEdit = $adminUser->getUserDetails($userId); 

// Display error message 
if (isset($_GET['error'])) {
    echo "<p class='text-red-500 font-bold mb-4'>" . htmlspecialchars($_GET['error']) . "</p>"; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BakeEase Bakery - Edit User</title>
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
            <h1 class="text-2xl font-bold">Edit User</h1>
            <a href="manage_users.php" class="text-white hover:text-accent">Back to Manage Users</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="edit-user bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">User Details</h2> 
            <?php if ($userToEdit): ?>
            <form method="post" action="../../actions/admin-user-actions.php" class="space-y-4"> 
                <input type="hidden" name="user_id" value="<?php echo $userToEdit['id']; ?>">

                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $userToEdit['name']; ?>" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-bold">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $userToEdit['email']; ?>" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-bold">New Password (Optional):</label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <button type="submit" name="update_user" 
                        class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">
                    Update User
                </button>
            </form>
            <?php else: ?> 
                <p class="text-red-500 font-bold">User not found.</p> 
            <?php endif; ?>
        </section>
    </main>

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer> 
</body>
</html>