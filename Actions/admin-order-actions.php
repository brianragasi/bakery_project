<?php
include "../classes/AdminOrder.php"; 
include "../classes/Order.php";

session_start(); 

$adminOrder = new AdminOrder();
$order = new Order(); 

// --- HANDLE ADMIN ACTIONS ---
if (isset($_SESSION['user_id']) && isset($_SESSION['admin']) && $_SESSION['admin']) { 

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['new_status']; 

        // Use the updateOrderStatus() method from the Order class
        if ($order->updateOrderStatus($orderId, $newStatus)) { 

            // --- BEGIN EMAIL NOTIFICATION --- 

            // 1. Get customer email (make sure column name is correct)
            $orderDetails = $adminOrder->getOrder($orderId);
            $customerEmail = $orderDetails['customer_email']; // Get the actual email from the order details
            // echo "Attempting to send email to: " . $customerEmail . "..."; // Remove this line (for production)

            // 2. Construct email content
            $subject = "Your BakeEase Bakery Order Status Update";
            $message = "Hello,\n\nYour order (ID: $orderId) status has been updated to: $newStatus.\n\n";
            $message .= "Thank you for your order!\n\nBakeEase Bakery";
            
            // 3. Set email headers
            $headers = "From: youractualemail@yourdomain.com" . "\r\n" . // Replace with your ACTUAL email address
                       "Reply-To: youractualemail@yourdomain.com" . "\r\n" . // Replace with your ACTUAL reply-to address
                       "Content-Type: text/plain; charset=UTF-8";  

            // 4. Send the email (and log errors)
            if (mail($customerEmail, $subject, $message, $headers)) {
                error_log("Email sent successfully to: " . $customerEmail); 
            } else {
                error_log("Email Error: " . error_get_last()['message']); 
            }

            // --- END EMAIL NOTIFICATION ---

            // Redirect parent page with success message
            echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order status updated successfully.") . "';</script>";
            exit;
        } else { 
            // Redirect parent page with error message
            echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error updating order status.") . "';</script>";
            exit;
        }

    } elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_order'])) {
        $orderIdToDelete = $_GET['delete_order'];

        if ($adminOrder->deleteOrder($orderIdToDelete)) {
            // Redirect parent page with success message
            echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order deleted successfully.") . "';</script>";
            exit; 
        } else {
            // Redirect parent page with error message
            echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error deleting order.") . "';</script>";
            exit;
        }
    } 

} // --- END OF ADMIN ACTIONS ---


// --- HANDLE CUSTOMER ORDER DELETION ---
elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_user_order'])) {
    $orderIdToDelete = $_GET['delete_user_order'];

    if (isset($_SESSION['user_id'])) { 
        $userId = $_SESSION['user_id'];
        $orderDetails = $adminOrder->getOrder($orderIdToDelete); 

        // Allow deletion if the order exists, the user owns it, AND the status is cancelled or delivered
        if ($orderDetails && $orderDetails['user_id'] == $userId && 
           ($orderDetails['status'] == 'cancelled' || $orderDetails['status'] == 'delivered')) {

            if ($adminOrder->deleteOrder($orderIdToDelete)) {
                header("Location: ../views/user/profile.php?success=" . urlencode("Order deleted from history."));
                exit;
            } else {
                header("Location: ../views/user/profile.php?error=" . urlencode("Error deleting order from history."));
                exit;
            }
        } else {
            header("Location: ../views/user/profile.php?error=" . urlencode("You do not have permission to delete this order."));
            exit;
        }
    } else {
        header("Location: ../views/login.php"); 
        exit;
    }
} // --- END CUSTOMER ORDER DELETION --- 

else {
  // Handle any other unauthorized access attempts
  echo "Unauthorized access."; 
  exit();
}
?>