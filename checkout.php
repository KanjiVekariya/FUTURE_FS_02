<!-- checkout.php -->
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  echo "<h2>Your cart is empty.</h2>";
  exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

foreach ($_SESSION['cart'] as $pid => $qty) {
  $res = $conn->query("SELECT * FROM products WHERE id=$pid");
  if ($res && $row = $res->fetch_assoc()) {
    $total += $row['price'] * $qty;
  }
}

$conn->query("INSERT INTO orders (user_id, total_price) VALUES ($user_id, $total)");
$order_id = $conn->insert_id;

foreach ($_SESSION['cart'] as $pid => $qty) {
  $res = $conn->query("SELECT * FROM products WHERE id=$pid");
  if ($res && $row = $res->fetch_assoc()) {
    $price = $row['price'];
    $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
    $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
  }
}

unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Complete</title>
  <style>
    body { font-family: Arial, sans-serif; text-align: center; padding: 2rem; background: #f0f0f0; }
    .success-box { background: white; padding: 2rem; border-radius: 8px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { color: green; }
    a { display: inline-block; margin-top: 1rem; color: #333; text-decoration: none; font-weight: bold; }
  </style>
</head>
<body>
  <div class="success-box">
    <h2>Order Placed Successfully!</h2>
    <p>Your order ID is <strong>#<?php echo $order_id; ?></strong></p>
    <p>Total Amount: ₹<?php echo $total; ?></p>
    <a href="index.php">Continue Shopping</a>
  </div>
</body>
</html>
