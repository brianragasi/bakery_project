<?php
require_once "Database.php";
require_once "User.php"; 

class Product extends Database {

    public function getProducts() {
        $sql = "SELECT * FROM products";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getProduct($productId) {
        $productId = $this->conn->real_escape_string($productId);
        $sql = "SELECT * FROM products WHERE id = '$productId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function getFeaturedProducts() {
        $sql = "SELECT * FROM products WHERE featured = 1"; 
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getReviewsForProduct($productId) {
        $productId = $this->conn->real_escape_string($productId);
        $sql = "SELECT r.*, u.name AS user_name
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = '$productId'
                ORDER BY r.review_date DESC"; // Orders reviews by date (newest first)
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function hasPurchasedProduct($userId, $productId) {
        $userId = $this->conn->real_escape_string($userId);
        $productId = $this->conn->real_escape_string($productId);
        $sql = "SELECT 1
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = '$userId' AND oi.product_id = '$productId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0);
    }

    public function executeQuery($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            $this->error = $this->conn->error; 
        }
        return $result;
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    public function getError() {
        return $this->error; 
    }

    // Submit Review Method
    public function submitReview($userId, $productId, $rating, $review) {
        $sql = "INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)"; 
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            return false;
        }

        $stmt->bind_param("iiis", $userId, $productId, $rating, $review);

        if ($stmt->execute()) {
            $stmt->close();
            return true; 
        } else {
            $this->error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();
            return false;
        }
    }

    // Update Product Function
    public function updateProduct($productId, $name, $description, $price, $quantity, $imagePath) {
        if ($imagePath !== null) {
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, image = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdssi", $name, $description, $price, $quantity, $imagePath, $productId);
        } else {
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdii", $name, $description, $price, $quantity, $productId);
        }
    
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating record: " . $this->conn->error;
            return false;
        }
    }
    
    // Delete Product Function
    public function deleteProduct($productIdToDelete) {
      try {
          // 1. Start a transaction
          $this->conn->begin_transaction();
  
          // 2. Delete related order items
          $deleteItemsSql = "DELETE FROM order_items WHERE product_id = ?";
          $stmt = $this->conn->prepare($deleteItemsSql);
          if (!$stmt) {
              throw new Exception("Error preparing to delete order items: " . $this->conn->error);
          }
          $stmt->bind_param("i", $productIdToDelete);
          if (!$stmt->execute()) {
              throw new Exception("Error deleting order items: " . $stmt->error);
          }
          $stmt->close();
  
          // 3. Delete the product
          $sql = "DELETE FROM products WHERE id = ?";
          $stmt = $this->conn->prepare($sql);
          if (!$stmt) {
              throw new Exception("Prepare failed: " . $this->conn->error);
          }
          $stmt->bind_param("i", $productIdToDelete);
          if (!$stmt->execute()) {
              throw new Exception("Execute failed: " . $stmt->error);
          }
          $stmt->close();
  
          // 4. Commit the transaction
          $this->conn->commit();
          return true; 
  
      } catch (Exception $e) {
          // 5. Rollback if there's an error
          $this->conn->rollback();
          $this->error = $e->getMessage();
          return false; 
      }
  }
}
?>