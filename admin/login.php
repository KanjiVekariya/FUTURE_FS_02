<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Direct comparison (NOT SECURE for real use)
  $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
  $res = $conn->query($sql);

  if ($res && $res->num_rows > 0) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    header("Location: index.php");
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Simple Admin Login</title>
  <style>
    body {
      font-family: Arial;
      background: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 300px;
    }
    input {
      width: 100%;
      padding: 0.5rem;
      margin: 0.5rem 0;
    }
    button {
      width: 100%;
      padding: 0.5rem;
      background: #333;
      color: white;
      border: none;
    }
    .error {
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
