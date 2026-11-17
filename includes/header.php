<?php 
// Safely start the session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// Define the base URL so CSS and Links work on EVERY page, regardless of folder depth
$base_url = '/career-link/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Link - Job Management System</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="<?php echo $base_url; ?>index.php" class="logo">Career Link</a>
    <div class="nav-links">
        <a href="<?php echo $base_url; ?>jobs/index.php">Find Jobs</a>
        <a href="<?php echo $base_url; ?>contact.php">Contact</a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_url; ?>admin/index.php" class="btn-primary">Dashboard</a>
            <?php elseif($_SESSION['role'] === 'employer'): ?>
                <a href="<?php echo $base_url; ?>employer/index.php" class="btn-primary">Dashboard</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>seeker/index.php" class="btn-primary">My Profile</a>
            <?php endif; ?>
            <a href="<?php echo $base_url; ?>auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>auth/login.php">Login</a>
            <a href="<?php echo $base_url; ?>auth/register_seeker.php" class="btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>