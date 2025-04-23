<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="row align-items-center">
    <div class="col-lg-6">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to <span class="text-primary">Group 5 Inventory Management system</span></h1>
            <p class="hero-subtitle">A powerful inventory management system to streamline your business operations</p>
            <p class="hero-text">Track products, manage stock levels, record sales, and generate insightful reports with our comprehensive solution.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary btn-lg mr-3">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <a href="register.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <img src="https://cdn.pixabay.com/photo/2018/03/10/09/45/businessman-3213659_1280.jpg" class="img-fluid hero-image" alt="Inventory Management">
    </div>
</div>

<div class="row mt-5">
    <div class="col-12 text-center">
        <h2 class="section-title">Key Features</h2>
        <p class="section-subtitle">Everything you need to manage your inventory efficiently</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card feature-card">
            <div class="card-body text-center">
                <div class="feature-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h5 class="feature-title">Product Management</h5>
                <p class="feature-text">Add, edit, and remove products with ease. Keep track of all your inventory in one place.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card feature-card">
            <div class="card-body text-center">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h5 class="feature-title">Sales Tracking</h5>
                <p class="feature-text">Record sales and automatically update inventory levels. Monitor your business performance.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card feature-card">
            <div class="card-body text-center">
                <div class="feature-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h5 class="feature-title">Detailed Reports</h5>
                <p class="feature-text">Generate comprehensive reports on sales, inventory levels, and low-stock products.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .hero-content {
        padding: 2rem 0;
    }
    
    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #5a5c69;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        color: #858796;
    }
    
    .hero-text {
        font-size: 1rem;
        margin-bottom: 2rem;
        color: #858796;
    }
    
    .hero-image {
        border-radius: 15px;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .section-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .section-subtitle {
        color: #858796;
        margin-bottom: 2rem;
    }
    
    .feature-card {
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
    }
    
    .feature-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
    }
    
    .feature-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #5a5c69;
    }
    
    .feature-text {
        color: #858796;
    }
</style>

<?php require_once 'includes/footer.php'; ?>

