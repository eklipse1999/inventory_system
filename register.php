<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $conn = connectDB();
        
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if username or email already exists
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            // Default role is 'user'
            $sql = "INSERT INTO users (username, email, password, role) 
                    VALUES ('$username', '$email', '$hashed_password', 'user')";
            
            if ($conn->query($sql) === TRUE) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
        
        $conn->close();
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card auth-card">
            <div class="card-header">
                <h4 class="auth-title"><i class="fas fa-user-plus"></i> Register</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="auth-form">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user mr-2"></i>Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-2"></i>Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-2"></i>Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock mr-2"></i>Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Already have an account? <a href="login.php" class="auth-link">Login</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-card {
        margin-top: 2rem;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .auth-title {
        margin-bottom: 0;
        color: #5a5c69;
        font-weight: 700;
    }
    
    .auth-form {
        padding: 1rem 0;
    }
    
    .auth-link {
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .auth-link:hover {
        text-decoration: none;
        color: #2e59d9;
    }
</style>

<?php require_once 'includes/footer.php'; ?>

