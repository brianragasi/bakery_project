<?php
require_once "Database.php";

class AdminUser extends Database {

    public function displayUsers() {
        $sql = "SELECT * FROM users WHERE isAdmin = 0"; 
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUserDetails($userId) {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $sql = "SELECT * FROM users WHERE id = '$userId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateUser($userId, $name, $email, $password = null) { 
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $name = mysqli_real_escape_string($this->conn, $name);
        $email = mysqli_real_escape_string($this->conn, $email);

        $updateFields = "name = '$name', email = '$email'";

        if (!is_null($password) && !empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $updateFields .= ", password = '$password'";
        }

        $sql = "UPDATE users SET $updateFields WHERE id = '$userId'";
        return $this->conn->query($sql);
    }

    public function deleteUser($userId) {
        $userId = mysqli_real_escape_string($this->conn, $userId);

        $this->conn->begin_transaction();

        try {
            // 1. Delete related reviews
            $deleteReviewsSql = "DELETE FROM reviews WHERE user_id = '$userId'";
            if (!$this->conn->query($deleteReviewsSql)) {
                throw new Exception("Error deleting user reviews: " . $this->conn->error);
            }

            // 2. Delete related order_items
            $deleteOrderItemsSql = "DELETE oi FROM order_items oi 
                                    INNER JOIN orders o ON oi.order_id = o.id
                                    WHERE o.user_id = '$userId'";
            if (!$this->conn->query($deleteOrderItemsSql)) {
                throw new Exception("Error deleting related order items: " . $this->conn->error);
            }

            // 3. Delete associated orders
            $deleteOrdersSql = "DELETE FROM orders WHERE user_id = '$userId'";
            if (!$this->conn->query($deleteOrdersSql)) {
                throw new Exception("Error deleting user's orders: " . $this->conn->error);
            }

            // 4. Delete the user
            $deleteUserSql = "DELETE FROM users WHERE id = '$userId' AND isAdmin = 0";
            if (!$this->conn->query($deleteUserSql)) {
                throw new Exception("Error deleting user: " . $this->conn->error);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("User deletion failed: " . $e->getMessage());
            return $e->getMessage(); // Return specific error
        }
    }

    public function executeQuery($sql) {
        return $this->conn->query($sql);
    }
}
?>