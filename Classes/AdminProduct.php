<?php
require_once "Database.php";
require_once "Product.php"; 

class AdminProduct extends Database {
    public $error;

    public function __construct() {
        parent::__construct();
    }

    public function getProducts($search = null) {
        $sql = "SELECT * FROM products";
        if ($search) {
            $search = '%' . $search . '%';
            $sql .= " WHERE name LIKE ? OR description LIKE ?";
        }

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Error preparing SQL statement: " . $this->conn->error;
            return false; 
        }

        if ($search) {
            $stmt->bind_param("ss", $search, $search);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $products;
        } else {
            $this->error = "Error executing SQL statement: " . $stmt->error;
            $stmt->close();
            return false; 
        }
    }

    public function getProduct($productId) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            return null;
        }

        $stmt->bind_param("i", $productId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();
            return $product; 
        } else {
            $this->error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();
            return null;
        }
    }

    public function setFeatured($productId, $featured) {
        $sql = "UPDATE products SET featured = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            return false;
        }

        $stmt->bind_param("ii", $featured, $productId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $this->error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();
            return false;
        }
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    public function getError() {
        return $this->error;
    }

    public function executeQuery($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            $this->error = $this->conn->error; 
        }
        return $result;
    }

    public function updateProductQuantity($productId, $newQuantity) {
      $sql = "UPDATE products SET quantity = ? WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      if (!$stmt) {
          $this->error = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
          return false;
      }

      $stmt->bind_param("ii", $newQuantity, $productId);

      if ($stmt->execute()) {
          $stmt->close();
          return true;
      } else {
          $this->error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          $stmt->close();
          return false;
      }
    }

    public function addProduct($name, $description, $price, $quantity, $imagePath) {
        $sql = "INSERT INTO products (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            return false;
        }

        $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $imagePath);

        if ($stmt->execute()) {
            $stmt->close();
            return true; 
        } else {
            $this->error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();
            return false;
        }
    }
    public function getLowStockProducts($threshold) {
      $sql = "SELECT * FROM products WHERE quantity <= ?";
      $stmt = $this->conn->prepare($sql);

      if (!$stmt) {
          $this->error = "Error preparing statement: " . $this->conn->error;
          return false; // Or handle the error as needed
      }

      $stmt->bind_param("i", $threshold);

      if ($stmt->execute()) {
          $result = $stmt->get_result();
          $lowStockProducts = $result->fetch_all(MYSQLI_ASSOC);
          $stmt->close(); // Close the statement
          return $lowStockProducts;
      } else {
          $this->error = "Error executing statement: " . $stmt->error;
          $stmt->close(); // Close the statement even on error
          return false; // Or handle the error as needed
      }
  }
}
?>