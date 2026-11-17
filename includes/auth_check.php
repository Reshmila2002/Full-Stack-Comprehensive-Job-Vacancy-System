<?php
// includes/auth_check.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Helper function to restrict page access by role
function requireRole($required_role) {
    // Admins get access to everything, otherwise check exact role match
    if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== $required_role) {
        // Redirect unauthorized users to the homepage
        header("Location: ../index.php");
        exit();
    }
}
?>