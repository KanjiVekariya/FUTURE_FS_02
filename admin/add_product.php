<!-- admin/add_product.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $image_name = $_FILES['image']['name'];
  $image_tmp = $_FILES['image']['tmp_name'];

  $target = '../assets/' . basename($image_name);
  if (move_uploaded_file($image_tmp, $target)) {
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image_name);
    $stmt->execute();
    $success = "Product added successfully!";
  } else {
    $error = "Failed to upload image.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 2rem;
    }
    .form-container {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 1rem;
    }
    input, textarea {
      width: 100%;
      padding: 0.7rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 0.7rem;
      width: 100%;
      background: #333;
      color: white;
      border: none;
      border-radius: 5px;
    }
    .message {
      text-align: center;
      color: green;
    }
    .error {
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Add New Product</h2>
    <?php if (!empty($success)) echo "<p class='message'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Product Name" required>
      <textarea name="description" placeholder="Description" rows="4" required></textarea>
      <input type="number" step="0.01" name="price" placeholder="Price" required>
      <input type="number" name="stock" placeholder="Stock Quantity" required>
      <input type="file" name="image" accept="image/*" required>
      <button type="submit">Add Product</button>
    </form>
  </div>
</body>
</html>
