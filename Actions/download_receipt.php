<?php
/**
 * download_receipt.php
 * Generates a PDF receipt using Dompdf and real order data.
 */

// 1. Load Dompdf from a sibling folder (adjust the path as needed)
require_once __DIR__ . '/../dompdf/autoload.inc.php';

// 2. Include the Order class to fetch order details from the database
require_once __DIR__ . '/../classes/Order.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

// (Optional) Ensure user is logged in or allowed to download receipt
// For example:
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../views/login.php");
//     exit();
// }

// 3. Get order ID from URL query parameter
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    die("Invalid order ID.");
}

// 4. Instantiate the Order object and fetch order details
$orderObj = new Order();
$orderDetails = $orderObj->getOrder($orderId);
if (!$orderDetails) {
    die("Order not found.");
}

// 5. Retrieve the individual order items (products) for this order
$sql = "SELECT p.name, oi.quantity, p.price 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = '$orderId'";
$result = $orderObj->executeQuery($sql);
$items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// 6. Build the HTML content for the PDF receipt
$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif; 
      margin: 0; padding: 0;
    }
    .receipt-container {
      padding: 20px;
    }
    .header { text-align: center; }
    .logo { width: 80px; height: auto; }
    .order-info, .customer-info {
      margin-bottom: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border: 1px solid #cccccc;
      padding: 8px;
      text-align: left;
    }
    .total {
      text-align: right;
      font-weight: bold;
      margin-top: 10px;
    }
    .footer {
      text-align: center;
      margin-top: 40px;
      font-size: 0.9em;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="receipt-container">
    <div class="header">
      <img src="https://img.icons8.com/color/96/000000/birthday-cake.png" alt="Logo" class="logo"/>
      <h1>BakeEase Bakery</h1>
      <p><strong>Official Receipt</strong></p>
    </div>
    
    <div class="order-info">
      <p><strong>Order ID:</strong> ' . htmlspecialchars($orderDetails['id']) . '</p>
      <p><strong>Order Date:</strong> ' . htmlspecialchars($orderDetails['order_date']) . '</p>
      <p><strong>Payment Method:</strong> ' . htmlspecialchars($orderDetails['payment_method']) . '</p>
    </div>
    
    <div class="customer-info">
      <p><strong>Customer Email:</strong> ' . htmlspecialchars($orderDetails['customer_email']) . '</p>
      <p><strong>Delivery Address:</strong> ' . htmlspecialchars($orderDetails['address']) . '</p>
    </div>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th style="width: 80px;">Qty</th>
          <th style="width: 100px;">Price</th>
        </tr>
      </thead>
      <tbody>';
      
foreach ($items as $prod) {
    $html .= '<tr>
        <td>' . htmlspecialchars($prod['name']) . '</td>
        <td>' . (int)$prod['quantity'] . '</td>
        <td>₱' . number_format($prod['price'], 2) . '</td>
    </tr>';
}

$html .= '
      </tbody>
    </table>
    <p class="total">Total: ₱' . number_format($orderDetails['final_total'], 2) . '</p>
    
    <div class="footer">
      <p>Thank you for ordering at BakeEase Bakery!</p>
    </div>
  </div>
</body>
</html>
';

// 7. Configure Dompdf options
$options = new Options();
$options->set('isRemoteEnabled', true); // Allows remote images (e.g., logo)
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

// 8. Load HTML content into Dompdf and render PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 9. Stream the PDF to the browser as a downloadable file
$filename = 'receipt_' . $orderId . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
