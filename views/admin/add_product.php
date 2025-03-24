<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Add Product</title>
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
            <h1 class="text-2xl font-bold">Add New Product</h1>
            <a href="manage_products.php" class="text-white hover:text-accent">Back to Manage Products</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="add-product bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Product Details</h2>

            <!-- Check if there is an error message to display -->
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Error:</p>
                    <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="../../actions/admin-product-actions.php" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="description" class="block text-gray-700 font-bold">Description:</label>
                    <textarea id="description" name="description" required
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ></textarea>
                </div>
                <div>
                    <label for="price" class="block text-gray-700 font-bold">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 font-bold">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="image" class="block text-gray-700 font-bold">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required
                           class="w-full"
                    >
                </div>
                <button type="submit" name="add_product" 
                        class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors"
                >
                    Add Product
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
