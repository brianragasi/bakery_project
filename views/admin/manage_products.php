<?php
include '../../classes/Database.php';
include '../../classes/Product.php';
include_once '../../classes/AdminProduct.php'; // Include AdminProduct

$product = new Product();
$adminProduct = new AdminProduct(); // Instantiate AdminProduct

$searchQuery = isset($_GET['search']) ? $_GET['search'] : "";
$products = $product->getProducts(); // Use $product to get products

$imageBaseUrl = 'http://localhost/bakery_oop/assets/images/';

if (!$products) {
    $errorMessage = "Error fetching products: " . $product->getError();
}

// Handle success, error, and other messages 
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Manage Products</title>
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
            <h1 class="text-2xl font-bold">Manage Products</h1>
            <a href="admin_dashboard.php" class="text-white hover:text-accent">Back to Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="manage-products bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Manage Products</h2>

            <form method="get" action="" class="mb-6 flex items-center">
                <input type="text" name="search" placeholder="Search by name or description" 
                       value="<?php echo $searchQuery; ?>"
                       class="border rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary w-full md:w-auto"
                >
                <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded-r hover:bg-green-600 transition-colors">
                    Search
                </button>
            </form>

            <a href="add_product.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors mb-4 inline-block">Add Product</a>

            <?php if (isset($errorMessage)): ?>
                <p class='text-red-500 mb-4'><?php echo $errorMessage ?></p>
            <?php endif; ?>

            <?php if ($products): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    <?php foreach ($products as $product): ?>
        <tr id="product-row-<?= $product['id'] ?>">
            <td class="px-6 py-4 whitespace-nowrap"><?= $product['id'] ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($product['name']) ?></td>

            <!-- Modified description cell -->
            <td class="px-6 py-4 whitespace-normal max-w-xs overflow-hidden">
                <div class="max-h-24 overflow-auto">
                    <?= htmlspecialchars($product['description']) ?>
                </div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap">$<?= $product['price'] ?></td>

            <!-- Image cell -->
            <td class="px-6 py-4 whitespace-nowrap">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?= $imageBaseUrl . urlencode(basename($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-40 w-40 object-cover rounded-lg">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>

            <td class="px-6 py-4 whitespace-nowrap featured-cell-<?= $product['id'] ?>"><?= $product['featured'] ? 'Yes' : 'No' ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a> |
                <button onclick="deleteProduct(<?= $product['id'] ?>)" class="text-red-600 hover:text-red-900 mr-4">Delete</button> |
                <button id="toggle-feature-button-<?= $product['id'] ?>" onclick="toggleFeatured(<?= $product['id'] ?>)" class="text-blue-600 hover:text-blue-900">
                    <?= $product['featured'] ? 'Unfeature' : 'Feature' ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>

            <?php else: ?>
                <p class="text-gray-600 mt-4">No products found.</p> 
            <?php endif; ?>
        </section>
    </main>


    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // JavaScript to Dismiss Messages  
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.addEventListener('click', () => {
                flashMessage.remove(); 
            });
            
            // Auto-dismiss after 3 seconds 
            setTimeout(() => {
                flashMessage.remove();
            }, 3000); 
        }

        function deleteProduct(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../../actions/admin-product-actions.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (this.status == 200) {
                        var rowToDelete = document.getElementById('product-row-' + productId);
                        if (rowToDelete) {
                            rowToDelete.remove();
                            alert('Product deleted successfully.');
                        } else {
                            alert('Error: Product row not found.');
                        }
                    } else {
                        alert('Error deleting product.');
                    }
                };
                xhr.send("delete_product=" + productId);
            }
        }

        function toggleFeatured(productId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../actions/admin-product-actions.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (this.status == 200) {
                    // Find the corresponding "Featured" cell and update its content
                    var featuredCell = document.querySelector('.featured-cell-' + productId);
                    if (featuredCell) {
                        // Toggle the button text and cell content in real-time
                        var newStatus = this.responseText.trim(); // Response will be "Yes" or "No"
                        featuredCell.textContent = newStatus; // Update the featured status in the table

                        // Find the button and update its text (Feature/Unfeature)
                        var button = document.querySelector('#toggle-feature-button-' + productId);
                        if (button) {
                            button.textContent = newStatus === "Yes" ? 'Unfeature' : 'Feature';
                        }
                    } 
                } else {
                    alert('Error: ' + this.status + ' ' + this.responseText);
                }
            };

            // Send the request to toggle the featured status
            xhr.send('toggle_featured=' + productId);
        }
    </script>
</body>
</html>
