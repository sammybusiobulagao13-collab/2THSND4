<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login/login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: products.php');
    exit();
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $image = $_POST['image'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (isset($_POST['edit_id']) && $_POST['edit_id']) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, image=?, description=? WHERE id=?");
        $stmt->execute([$name, $price, $stock, $image, $description, $_POST['edit_id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, image, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $stock, $image, $description]);
    }
    header('Location: products.php');
    exit();
}

// Handle stock update
if (isset($_POST['update_stock'])) {
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$_POST['stock'], $_POST['product_id']]);
    header('Location: products.php');
    exit();
}

// Handle add stock via GET
if (isset($_GET['add_stock'])) {
    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    
    if ($product_id > 0 && $quantity > 0) {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);
    }
    header('Location: products.php');
    exit();
}

// Get unread messages count for badge
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();

$products = $pdo->query("SELECT * FROM products ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Admin (Products)</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #0a0a1a; }
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #000; padding: 20px; border-right: 1px solid rgba(255,255,255,0.05); min-height: 100vh; position: fixed; height: 100%; overflow-y: auto; z-index: 99; }
        .admin-sidebar .logo { font-size: 24px; font-weight: 700; color: #fff; padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px; text-align: center; letter-spacing: 2px; }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li { margin-bottom: 2px; }
        .admin-sidebar ul li a { display: block; padding: 12px 18px; color: #888; text-decoration: none; border-radius: 10px; transition: all 0.3s; font-size: 14px; }
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active { background: rgba(255,255,255,0.05); color: #fff; }
        .admin-sidebar ul li a i { width: 22px; margin-right: 12px; text-align: center; }
        .admin-sidebar .logout-link { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
        .admin-sidebar .logout-link a { color: #ff4444 !important; }
        .admin-sidebar .logout-link a:hover { background: rgba(255,68,68,0.1) !important; }
        .admin-content { margin-left: 260px; flex: 1; padding: 30px; min-height: 100vh; width: calc(100% - 260px); }
        .admin-content h1 { color: #fff; font-family: var(--font-primary); margin-bottom: 20px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .btn-add { padding: 10px 25px; border: 2px solid #fff; background: transparent; color: #fff; border-radius: 50px; cursor: pointer; transition: all 0.3s; }
        .btn-add:hover { background: #fff; color: #0a0a1a; }
        .table-container { background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: #aaa; font-weight: 400; font-size: 13px; letter-spacing: 1px; }
        td a { color: #ffc107; text-decoration: none; margin-right: 10px; }
        td a.delete { color: #ff4444; }
        
        /* Stock Cell */
        .stock-cell { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .stock-cell .stock-number { font-size: 16px; font-weight: 600; color: #fff; min-width: 30px; }
        .btn-add-stock { 
            padding: 4px 14px; 
            border: 1px solid rgba(40,167,69,0.4); 
            border-radius: 50px; 
            background: transparent; 
            color: #28a745; 
            cursor: pointer; 
            transition: all 0.3s; 
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-add-stock:hover { 
            background: rgba(40,167,69,0.15); 
            border-color: #28a745; 
        }
        .btn-add-stock i { margin-right: 3px; }
        
        .badge { padding: 4px 10px; border-radius: 50px; font-size: 12px; }
        .badge-low { background: rgba(255,193,7,0.15); color: #ffc107; }
        .badge-out { background: rgba(255,68,68,0.15); color: #ff4444; }
        .badge-good { background: rgba(40,167,69,0.15); color: #28a745; }
        .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        
        /* Add Stock Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); align-items: center; justify-content: center; z-index: 9999; }
        .modal-overlay .modal-box { background: #0a0a1a; padding: 30px; border-radius: 12px; max-width: 400px; width: 90%; border: 1px solid rgba(255,255,255,0.05); }
        .modal-overlay .modal-box h2 { color: #fff; margin-bottom: 10px; font-family: var(--font-primary); }
        .modal-overlay .modal-box .product-name { color: #888; margin-bottom: 20px; font-size: 14px; }
        .modal-overlay .modal-box .product-name strong { color: #fff; }
        .modal-overlay .modal-box input { width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(255,255,255,0.05); color: #fff; font-size: 16px; text-align: center; }
        .modal-overlay .modal-box input:focus { outline: none; border-color: #fff; }
        .modal-overlay .modal-box .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px; }
        .modal-overlay .modal-box .btn-confirm { padding: 10px 30px; border: 2px solid #28a745; background: transparent; color: #28a745; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-size: 14px; }
        .modal-overlay .modal-box .btn-confirm:hover { background: rgba(40,167,69,0.15); }
        .modal-overlay .modal-box .btn-cancel { padding: 10px 30px; border: none; background: transparent; color: #aaa; cursor: pointer; transition: all 0.3s; font-size: 14px; }
        .modal-overlay .modal-box .btn-cancel:hover { color: #fff; }
        
        /* Product Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); align-items: center; justify-content: center; z-index: 9999; }
        .modal-content { background: #0a0a1a; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%; border: 1px solid rgba(255,255,255,0.05); }
        .modal-content h2 { color: #fff; margin-bottom: 20px; font-family: var(--font-primary); }
        .modal-content input, .modal-content textarea { width: 100%; padding: 12px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(255,255,255,0.05); color: #fff; margin-bottom: 10px; }
        .modal-content input:focus, .modal-content textarea:focus { outline: none; border-color: #fff; }
        .modal-content .btn-save { padding: 10px 30px; border: 2px solid #fff; background: transparent; color: #fff; border-radius: 8px; cursor: pointer; }
        .modal-content .btn-save:hover { background: #fff; color: #0a0a1a; }
        .modal-content .btn-cancel { padding: 10px 30px; border: none; background: transparent; color: #aaa; cursor: pointer; margin-right: 10px; }
        .modal-actions { display: flex; justify-content: flex-end; margin-top: 10px; }
        .no-data { text-align: center; color: #666; padding: 30px; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; } .admin-content { margin-left: 200px; padding: 15px; width: calc(100% - 200px); } }
        @media (max-width: 480px) { .admin-sidebar { width: 60px; } .admin-sidebar ul li a span { display: none; } .admin-sidebar ul li a i { margin-right: 0; font-size: 18px; } .admin-content { margin-left: 60px; padding: 10px; width: calc(100% - 60px); } }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="logo">2THSND4</div>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span>
                    <?php if ($unreadMessages > 0): ?>
                        <span style="background:#ff4444;color:#fff;border-radius:50%;padding:2px 8px;font-size:11px;margin-left:5px;"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li class="logout-link"><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <h1>Products</h1>
            <div class="header-actions">
                <button class="btn-add" onclick="openModal()">+ Add Product</button>
            </div>
            
            <div class="table-container">
                <?php if (count($products) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img class="product-image" src="../images/<?php echo $product['image']; ?>">
                                    <?php else: ?>
                                        <span style="color:#666;">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <div class="stock-cell">
                                        <span class="stock-number"><?php echo $product['stock']; ?></span>
                                        <button class="btn-add-stock" onclick="openAddStockModal(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($product['stock'] <= 0): ?>
                                        <span class="badge badge-out">❌ Out of Stock</span>
                                    <?php elseif ($product['stock'] <= 3): ?>
                                        <span class="badge badge-low">⚠️ Low Stock (<?php echo $product['stock']; ?>)</span>
                                    <?php else: ?>
                                        <span class="badge badge-good">✅ In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>', '<?php echo $product['stock']; ?>', '<?php echo addslashes($product['image']); ?>', '<?php echo addslashes($product['description']); ?>')"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="delete" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-data">No products yet. Click "Add Product" to get started.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal-overlay" id="addStockModal">
        <div class="modal-box">
            <h2>➕ Add Stock</h2>
            <p class="product-name">Adding stock to: <strong id="addStockProductName">Product</strong></p>
            <input type="number" id="addStockQuantity" value="1" min="1">
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeAddStockModal()">Cancel</button>
                <button class="btn-confirm" onclick="confirmAddStock()">Add Stock</button>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <h2 id="modalTitle">Add Product</h2>
            <form method="POST">
                <input type="hidden" name="edit_id" id="editId">
                <input type="text" name="name" id="productName" placeholder="Product Name" required>
                <input type="number" name="price" id="productPrice" placeholder="Price" step="0.01" required>
                <input type="number" name="stock" id="productStock" placeholder="Stock" required>
                <input type="text" name="image" id="productImage" placeholder="Image filename (e.g., product.png)">
                <textarea name="description" id="productDescription" placeholder="Description" rows="3"></textarea>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variables for add stock
        let addStockProductId = 0;
        
        function openAddStockModal(productId, productName) {
            addStockProductId = productId;
            document.getElementById('addStockProductName').textContent = productName;
            document.getElementById('addStockQuantity').value = 1;
            document.getElementById('addStockModal').style.display = 'flex';
        }
        
        function closeAddStockModal() {
            document.getElementById('addStockModal').style.display = 'none';
        }
        
        function confirmAddStock() {
            const quantity = document.getElementById('addStockQuantity').value;
            if (quantity <= 0) {
                alert('Please enter a valid quantity.');
                return;
            }
            window.location.href = 'products.php?add_stock=1&product_id=' + addStockProductId + '&qty=' + quantity;
        }
        
        // Product Modal functions
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('editId').value = '';
            document.getElementById('productName').value = '';
            document.getElementById('productPrice').value = '';
            document.getElementById('productStock').value = '';
            document.getElementById('productImage').value = '';
            document.getElementById('productDescription').value = '';
            document.getElementById('productModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        function editProduct(id, name, price, stock, image, description) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('editId').value = id;
            document.getElementById('productName').value = name;
            document.getElementById('productPrice').value = price;
            document.getElementById('productStock').value = stock;
            document.getElementById('productImage').value = image || '';
            document.getElementById('productDescription').value = description || '';
            document.getElementById('productModal').style.display = 'flex';
        }
        
        // Close modals on background click
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        
        document.getElementById('addStockModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddStockModal();
        });
        
        // Enter key support for add stock
        document.getElementById('addStockQuantity').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                confirmAddStock();
            }
        });
    </script>

<!-- LOGOUT MODAL -->
<div class="logout-modal-overlay" id="logoutModal" style="display: none;">
    <div class="logout-modal">
        <div class="logout-modal-content">
            <p>Are you sure you want to log out?</p>
            <div class="logout-modal-actions">
                <button class="btn btn-secondary" onclick="closeLogoutModal()">Cancel</button>
                <a href="../login/logout.php" class="btn btn-primary">Yes, Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
function showLogoutModal(event) {
    event.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

// Close modal on background click
document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});
</script>

</body>
</html>