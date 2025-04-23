<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <title>Inventory Management System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
      :root {
          --primary-color: #4e73df;
          --secondary-color: #1cc88a;
          --accent-color: #f6c23e;
          --dark-color: #5a5c69;
          --light-color: #f8f9fc;
      }
      
      body {
          font-family: 'Poppins', sans-serif;
          background-color: #f8f9fc;
          padding-top: 0;
      }
      
      .navbar {
          background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
          padding: 1rem 1.5rem;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
          margin-bottom: 30px;
          border-radius: 0 0 15px 15px;
      }
      
      .navbar-brand {
          font-weight: 700;
          font-size: 1.5rem;
          color: white !important;
          display: flex;
          align-items: center;
          transition: all 0.3s ease;
      }
      
      .navbar-brand:hover {
          transform: translateY(-2px);
      }
      
      .navbar-brand i {
          margin-right: 10px;
          font-size: 1.8rem;
          color: var(--accent-color);
      }
      
      .navbar-dark .navbar-nav .nav-link {
          color: rgba(255, 255, 255, 0.8);
          font-weight: 500;
          padding: 0.5rem 1rem;
          border-radius: 5px;
          transition: all 0.3s ease;
      }
      
      .navbar-dark .navbar-nav .nav-link:hover {
          color: white;
          background-color: rgba(255, 255, 255, 0.1);
          transform: translateY(-2px);
      }
      
      .navbar-dark .navbar-nav .active > .nav-link {
          background-color: rgba(255, 255, 255, 0.2);
          color: white;
      }
      
      .user-welcome {
          background-color: rgba(255, 255, 255, 0.15);
          padding: 0.5rem 1rem;
          border-radius: 50px;
          display: flex;
          align-items: center;
          margin-right: 10px;
      }
      
      .user-welcome i {
          margin-right: 8px;
          color: var(--accent-color);
      }
      
      .logout-btn {
          background-color: rgba(255, 255, 255, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
          color: white !important;
          border-radius: 50px;
          padding: 0.5rem 1.2rem !important;
          transition: all 0.3s ease;
      }
      
      .logout-btn:hover {
          background-color: rgba(255, 255, 255, 0.2);
          transform: translateY(-2px);
      }
      
      .auth-btn {
          border-radius: 50px;
          padding: 0.5rem 1.2rem !important;
          margin-left: 8px;
          transition: all 0.3s ease;
      }
      
      .login-btn {
          background-color: var(--secondary-color);
          color: white !important;
      }
      
      .register-btn {
          background-color: var(--accent-color);
          color: var(--dark-color) !important;
          font-weight: 600;
      }
      
      .login-btn:hover, .register-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
      
      .nav-item {
          margin: 0 2px;
      }
      
      .nav-icon {
          margin-right: 8px;
      }
      
      .container {
          max-width: 1200px;
          padding: 0 20px;
      }
      
      .header-container {
          background-color: white;
          border-radius: 15px;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
          padding: 20px;
          margin-bottom: 30px;
      }
      
      /* Mobile Responsive Enhancements */
      @media (max-width: 992px) {
          .navbar-collapse {
              background-color: rgba(0, 0, 0, 0.1);
              border-radius: 10px;
              padding: 10px;
              margin-top: 15px;
          }
          
          .user-welcome {
              margin-bottom: 10px;
              width: 100%;
              justify-content: center;
          }
          
          .auth-btn, .logout-btn {
              display: block;
              margin: 5px 0;
              text-align: center;
              width: 100%;
          }
          
          .navbar-nav {
              width: 100%;
          }
          
          .nav-item {
              margin: 5px 0;
          }
          
          .container {
              padding: 0 15px;
          }
          
          .header-container {
              padding: 15px;
          }
          
          h1, .h1 {
              font-size: 1.8rem;
          }
          
          h2, .h2 {
              font-size: 1.5rem;
          }
          
          .btn {
              padding: 0.375rem 1rem;
          }
          
          .card-body {
              padding: 1rem;
          }
      }
      
      @media (max-width: 576px) {
          .navbar-brand {
              font-size: 0.85rem;
          }
          
          .navbar-brand i {
              font-size: 1.5rem;
          }
          
          h1, .h1 {
              font-size: 1.5rem;
          }
          
          h2, .h2 {
              font-size: 1.3rem;
          }
          
          .btn-group {
              display: flex;
              flex-direction: column;
          }
          
          .btn-group .btn {
              margin-bottom: 0.5rem;
              border-radius: 50px !important;
          }
      }
  </style>
</head>
<body>
  <div class="container-fluid px-0">
      <nav class="navbar navbar-expand-lg navbar-dark">
          <div class="container">
              <a class="navbar-brand" href="index.php">
                  <i class="fas fa-boxes"></i>
                  Inventory Management System
              </a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav mr-auto">
                      <?php if (isLoggedIn()): ?>
                          <li class="nav-item">
                              <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                                  <i class="fas fa-tachometer-alt nav-icon"></i>Dashboard
                              </a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                                  <i class="fas fa-box nav-icon"></i>Products
                              </a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>" href="sales.php">
                                  <i class="fas fa-shopping-cart nav-icon"></i>Sales
                              </a>
                          </li>
                          <?php if (hasRole('admin')): ?>
                              <li class="nav-item">
                                  <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                                      <i class="fas fa-users nav-icon"></i>Users
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                                      <i class="fas fa-chart-bar nav-icon"></i>Reports
                                  </a>
                              </li>
                          <?php endif; ?>
                      <?php endif; ?>
                  </ul>
                  <ul class="navbar-nav">
                      <?php if (isLoggedIn()): ?>
                          <li class="nav-item">
                              <a class="nav-link user-welcome" href="account.php">
                                  <i class="fas fa-user-circle"></i>
                                  Welcome, <?php echo $_SESSION['username']; ?> 
                                  <span class="badge badge-light ml-2"><?php echo ucfirst($_SESSION['role']); ?></span>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link logout-btn" href="logout.php">
                                  <i class="fas fa-sign-out-alt"></i> Logout
                              </a>
                          </li>
                      <?php else: ?>
                          <li class="nav-item">
                              <a class="nav-link auth-btn login-btn" href="login.php">
                                  <i class="fas fa-sign-in-alt"></i> Login
                              </a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link auth-btn register-btn" href="register.php">
                                  <i class="fas fa-user-plus"></i> Register
                              </a>
                          </li>
                      <?php endif; ?>
                  </ul>
              </div>
          </div>
      </nav>
  </div>
  
  <div class="container">
      <div class="header-container">

