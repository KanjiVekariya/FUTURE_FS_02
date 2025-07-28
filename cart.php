<?php
session_start();
include('db.php'); // your DB connection in $conn

// Handle Add to Cart or Update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add to Cart
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $id = intval($_POST['product_id']);
        $quantity = max(1, intval($_POST['quantity']));

        // Fetch product details from DB (use prepared statements)
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Add or update quantity, cap by stock
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] += $quantity;
                if ($_SESSION['cart'][$id]['quantity'] > $product['stock']) {
                    $_SESSION['cart'][$id]['quantity'] = $product['stock'];
                }
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Update cart quantities
    if (isset($_POST['update_cart']) && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $productId => $qty) {
            $pid = intval($productId);
            $qty = max(0, intval($qty));
            if (isset($_SESSION['cart'][$pid])) {
                if ($qty == 0) {
                    // Remove item if qty=0
                    unset($_SESSION['cart'][$pid]);
                } else {
                    // Update quantity but don't exceed stock
                    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
                    $stmt->bind_param("i", $pid);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $prod = $res->fetch_assoc();
                    $stock = $prod ? intval($prod['stock']) : 0;

                    $_SESSION['cart'][$pid]['quantity'] = min($qty, $stock);
                }
            }
        }
    }

    // Clear cart
    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Cart</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 2rem; }
    table { width: 100%; max-width: 800px; margin: auto; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
    th {  color: black; }
    img { max-width: 80px; border-radius: 6px; }
    input[type=number] { width: 60px; padding: 6px; }
    button { padding: 8px 15px; border: none; border-radius: 5px; background: #007bff; color: white; cursor: pointer; }
    button:hover { background: #0056b3; }
    .actions { margin: 1rem auto; max-width: 800px; text-align: center; }
    a { text-decoration: none; color: #007bff; }
  </style>
</head>
<body>

<h1 style="text-align:center;">Your Shopping Cart</h1>

<?php if (!empty($_SESSION['cart'])): ?>
<form method="post" action="cart.php">
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Price (₹)</th>
        <th>Quantity</th>
        <th>Subtotal (₹)</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $total = 0;
      foreach ($_SESSION['cart'] as $item): 
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
      ?>
      <tr>
        <td style="text-align:left;">
          <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
          <br><?php echo htmlspecialchars($item['name']); ?>
        </td>
        <td><?php echo number_format($item['price'], 2); ?></td>
        <td>
          <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" />
          <small>(Set 0 to remove)</small>
        </td>
        <td><?php echo number_format($subtotal, 2); ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="3" style="text-align:right;">Total:</th>
        <th>₹<?php echo number_format($total, 2); ?></th>
      </tr>
    </tbody>
  </table>

  <div class="actions">
    <button type="submit" name="update_cart">Update Cart</button>
    <button type="submit" name="clear_cart" onclick="return confirm('Are you sure you want to clear the cart?');">Clear Cart</button>
  </div>
</form>
<?php else: ?>
  <p style="text-align:center;">Your cart is empty. <a href="index.php">Shop now</a></p>
<?php endif; ?>

</body>
</html>
