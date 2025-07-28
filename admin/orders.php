<!-- admin/orders.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit();
}
include '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Orders</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      padding: 2rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 1rem;
    }
    .status-select {
      padding: 0.3rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Customer Orders</h2>
    <table>
      <tr>
        <th>Order ID</th>
        <th>User ID</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Date</th>
        <th>Items</th>
      </tr>
      <?php
      $orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
      while ($order = $orders->fetch_assoc()) {
        echo "<tr>
          <td>{$order['id']}</td>
          <td>{$order['user_id']}</td>
          <td>₹{$order['total_price']}</td>
          <td>{$order['status']}</td>
          <td>{$order['created_at']}</td>
          <td>";

        $items = $conn->query("SELECT oi.quantity, oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']}");
        while ($item = $items->fetch_assoc()) {
          echo "{$item['name']} × {$item['quantity']} (₹{$item['price']})<br>";
        }

        echo "</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
