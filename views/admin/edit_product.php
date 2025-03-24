<?php
include '../../classes/AdminProduct.php';

session_start(); 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: ../login.php"); 
    exit();
}

$adminProduct = new AdminProduct();

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $product = $adminProduct->getProduct($productId); 

    if (!$product) {
        echo "Product not found.";
        exit; 
    }
} else {
    header("Location: manage_products.php"); 
    exit();
}

// Handle error messages
if (isset($_GET['error'])) {
    echo "<p class='text-red-500 font-bold mb-4'>" . htmlspecialchars($_GET['error']) . "</p>"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Edit Product</title>
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
            <h1 class="text-2xl font-bold">Edit Product</h1>
            <a href="manage_products.php" class="text-white hover:text-accent">Back to Manage Products</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="edit-product bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Product Details</h2>

            <form method="post" action="../../actions/admin-product-actions.php" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" value="<?= $product['name'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="description" class="block text-gray-700 font-bold">Description:</label>
                    <textarea id="description" name="description" required
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ><?= $product['description'] ?></textarea>
                </div>

                <div>
                    <label for="price" class="block text-gray-700 font-bold">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?= $product['price'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 font-bold">Quantity:</label> 
                    <input type="number" id="quantity" name="quantity" min="0" value="<?= $product['quantity'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="image" class="block text-gray-700 font-bold">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full"> 
                </div>

                <button type="submit" name="update_product" 
                        class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors"
                >
                    Update Product
                </button>
            </form>

        </section>
    </main>

    <footer class="bg-primary text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer> 
</body>
</html>