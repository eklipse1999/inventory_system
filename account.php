<?php
require_once 'includes/functions.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Determine which form was submitted
    if (isset($_POST['update_profile'])) {
        // Update profile information
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        
        // Validate inputs
        if (empty($username) || empty($email)) {
            $error = "Username and email are required fields";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address";
        } else {
            // Check if username or email already exists for other users
            $conn = connectDB();
            $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->bind_param("ssi", $username, $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Username or email already exists";
            } else {
                // Update user profile
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $email, $user_id);
                
                if ($stmt->execute()) {
                    $success = "Profile updated successfully";
                    $_SESSION['username'] = $username; // Update session
                    $user = getUserById($user_id); // Refresh user data
                } else {
                    $error = "Error updating profile: " . $conn->error;
                }
            }
            $conn->close();
        }
    } elseif (isset($_POST['change_password'])) {
        // Change password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required";
        } elseif ($new_password != $confirm_password) {
            $error = "New passwords do not match";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters long";
        } else {
            // Verify current password
            $conn = connectDB();
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            
            if (!password_verify($current_password, $user_data['password'])) {
                $error = "Current password is incorrect";
            } else {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $success = "Password changed successfully";
                } else {
                    $error = "Error changing password: " . $conn->error;
                }
            }
            $conn->close();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-user-cog mr-2"></i>Account Settings</h1>
        <p class="page-subtitle">Manage your account information</p>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <!-- Profile Information -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-user mr-2"></i>Profile Information</h6>
            </div>
            <div class="card-body">
                <form method="post" action="account.php">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user mr-2"></i>Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-2"></i>Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role"><i class="fas fa-user-tag mr-2"></i>Role</label>
                        <input type="text" class="form-control" id="role" value="<?php echo ucfirst($user['role']); ?>" readonly>
                        <small class="form-text text-muted">Your role cannot be changed. Contact an administrator for role changes.</small>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Change Password -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-key mr-2"></i>Change Password</h6>
            </div>
            <div class="card-body">
                <form method="post" action="account.php">
                    <div class="form-group">
                        <label for="current_password"><i class="fas fa-lock mr-2"></i>Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password"><i class="fas fa-lock mr-2"></i>New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock mr-2"></i>Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Account Information</h6>
            </div>
            <div class="card-body">
                <div class="account-info">
                    <p><i class="fas fa-calendar-alt mr-2"></i><strong>Created:</strong> <?php echo $user['created_at']; ?></p>
                    <p><i class="fas fa-id-card mr-2"></i><strong>Account ID:</strong> <?php echo $user['id']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: #858796;
        margin-bottom: 0;
    }
    
    .account-info p {
        margin-bottom: 1rem;
        color: #5a5c69;
    }
    
    .account-info i {
        color: var(--primary-color);
    }
</style>

<?php require_once 'includes/footer.php'; ?>

