<?php
require_once "Database.php";

class AdminOrder extends Database {
  public function getOrders() {
    $sql = "SELECT o.*, u.name AS customer_name,
               SUBSTRING_INDEX(GROUP_CONCAT(p.name SEPARATOR ', '), ', ', 3) AS product_names,
               SUM(oi.quantity) AS total_quantity,
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_products_in_order,
               CASE
                   WHEN o.order_type = 'delivery' THEN SUBSTRING_INDEX(o.delivery_address, '\n', 1)
                   ELSE CONCAT('Pickup - ', o.pickup_time)
               END AS delivery_info,
               o.final_total AS total_price  -- ADDED THIS LINE to select final_total as total_price
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            GROUP BY o.id
            ORDER BY o.id DESC";
    $result = $this->conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
  }

    public function getOrderDetailsWithItems($orderId) {
        $orderId = mysqli_real_escape_string($this->conn, $orderId);

        $sql = "SELECT
                o.id AS order_id,
                o.order_date,
                o.status,
                o.payment_method,
                o.delivery_address,
                o.pickup_time,
                o.order_type,
                o.final_total,
                u.name AS customer_name,
                u.email AS customer_email,
                p.id AS product_id,
                p.name AS product_name,
                p.price AS product_price,
                oi.quantity AS item_quantity
            FROM orders o
            INNER JOIN users u ON o.user_id = u.id
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE o.id = '$orderId'
            ORDER BY o.order_date DESC";

        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOrder($orderId) {
        $orderId = mysqli_real_escape_string($this->conn, $orderId);

        $sql = "SELECT o.*, u.name AS customer_name, p.name AS product_name, o.customer_email
        FROM orders o
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN order_items oi ON o.id = oi.order_id
        INNER JOIN products p ON oi.product_id = p.id
        WHERE o.id = '$orderId'";

        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateOrderStatus($orderId, $newStatus) {
        $orderId = mysqli_real_escape_string($this->conn, $orderId);
        $newStatus = mysqli_real_escape_string($this->conn, $newStatus);

        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    public function deleteOrder($orderId) {
      $orderId = mysqli_real_escape_string($this->conn, $orderId);

      $this->conn->begin_transaction();

      try {
          $deleteItemsSql = "DELETE FROM order_items WHERE order_id = '$orderId'";
          if (!$this->conn->query($deleteItemsSql)) {
              throw new Exception("Error deleting order items: " . $this->conn->error);
          }

          $deleteOrderSql = "DELETE FROM orders WHERE id = '$orderId'";
          if (!$this->conn->query($deleteOrderSql)) {
              throw new Exception("Error deleting order: " . $this->conn->error);
          }

          $this->conn->commit();
          return true;

      } catch (Exception $e) {
          $this->conn->rollback();
          error_log("Order deletion failed: " . $e->getMessage());
          return false;
      }
  }
}

?>