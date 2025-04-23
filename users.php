<?php
require_once 'includes/functions.php';
requireLogin();
requireRole('admin');

$success = '';
$error = '';

// Process user role update
if (isset($_POST['update_role'])) {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    
    if ($user_id <= 0) {
        $error = "Invalid user ID. Please try again.";
    } elseif (empty($role)) {
        $error = "Role selection is required.";
    } else {
        if (updateUserRole($user_id, $role)) {
            $success = "User role updated successfully";
        } else {
            $error = "Error updating user role. Please try again.";
        }
    }
}

// Process user deletion
if (isset($_POST['delete_user'])) {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    // Prevent admin from deleting their own account
    if ($user_id == $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } elseif ($user_id <= 0) {
        $error = "Invalid user ID. Please try again.";
    } else {
        if (deleteUser($user_id)) {
            $success = "User deleted successfully";
        } else {
            $error = "Error deleting user. Please try again.";
        }
    }
}

// Get all users
$users = getAllUsers();

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-users mr-2"></i>User Management</h1>
        <p class="page-subtitle">Manage system users and their roles</p>
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

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-list mr-2"></i>User List</h6>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No users found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $user['role'] == 'admin' ? 'primary' : 
                                            ($user['role'] == 'manager' ? 'success' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <!-- Update the d-flex to make buttons appear better side by side -->
                                    <div class="d-flex flex-column flex-md-row">
                                        <!-- Role update form -->
                                        <form method="post" class="role-update-form mb-2 mb-md-0 mr-md-2">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <div class="input-group input-group-sm">
                                                <select name="role" class="form-control form-control-sm">
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="manager" <?php echo $user['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="submit" name="update_role" class="btn btn-sm btn-warning d-flex align-items-center">
                                                        <i class="fas fa-save mr-1"></i> Update
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        
                                        <!-- Delete user button -->
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="post" class="delete-user-form" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <div class="input-group input-group-sm">
                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger btn-block d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-trash mr-2"></i> Delete
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
    
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    /* Add this CSS to the style section to ensure both buttons have the same styling */
   

    .delete-user-form .btn {
        width: 100%;
        height: 31px; 
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-group-sm .btn {
        display: flex;
        align-items: center;
    }

    .role-update-form .input-group {
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .table td, .table th {
            vertical-align: middle;
        }
        
        .input-group {
            max-width: 200px;
            margin: 0 auto;
        }
        
        .delete-user-form {
            margin-top: 0.5rem;
            text-align: center;
        }

        .role-update-form,
        .delete-user-form {
            width: 100%;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>

