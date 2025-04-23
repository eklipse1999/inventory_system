<?php
require_once 'includes/functions.php';
requireLogin();

// Get dashboard data
$conn = connectDB();

// Total products
$sql = "SELECT COUNT(*) as total FROM products";
$result = $conn->query($sql);
$total_products = $result->fetch_assoc()['total'];

// Total stock value
$sql = "SELECT SUM(p.price * s.quantity) as total 
        FROM products p 
        JOIN stock s ON p.id = s.product_id";
$result = $conn->query($sql);
$total_stock_value = $result->fetch_assoc()['total'] ?? 0;

// Low stock products
$sql = "SELECT COUNT(*) as total 
        FROM products p 
        JOIN stock s ON p.id = s.product_id 
        WHERE s.quantity <= 10";
$result = $conn->query($sql);
$low_stock_count = $result->fetch_assoc()['total'];

// Recent sales
$sql = "SELECT s.id, p.name, s.quantity, s.total_price, s.sale_date 
        FROM sales s 
        JOIN products p ON s.product_id = p.id 
        ORDER BY s.sale_date DESC 
        LIMIT 5";
$result = $conn->query($sql);
$recent_sales = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recent_sales[] = $row;
    }
}

$conn->close();

require_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Dashboard</h1>
    <p class="dashboard-subtitle">Welcome back, <?php echo $_SESSION['username']; ?>! Here's an overview of your inventory.</p>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">TOTAL PRODUCTS</div>
                        <div class="stat-card-value"><?php echo $total_products; ?></div>
                        <div class="stat-card-text">Products in inventory</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">TOTAL STOCK VALUE</div>
                        <div class="stat-card-value">$<?php echo number_format($total_stock_value, 2); ?></div>
                        <div class="stat-card-text">Value of current inventory</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-danger h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">LOW STOCK ITEMS</div>
                        <div class="stat-card-value"><?php echo $low_stock_count; ?></div>
                        <div class="stat-card-text">Products with low stock</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i>Recent Sales</h6>
                <a href="sales.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-list fa-sm mr-1"></i> View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_sales)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent sales found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_sales as $sale): ?>
                                    <tr>
                                        <td><?php echo $sale['id']; ?></td>
                                        <td><?php echo $sale['name']; ?></td>
                                        <td><?php echo $sale['quantity']; ?></td>
                                        <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                                        <td><?php echo $sale['sale_date']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-header {
        margin-bottom: 1.5rem;
    }
    
    .dashboard-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .dashboard-subtitle {
        color: #858796;
        margin-bottom: 1.5rem;
    }
    
    .stat-card-text {
        font-size: 0.8rem;
        color: #858796;
        margin-top: 0.25rem;
    }
</style>

<?php require_once 'includes/footer.php'; ?>

