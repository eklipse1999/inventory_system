<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['role'] == $role || 
           ($_SESSION['role'] == 'admin' && ($role == 'manager' || $role == 'user')) ||
           ($_SESSION['role'] == 'manager' && $role == 'user');
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Redirect if not authorized
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        header("Location: unauthorized.php");
        exit;
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Get user by ID
function getUserById($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $conn->close();
        return $user;
    }
    
    $conn->close();
    return null;
}

// Get all products
function getAllProducts($search = '') {
    $conn = connectDB();
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            LEFT JOIN stock s ON p.id = s.product_id";
    
     // Add soft delete filter
     $sql .= " WHERE p.is_deleted = 0";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get product by ID
function getProductById($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            LEFT JOIN stock s ON p.id = s.product_id 
            WHERE p.id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $conn->close();
        return $product;
    }
    
    $conn->close();
    return null;
}

// Add product
function addProduct($name, $description, $sku, $price, $quantity) {
    $conn = connectDB();
    
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $sku = $conn->real_escape_string($sku);
    $price = $conn->real_escape_string($price);
    $quantity = $conn->real_escape_string($quantity);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert product
        $sql = "INSERT INTO products (name, description, sku, price) 
                VALUES ('$name', '$description', '$sku', '$price')";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error adding product: " . $conn->error);
        }
        
        $product_id = $conn->insert_id;
        
        // Insert stock
        $sql = "INSERT INTO stock (product_id, quantity) 
                VALUES ('$product_id', '$quantity')";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error adding stock: " . $conn->error);
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        
        echo $e->getMessage();
        return false;
    }
}

// Update product
function updateProduct($id, $name, $description, $sku, $price, $quantity) {
    $conn = connectDB();
    
    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $sku = $conn->real_escape_string($sku);
    $price = $conn->real_escape_string($price);
    $quantity = $conn->real_escape_string($quantity);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update product
        $sql = "UPDATE products 
                SET name = '$name', description = '$description', 
                    sku = '$sku', price = '$price' 
                WHERE id = '$id'";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error updating product: " . $conn->error);
        }
        
        // Update stock
        $sql = "UPDATE stock SET quantity = '$quantity' WHERE product_id = '$id'";
        $result = $conn->query($sql);
        
        if ($conn->affected_rows == 0) {
            // Insert stock if not exists
            $sql = "INSERT INTO stock (product_id, quantity) 
                    VALUES ('$id', '$quantity')";
            
            if ($conn->query($sql) === FALSE) {
                throw new Exception("Error updating stock: " . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        
        echo $e->getMessage();
        return false;
    }
}

// Delete product
function deleteProduct($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);

    $sql = "UPDATE products SET is_deleted = 1 WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return true;
    }

    $conn->close();
    return false;
}


// Record sale
function recordSale($product_id, $quantity, $user_id) {
    $conn = connectDB();
    
    $product_id = $conn->real_escape_string($product_id);
    $quantity = $conn->real_escape_string($quantity);
    $user_id = $conn->real_escape_string($user_id);
    
    // Get product price
    $sql = "SELECT price FROM products WHERE id = '$product_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        $conn->close();
        return false;
    }
    
    $product = $result->fetch_assoc();
    $total_price = $product['price'] * $quantity;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Record sale
        $sql = "INSERT INTO sales (product_id, quantity, total_price, user_id) 
                VALUES ('$product_id', '$quantity', '$total_price', '$user_id')";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error recording sale: " . $conn->error);
        }
        
        // Update stock
        $sql = "UPDATE stock 
                SET quantity = quantity - '$quantity' 
                WHERE product_id = '$product_id' AND quantity >= '$quantity'";
        
        if ($conn->query($sql) === FALSE || $conn->affected_rows == 0) {
            throw new Exception("Error updating stock or insufficient quantity");
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        
        echo $e->getMessage();
        return false;
    }
}

// Get low stock products
function getLowStockProducts($threshold = 10) {
    $conn = connectDB();
    $threshold = $conn->real_escape_string($threshold);
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            JOIN stock s ON p.id = s.product_id 
            WHERE s.quantity <= '$threshold'";
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get sales report
function getSalesReport($start_date = null, $end_date = null) {
    $conn = connectDB();
    
    $sql = "SELECT s.id, p.name, s.quantity, s.total_price, s.sale_date, u.username 
            FROM sales s 
            JOIN products p ON s.product_id = p.id 
            JOIN users u ON s.user_id = u.id";
    
    if ($start_date && $end_date) {
        $start_date = $conn->real_escape_string($start_date);
        $end_date = $conn->real_escape_string($end_date);
        $sql .= " WHERE s.sale_date BETWEEN '$start_date' AND '$end_date'";
    }
    
    $sql .= " ORDER BY s.sale_date DESC";
    
    $result = $conn->query($sql);
    
    $sales = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sales[] = $row;
        }
    }
    
    $conn->close();
    return $sales;
}

// Get all users
function getAllUsers() {
    $conn = connectDB();
    
    $sql = "SELECT id, username, email, role, created_at FROM users";
    $result = $conn->query($sql);
    
    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    $conn->close();
    return $users;
}

// Update user role
function updateUserRole($user_id, $role) {
    $conn = connectDB();
    
    // Validate inputs
    $user_id = (int)$user_id;
    if ($user_id <= 0) {
        return false;
    }
    
    // Validate role
    $valid_roles = ['admin', 'manager', 'user'];
    if (!in_array($role, $valid_roles)) {
        return false;
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $user_id);
    
    $result = $stmt->execute();
    $affected = $stmt->affected_rows;
    
    $stmt->close();
    $conn->close();
    
    return ($result && $affected > 0);
}

// Delete user
function deleteUser($user_id) {
    $conn = connectDB();
    
    // Validate input
    $user_id = (int)$user_id;
    if ($user_id <= 0) {
        return false;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First check if this user has any sales records
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM sales WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        // If user has sales records, update them to admin user (ID 1) instead of deleting
        if ($row['count'] > 0) {
            $admin_id = 1; // Assuming admin has ID 1
            $stmt = $conn->prepare("UPDATE sales SET user_id = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $admin_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Now delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        if (!$result || $affected == 0) {
            throw new Exception("Failed to delete user");
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}
