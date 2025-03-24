<?php
include '../../classes/AdminUser.php';

session_start(); 

// Admin login check 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

$adminUser = new AdminUser();

// Search Functionality 
$searchQuery = isset($_GET['search']) ? $_GET['search'] : ""; 

// Build the SQL query with search functionality
$sql = "SELECT * FROM users 
        WHERE isAdmin = 0 
        AND (name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%')"; 

$result = $adminUser->executeQuery($sql);
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Display success or error messages 
if (isset($_GET['success']) || isset($_GET['error']) || isset($_GET['message'])) { 
  echo "<div id='flash-message' class='fixed inset-0 flex items-center justify-center z-50'>";
  if (isset($_GET['success'])) {
      echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>
                <span class='block sm:inline'>" . htmlspecialchars($_GET['success']) . "</span>
            </div>";
  } elseif (isset($_GET['error'])) {
      echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>
                <span class='block sm:inline'>" . htmlspecialchars($_GET['error']) . "</span>
            </div>";
  } elseif (isset($_GET['message'])) { 
      echo "<div class='bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative' role='alert'>
                <span class='block sm:inline'>" . htmlspecialchars($_GET['message']) . "</span>
            </div>"; 
  }
  echo "</div>"; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BakeEase Bakery - Manage Users</title>
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
    <header class="bg-primary text-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Manage Users</h1>
            <a href="admin_dashboard.php" class="text-white hover:text-accent">Back to Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 flex-grow"> 
        <section class="manage-users bg-white p-8 rounded-lg shadow-md">

            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-6 flex items-center">
                <input type="text" name="search" placeholder="Search by name or email" 
                       value="<?php echo $searchQuery; ?>"
                       class="border rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary w-full md:w-auto" 
                >
                <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded-r hover:bg-green-600 transition-colors">
                    Search
                </button>
            </form>

            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th> 
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . $user['id'] . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . $user['name'] . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . $user['email'] . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>";
                            echo "<a href='edit_user.php?id=" . $user['id'] . "' class='text-indigo-600 hover:text-indigo-900 mr-4'>Edit</a>";

                            echo "<form method='post' action='../../actions/admin-user-actions.php' target='user-action-iframe' class='inline'>"; 
                            echo "<input type='hidden' name='delete_user' value='" . $user['id'] . "'>";
                            echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete this user?\")' class='text-red-600 hover:text-red-900'>Delete</button>"; 
                            echo "</form>"; 

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='px-6 py-4 whitespace-nowrap text-center'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <iframe name="user-action-iframe" style="display: none;"></iframe> 

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.addEventListener('click', () => {
                flashMessage.remove(); 
            });
        }
    </script> 
</body>
</html>