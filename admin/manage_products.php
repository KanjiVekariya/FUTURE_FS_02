<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit();
}
include '../db.php';

// Handle update product
if (isset($_POST['update_product'])) {
  $id = intval($_POST['id']);
  $name = $conn->real_escape_string($_POST['name']);
  $price = floatval($_POST['price']);
  $stock = intval($_POST['stock']);
  $description = $conn->real_escape_string($_POST['description']);

  // Handle image upload
  $imageUpdated = false;
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileExtension, $allowedExtensions)) {
      // Create a unique filename to avoid overwriting
      $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
      $uploadFileDir = '../assets/';
      $destPath = $uploadFileDir . $newFileName;

      if (move_uploaded_file($fileTmpPath, $destPath)) {
        $imageUpdated = true;
      } else {
        echo "<script>alert('Error uploading the image.');</script>";
      }
    } else {
      echo "<script>alert('Invalid file type. Only JPG, PNG, GIF allowed.');</script>";
    }
  }

  if ($imageUpdated) {
    $conn->query("UPDATE products SET name='$name', price=$price, stock=$stock, description='$description', image='$newFileName' WHERE id=$id");
  } else {
    $conn->query("UPDATE products SET name='$name', price=$price, stock=$stock, description='$description' WHERE id=$id");
  }

  header("Location: manage_products.php");
  exit();
}

// Delete product if requested
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM products WHERE id = $id");
  header("Location: manage_products.php");
  exit();
}

$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = [];
while ($row = $res->fetch_assoc()) {
  $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Products</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

  <style>
     /* Reset & base */
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      //background: #f0f2f5;
      margin: 0; padding: 2rem;
      color: #333;
    }
    .container {
      max-width: 100%;
      //margin: 0 auto;
      background: #fff;
     // padding: 2.5rem 3rem;
      border-radius: 12px;
      //box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    h2 {
      font-weight: 700;
      margin-bottom: 1.5rem;
      font-size: 2rem;
      color: #222;
    }
    .add-link {
      display: inline-block;
      margin-bottom: 1.5rem;
      text-decoration: none;
      background: #4f46e5;
      color: white;
      padding: 0.7rem 1.4rem;
      border-radius: 8px;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(79,70,229,0.3);
      transition: background-color 0.3s ease;
    }
    .add-link:hover {
      background: #4338ca;
      box-shadow: 0 6px 20px rgba(67,56,202,0.4);
    }
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 0.6rem;
    }
    th{
      padding: 0.75rem 1rem;
      text-align: left;
      vertical-align: middle;
      font-size: 0.85rem;
    }
	 td {
      padding: 0.05rem 1rem;
      text-align: left;
      vertical-align: middle;
      font-size: 0.85rem;
    }
    th {
      background: #e4e7f1;
      color: #555;
      font-weight: 600;
      //border-top-left-radius: 10px;
     // border-top-right-radius: 10px;
    }
    tbody tr {
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
	  border:50px solid lightgrey;
      border-radius: 10px;
      transition: box-shadow 0.3s ease;
    }
    tbody tr:hover {
      box-shadow: 0 6px 12px rgba(0,0,0,0.12);
    }
    td img {
      width: 60px;
      height: 40px;
      object-fit: cover;
      border-radius: 6px;
      //box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .actions button, .actions a {
      border: none;
      cursor: pointer;
      font-weight: 600;
      padding: 0.2rem 0.4rem;
     border-radius: 5px;
      font-size: 0.7rem;
      transition: all 0.3s ease;
      display: inline-block;
      text-decoration: none;
      user-select: none;
    }
	.actions {
  display: flex;
  justify-content: center;
  gap: 10px;
  align-items: center;
}
    .edit-btn {
      background: #3b82f6;
      color: white;
      box-shadow: 0 3px 8px rgba(59,130,246,0.4);
	  //border:5px solid #3b82f6;
	  //background-color:white;
	 // color:#3b82f6;
	}
    .edit-btn:hover {
      background: #2563eb;
      box-shadow: 0 5px 14px rgba(37,99,235,0.6);
    }
    .delete-btn {
      background: #ef4444;
      color: white;
      box-shadow: 0 3px 8px rgba(239,68,68,0.4);
    }
    .delete-btn:hover {
      background: #dc2626;
      box-shadow: 0 5px 14px rgba(220,38,38,0.6);
    }

    /* MODAL */
    .modal {
      position: fixed;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.45);
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
      z-index: 1000;
    }
    .modal.active {
      opacity: 1;
      pointer-events: auto;
    }
    .modal-content {
      background: white;
      border-radius: 14px;
      max-width: 580px;
      width: 100%;
      padding: 2rem 2.5rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
      transform: scale(0.85);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    .modal.active .modal-content {
      transform: scale(1);
      opacity: 1;
    }
    .modal-content h3 {
      font-weight: 700;
      margin: 0 0 1.25rem 0;
      color: #111827;
      font-size: 1.8rem;
      user-select: none;
    }
    .modal-content label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #374151;
    }
    .modal-content input[type="text"],
    .modal-content input[type="number"],
    .modal-content textarea {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 1.2rem;
      border: 1.8px solid #d1d5db;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 500;
      transition: border-color 0.25s ease;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #111827;
      resize: vertical;
    }
    .modal-content input[type="text"]:focus,
    .modal-content input[type="number"]:focus,
    .modal-content textarea:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 6px rgba(37, 99, 235, 0.5);
    }
    .modal-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 0.8rem;
    }
    .modal-buttons button {
      padding: 0.6rem 1.5rem;
      font-weight: 700;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      border: none;
      transition: background-color 0.3s ease;
      user-select: none;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .save-btn {
      background-color: #2563eb;
      color: white;
      box-shadow: 0 6px 16px rgba(37,99,235,0.5);
    }
    .save-btn:hover {
      background-color: #1e40af;
      box-shadow: 0 8px 22px rgba(30,64,175,0.7);
    }
    .cancel-btn {
      background-color: #6b7280;
      color: white;
      box-shadow: 0 6px 16px rgba(107,114,128,0.4);
    }
    .cancel-btn:hover {
      background-color: #4b5563;
      box-shadow: 0 8px 22px rgba(75,85,99,0.6);
    }
	body {
  font-family: 'Poppins', sans-serif;
 
}
    #imagePreview {
      width: 100%;
      max-height: 150px;
      object-fit: contain;
      margin-bottom: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      background: #f9fafb;
      display: block;
    }
	.custom-file-input {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 1rem;
}

.custom-file-input input[type="file"] {
  display: none;
}

.custom-file-input button {
  padding: 0.5rem 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.custom-file-input button:hover {
  background-color: #0056b3;
}

#fileName {
  font-style: italic;
  color: #555;
  user-select: none;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
body {
  display: flex;
  margin: 0;
  font-family: 'Poppins', sans-serif;
  //background-color: #f5f6fa;
}

.sidebar {
  width: 290px;
  min-height: 100vh;
  background-color: #f3f8fc;
  padding: 1.5rem 1rem;
  color: black;
  //box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  position: fixed;
  left: 0;
  top: 0;
  font-size:0.8rem;
}

.sidebar h2 {
  margin-bottom: 2rem;
  font-size: 1.5rem;
  text-align: center;
  color: black;
}

.sidebar a {
  display: block;
  padding: 0.75rem 1rem;
  margin: 0.5rem 0;
  color: black;
  text-decoration: none;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.sidebar a:hover,
.sidebar a.active {
  background-color: #E8F9FF;
}

.sidebar a.logout {
  margin-top: 2rem;
  color: #e74c3c;
}

main.content {
  margin-left: 230px;
  padding: 2rem;
  flex: 1;
}


  </style>
</head>
<body>
<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="index.php">🏠 Dashboard</a>
  <a href="add_product.php">➕ Add Product</a>
  <a href="manage_products.php" class="active">📦 Manage Products</a>
  <a href="orders.php">🛒 Customer Orders</a>
  <a href="logout.php" class="logout">🚪 Logout</a>
</div>
<main class="content">

  <div class="container">
    <h2>Manage Products</h2>
    <a href="add_product.php" class="add-link">+ Add New Product</a>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><img src="../assets/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>₹<?= $row['price'] ?></td>
          <td><?= $row['stock'] ?></td>
          <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
          <td class="actions" colspan=2>
            <button class="edit-btn" 
                    data-id="<?= $row['id'] ?>" 
                    data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                    data-price="<?= $row['price'] ?>"
                    data-stock="<?= $row['stock'] ?>"
                    data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                    data-image="<?= htmlspecialchars($row['image'], ENT_QUOTES) ?>"
            >Edit</button>
            <a href="manage_products.php?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal for editing -->
  <div id="editModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-content" role="document" tabindex="-1">
      <h3 id="modalTitle">Edit Product</h3>
      <form method="POST" action="manage_products.php" id="editForm" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="id" id="productId" />
        
        <label for="productName">Name:</label>
        <input type="text" name="name" id="productName" required />
        
        <label for="productPrice">Price (₹):</label>
        <input type="number" name="price" id="productPrice" step="0.01" min="0" required />
        
        <label for="productStock">Stock:</label>
        <input type="number" name="stock" id="productStock" min="0" required />
        
        <label for="productDescription">Description:</label>
        <textarea name="description" id="productDescription" rows="4" required></textarea>

        <label>Product Image (leave blank to keep current):</label>

<div class="custom-file-input">
  <button type="button" id="fileSelectBtn">Choose Image</button>
  <span id="fileName">No file chosen</span>
  <input type="file" name="image" id="productImage" accept="image/*" />
</div>

<img id="imagePreview" src="" alt="Current Image Preview" />

        
        <div class="modal-buttons">
          <button type="button" class="cancel-btn" id="cancelBtn">Cancel</button>
          <button type="submit" name="update_product" class="save-btn">Save</button>
        </div>
      </form>
    </div>
  </div>

<script>
  const modal = document.getElementById('editModal');
const modalContent = modal.querySelector('.modal-content');
const editButtons = document.querySelectorAll('.edit-btn');
const cancelBtn = document.getElementById('cancelBtn');
const form = document.getElementById('editForm');
const imagePreview = document.getElementById('imagePreview');

const fileSelectBtn = document.getElementById('fileSelectBtn');
const productImageInput = document.getElementById('productImage');
const fileNameSpan = document.getElementById('fileName');

function openModal(button) {
  document.getElementById('productId').value = button.dataset.id;
  document.getElementById('productName').value = button.dataset.name;
  document.getElementById('productPrice').value = button.dataset.price;
  document.getElementById('productStock').value = button.dataset.stock;
  document.getElementById('productDescription').value = button.dataset.description;

  // Show current image preview
  const imageFileName = button.dataset.image;
  if (imageFileName) {
    imagePreview.src = '../assets/' + imageFileName;
    imagePreview.style.display = 'block';
  } else {
    imagePreview.style.display = 'none';
  }

  // Reset file input and filename text
  productImageInput.value = '';
  fileNameSpan.textContent = 'No file chosen';

  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');
  modalContent.focus();
}

function closeModal() {
  modal.classList.remove('active');
  modal.setAttribute('aria-hidden', 'true');
}

editButtons.forEach(button => {
  button.addEventListener('click', () => openModal(button));
});

cancelBtn.addEventListener('click', closeModal);

// Close modal when clicking outside the modal content
modal.addEventListener('click', (e) => {
  if (e.target === modal) {
    closeModal();
  }
});

// Accessibility: close modal on ESC
window.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modal.classList.contains('active')) {
    closeModal();
  }
});

// Custom file input button opens file picker
fileSelectBtn.addEventListener('click', () => {
  productImageInput.click();
});

// When file input changes, update filename and preview image
productImageInput.addEventListener('change', () => {
  const file = productImageInput.files[0];
  if (file) {
    fileNameSpan.textContent = file.name;
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    fileNameSpan.textContent = 'No file chosen';
    // Reset preview to original image or hide
    const currentId = document.getElementById('productId').value;
    const originalButton = [...editButtons].find(btn => btn.dataset.id === currentId);
    if (originalButton) {
      imagePreview.src = '../assets/' + originalButton.dataset.image;
      imagePreview.style.display = 'block';
    } else {
      imagePreview.style.display = 'none';
    }
  }
});

</script>
</body>
</html>
