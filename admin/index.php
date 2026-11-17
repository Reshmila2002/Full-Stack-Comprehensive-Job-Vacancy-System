<?php
// admin/index.php

// 1. Include the database connection and the authentication check file
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';

// 2. Security measure: Ensure only users with 'admin' role can view this page
requireRole('admin');

// 3. Initialize an empty array to store system statistics
$stats = [];

// --- STAT 1: Total number of registered users ---
$stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// --- STAT 2: Jobs awaiting approval (Pending) ---
$stats['pending_jobs'] = $pdo->query("SELECT COUNT(*) FROM jobs WHERE status = 'pending'")->fetchColumn();

// --- STAT 3: Active / Approved jobs ---
$stats['approved_jobs'] = $pdo->query("SELECT COUNT(*) FROM jobs WHERE status = 'approved'")->fetchColumn();

// 4. Include the global website header (Navigation Bar)
include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%; max-width: 1200px; margin: auto; min-height: 75vh;">
    
    <h2 style="color: #1e293b; font-size: 2.4rem; margin-bottom: 0.5rem;">Admin Dashboard</h2>
    <p style="color: #64748b; font-size: 1.1rem;">Welcome to the Career Link central control system.</p>
    
    <div style="display: flex; gap: 2rem; margin-top: 3rem; flex-wrap: wrap;">
        
        <div style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow); flex: 1; min-width: 250px; display: flex; flex-direction: column;">
            <h3 style="color: #334155; margin-bottom: 0;">Total Users</h3>
            <p style="font-size: 3rem; color: var(--primary-color); font-weight: 800; margin: 1rem 0;"><?php echo $stats['total_users']; ?></p>
            
            <a href="manage_users.php" class="btn-primary" style="text-decoration: none; display: inline-block; width: 100%; text-align: center; margin-top: auto; padding: 12px;">Manage Users</a>
        </div>
        
        <div style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow); flex: 1; min-width: 250px; display: flex; flex-direction: column;">
            <h3 style="color: #334155; margin-bottom: 0;">Pending Jobs</h3>
            <p style="font-size: 3rem; color: #f59e0b; font-weight: 800; margin: 1rem 0;"><?php echo $stats['pending_jobs']; ?></p>
            
            <a href="approve_jobs.php" class="btn-primary" style="text-decoration: none; display: inline-block; width: 100%; text-align: center; margin-top: auto; padding: 12px; background-color: #f59e0b;">Review Jobs</a>
        </div>
        
        <div style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow); flex: 1; min-width: 250px; display: flex; flex-direction: column;">
            <h3 style="color: #334155; margin-bottom: 0;">Active Jobs</h3>
            <p style="font-size: 3rem; color: #10b981; font-weight: 800; margin: 1rem 0;"><?php echo $stats['approved_jobs']; ?></p>
            
            <a href="reports.php" class="btn-primary" style="text-decoration: none; display: inline-block; width: 100%; text-align: center; margin-top: auto; padding: 12px; background-color: #10b981;">View Reports</a>
        </div>
        
    </div>
</main>

<?php include '../includes/footer.php'; ?>