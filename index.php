<!-- index.php -->
<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - E-Commerce</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }

    header {
      background: #111;
      color: #fff;
     // padding: 1rem;
      text-align: center;
      font-size: 1.5rem;
      letter-spacing: 1px;
	
	  
	}

    nav {
      background: #fff;
	 
      padding: 1rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    nav a {
      margin: 0 1rem;
      text-decoration: none;
      color: #111;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #007bff;
    }

    .product-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 2rem;
      gap: 1.5rem;
    }

    .product {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      width: 220px;
      text-align: center;
      padding: 1rem;
    }

    .product:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .product img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    .product strong {
      display: block;
      font-size: 1.1rem;
      margin: 0.5rem 0;
      color: #333;
    }

    .product a {
      display: inline-block;
      margin-top: 0.5rem;
      padding: 0.5rem 1rem;
      background: #007bff;
      color: #fff;
      border-radius: 5px;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .product a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <header>
    <h1>Shoping bazar</h1>
  </header>
  <nav>
    <a href="index.php">Home</a>
    <a href="cart.php">View Cart</a>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Signup</a>
    <?php endif; ?>
  </nav>

  <div class="product-list">
    <?php
    $res = $conn->query("SELECT * FROM products");
    while ($row = $res->fetch_assoc()) {
      echo "<div class='product'>
        <img src='assets/{$row['image']}' alt='{$row['name']}'><br>
        <strong>{$row['name']}</strong>
        ₹{$row['price']}<br>
        <a href='product.php?id={$row['id']}'>View</a>
      </div>";
    }
    ?>
  </div>
</body>
</html>
