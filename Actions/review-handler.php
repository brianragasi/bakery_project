<?php
include "../classes/Product.php"; // Include the Product class for database interaction

session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) { 
        $userId = $_SESSION['user_id'];
        $productId = $_POST['product_id'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        // Sanitize input to prevent SQL injection and XSS
        $productId = (int)$productId; 
        $rating = (int)$rating; 
        $review = htmlspecialchars($review, ENT_QUOTES, 'UTF-8');

        // Validate rating (1-5)
        if ($rating < 1 || $rating > 5) {
            header("Location: ../views/user/product_details.php?id=$productId&error=Invalid rating. Please choose between 1 and 5.");
            exit;
        }

        $productObj = new Product();
        if ($productObj->submitReview($userId, $productId, $rating, $review)) {
            header("Location: ../views/user/product_details.php?id=$productId&success=Review submitted successfully!");
            exit;
        } else {
            $error = $productObj->getError(); 
            header("Location: ../views/user/product_details.php?id=$productId&error=Error submitting review: $error");
            exit;
        }

    } else {
        header("Location: ../views/login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI'])); 
        exit;
    }
} else {
    // Handle invalid requests 
    header("Location: ../views/user/index.php"); 
    exit;
}
?>