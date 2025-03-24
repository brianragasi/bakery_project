<?php

// Include necessary classes
include '../classes/Database.php';
include '../classes/Product.php'; 
include_once '../classes/AdminProduct.php'; 

// Start the session
session_start();

// --- STRICT ADMIN LOGIN CHECK ---
// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    // If not authorized, redirect to the login page with an error message
    header("Location: ../views/login.php?error=" . urlencode("You are not authorized to access the admin panel.")); 
    exit(); 
}
// --- END ADMIN LOGIN CHECK ---

// Instantiate the classes
$adminProduct = new AdminProduct(); 
$product = new Product(); 

// Define the relative upload path (relative to document root)
$relativeUploadPath = "/bakery_oop/assets/images/";

// Function to sanitize filenames
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename); 
    return $filename;
}

function handleImageUpload($file, $relativeUploadPath) {
    $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . $relativeUploadPath;
    $targetFile = $targetDirectory . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["error" => "File is not an image."];
    }

    if ($file["size"] > 5000000) {
        return ["error" => "Sorry, your file is too large."];
    }

    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if(!in_array($imageFileType, $allowedTypes)) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }

    $originalFileName = $targetFile;
    $i = 1;
    while (file_exists($targetFile)) {
        $targetFile = $originalFileName . "_" . $i . "." . $imageFileType;
        $i++;
    }

    if (is_uploaded_file($file["tmp_name"])) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $imageFilename = sanitizeFilename(basename($targetFile)); // Sanitize filename
            return ["success" => $imageFilename];  // Return only the filename
        } else {
            $uploadError = $file["error"]; 
            return ["error" => "Sorry, there was an error uploading your file. Error code: " . $uploadError];
        }
    } else {
        error_log("Temporary uploaded file not found: " . $file["tmp_name"]);
        return ["error" => "Temporary uploaded file not found."];
    }
}

// Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $adminProduct->escapeString($_POST['name']);
    $description = $adminProduct->escapeString($_POST['description']);
    $price = (float)$adminProduct->escapeString($_POST['price']);
    $quantity = (int)$adminProduct->escapeString($_POST['quantity']);
    $imagePath = null;  

    if ($_FILES['image']['error'] === 0) { 
        $uploadResult = handleImageUpload($_FILES["image"], $relativeUploadPath);

        if (isset($uploadResult["success"])) {
            $imagePath = $uploadResult["success"]; 
        } else {
            $errorMessage = $uploadResult["error"];
            header("Location: ../views/admin/add_product.php?error=" . urlencode($errorMessage));
            exit; 
        }
    }

    if ($adminProduct->addProduct($name, $description, $price, $quantity, $imagePath)) {
        $newProductId = $adminProduct->conn->insert_id; 

        // Redirect with new product data as URL parameters 
        header("Location: ../views/admin/manage_products.php?success=Product added successfully.&new_product_id=$newProductId&new_product_name=" . urlencode($name) . "&new_product_description=" . urlencode($description) . "&new_product_price=" . urlencode($price) . "&new_product_image=" . urlencode($imagePath) . "&new_product_featured=0"); 
        exit; 
    } else {
        $error = $adminProduct->getError(); 
        header("Location: ../views/admin/add_product.php?error=Error adding product: " . urlencode($error));
        exit;
    }
}

// Update Product 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    // Check and sanitize inputs
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $name = isset($_POST['name']) ? $adminProduct->escapeString($_POST['name']) : null;
    $description = isset($_POST['description']) ? $adminProduct->escapeString($_POST['description']) : null;
    $price = isset($_POST['price']) ? (float)$adminProduct->escapeString($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? (int)$adminProduct->escapeString($_POST['quantity']) : null;
    $imagePath = null;

    if ($_FILES['image']['error'] === 0) { 
        $uploadResult = handleImageUpload($_FILES["image"], $relativeUploadPath);

        if (isset($uploadResult["success"])) {
            $imagePath = $uploadResult["success"]; 

            // Delete the old image if one exists for this product
            $oldProduct = $adminProduct->getProduct($productId);
        } else {
            $errorMessage = $uploadResult["error"];
            header("Location: ../views/admin/edit_product.php?id=$productId&error=" . urlencode($errorMessage));
            exit; 
        }
    }

    if ($product->updateProduct($productId, $name, $description, $price, $quantity, $imagePath)) {
        if ($oldProduct && !empty($oldProduct['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldProduct['image'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $oldProduct['image']); 
        }
        header("Location: ../views/admin/manage_products.php?success=Product updated successfully.");
        exit;
    } else {
        $error = $product->getError(); 
        header("Location: ../views/admin/edit_product.php?id=$productId&error=Error updating product: " . urlencode($error));
        exit;
    }
}

// Delete Product (Modified for AJAX)
if (isset($_POST['delete_product'])) {
  $productIdToDelete = (int)$_POST['delete_product'];

  if ($product->deleteProduct($productIdToDelete)) {
      // Send success response for AJAX
      echo "Product deleted successfully.";
      exit;
  } else {
      // Send error response for AJAX
      echo "Error deleting product: " . $product->getError();
      exit;
  }
}

// Set Featured (Toggle) - Modified for AJAX
if (isset($_POST['toggle_featured'])) { 
    $productId = $adminProduct->escapeString($_POST['toggle_featured']);
    $productData = $adminProduct->getProduct($productId);

    if ($productData) {
        $newFeaturedStatus = $productData['featured'] ? 0 : 1; 
        if ($adminProduct->setFeatured($productId, $newFeaturedStatus)) {
            // Echo the new featured status ("Yes" or "No")
            echo $newFeaturedStatus ? 'Yes' : 'No'; 
            exit; // Stop further execution 
        } else {
            echo "Error updating featured status: " . $adminProduct->getError();
            exit;
        }
    } else {
        echo "Product not found.";
        exit;
    }
}

// Handle Quantity Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['new_quantity'];

    if ($adminProduct->updateProductQuantity($productId, $newQuantity)) { 
        header("Location: ../views/admin/manage_products.php?success=Quantity updated successfully.");
        exit;
    } else {
        header("Location: ../views/admin/manage_products.php?error=Error updating quantity.");
        exit;
    }
}

?>