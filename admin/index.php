<!-- admin/index.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      background: #333;
      color: white;
      padding: 1rem;
      position: fixed;
    }
    .sidebar h2 {
      text-align: center;
    }
    .sidebar a {
      display: block;
      padding: 0.8rem;
      color: white;
      text-decoration: none;
      border-bottom: 1px solid #444;
    }
    .sidebar a:hover {
      background: #444;
    }
    .main-content {
      margin-left: 250px;
      padding: 2rem;
      width: 100%;
    }
    .logout {
      color: red;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin</h2>
    <a href="index.php">Dashboard</a>
    <a href="add_product.php">Add Product</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="orders.php">Customer Orders</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <div class="main-content">
    <h1>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h1>
    <p>This is your admin dashboard. Use the sidebar to manage the store.</p>
  </div>
</body>
</html>
