<?php
require_once 'includes/functions.php';
requireLogin();
requireRole('manager');

$id = isset($_GET['id']) ? sanitize($_GET['id']) : null;
$product = null;
$success = '';
$error = '';

// Get product if editing
if ($id) {
    $product = getProductById($id);
    if (!$product) {
        header("Location: products.php");
        exit;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $sku = sanitize($_POST['sku']);
    $price = sanitize($_POST['price']);
    $quantity = sanitize($_POST['quantity']);
    
    if (empty($name) || empty($sku) || empty($price) || !isset($quantity)) {
        $error = "Please fill in all required fields";
    } else {
        if ($id) {
            // Update product
            if (updateProduct($id, $name, $description, $sku, $price, $quantity)) {
                $success = "Product updated successfully";
                $product = getProductById($id); // Refresh product data
            } else {
                $error = "Error updating product";
            }
        } else {
            // Add new product
            if (addProduct($name, $description, $sku, $price, $quantity)) {
                $success = "Product added successfully";
                // Clear form
                $name = $description = $sku = $price = $quantity = '';
            } else {
                $error = "Error adding product";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $id ? 'Edit' : 'Add'; ?> Product</h1>
    <a href="products.php" class="btn btn-secondary">Back to Products</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product ? $product['name'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $product ? $product['description'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="sku">SKU *</label>
                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo $product ? $product['sku'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price *</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $product ? $product['price'] : ''; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="<?php echo $product ? $product['quantity'] : '0'; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Add'; ?> Product</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

