<!-- product.php -->
<?php include 'db.php'; session_start();
if (!isset($_GET['id'])) die("Product not found.");
$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM products WHERE id=$id");
if (!$res || $res->num_rows === 0) die("Product not found.");
$product = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['name']); ?> - Product</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 2rem; }
    .product-box { background: #fff; padding: 2rem; max-width: 600px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    img { max-width: 100%; height: auto; }
    .product-info { margin-top: 1rem; }
    .product-info h2 { margin-bottom: 0.5rem; }
    form { margin-top: 1rem; }
    input[type=number] { padding: 0.5rem; width: 60px; margin-right: 1rem; }
    button { padding: 0.5rem 1rem; background: #333; color: #fff; border: none; border-radius: 5px; }
  </style>
</head>
<body>
  <div class="product-box">
    <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <div class="product-info">
      <h2><?php echo htmlspecialchars($product['name']); ?></h2>
      <p><strong>Price:</strong> ₹<?php echo $product['price']; ?></p>
      <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      <form method="post" action="cart.php">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
        <button type="submit" name="add">Add to Cart</button>
      </form>
    </div>
  </div>
</body>
</html>
