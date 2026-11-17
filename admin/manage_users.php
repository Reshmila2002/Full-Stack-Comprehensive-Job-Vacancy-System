<?php
// admin/manage_users.php

// 1. Include database connection and authentication check files
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';

// 2. Security measure: Ensure only users with 'admin' role can access this page
requireRole('admin');

// 3. Start the session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Generate a CSRF token for security if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error_message = ''; // Variable to display validation errors

// --- Handle User Deletion (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // FORM VALIDATION 1: Verify CSRF token for security
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Security token mismatch. Action denied.";
    } 
    // FORM VALIDATION 2: Check if user ID is provided and not empty
    else if (!isset($_POST['delete_user_id']) || empty($_POST['delete_user_id'])) {
        $error_message = "Invalid user deletion data.";
    } 
    else {
        // Sanitize and validate User ID (ensure it's an integer)
        $delete_id = filter_var($_POST['delete_user_id'], FILTER_VALIDATE_INT);
        
        if ($delete_id === false) {
            $error_message = "Invalid user identification format.";
        } 
        // Security measure: Prevent admin from accidentally deleting their own account
        else if ($delete_id == $_SESSION['user_id']) {
            $error_message = "You cannot delete your own admin account.";
        } 
        else {
            // Delete the user from the database using Prepared Statements
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $message = "User account deleted successfully.";
            } else {
                $error_message = "Database action failed. Please try again.";
            }
        }
    }
}

// --- FETCH USERS ---
// Get all users ordered by registration date (newest first)
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>User Management</h2>
        <a href="index.php" style="color: var(--text-light); text-decoration: none;">&larr; Back to Dashboard</a>
    </div>

    <?php if($message): ?>
        <div style="background: #e6f4ea; color: green; padding: 10px; margin: 1rem 0; border: 1px solid #b7e1cd; border-radius: 4px; border-left: 5px solid green; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div style="background: #fce8e6; color: red; padding: 10px; margin: 1rem 0; border: 1px solid #fad2cf; border-radius: 4px; border-left: 5px solid red; font-weight: bold;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem; background: var(--white); box-shadow: var(--box-shadow);">
        <tr style="background-color: #333; color: var(--white); text-align: left;">
            <th style="padding: 12px;">ID</th>
            <th style="padding: 12px;">Name</th>
            <th style="padding: 12px;">Email</th>
            <th style="padding: 12px;">Role</th>
            <th style="padding: 12px;">Registered On</th>
            <th style="padding: 12px;">Actions</th>
        </tr>
        
        <?php foreach($users as $user): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 12px;"><?php echo $user['id']; ?></td>
                <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($user['name']); ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
                <td style="padding: 12px; text-transform: capitalize;"><?php echo htmlspecialchars($user['role']); ?></td>
                <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                
                <td style="padding: 12px;">
                    <?php if($user['role'] !== 'admin'): ?>
                        <form method="POST" onsubmit="return confirm('Delete this user? All their posted jobs/applications will also be deleted.');">
                            
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                            
                            <button type="submit" style="background: red; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer;">Remove</button>
                        </form>
                    <?php else: ?>
                        <span style="color: gray; font-size: 0.9rem;">Admin</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include '../includes/footer.php'; ?>