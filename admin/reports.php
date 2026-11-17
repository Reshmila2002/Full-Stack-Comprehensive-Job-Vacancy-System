<?php
// admin/reports.php

// 1. Include the database connection and the authentication check file
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';

// 2. Security measure: Ensure only users with 'admin' role can view this page
requireRole('admin');

// --- QUERY 1: Jobs by Category ---
// Count jobs in each category and sort by the highest number of jobs
$cat_stmt = $pdo->query("
    SELECT category, COUNT(*) as total 
    FROM jobs 
    GROUP BY category 
    ORDER BY total DESC
");
$categories = $cat_stmt->fetchAll();

// --- QUERY 2: Most Applied-To Jobs ---
// Join jobs, users, and applications tables to count applications per job
$app_stmt = $pdo->query("
    SELECT jobs.title, users.name as employer, COUNT(applications.id) as app_count
    FROM jobs
    JOIN users ON jobs.employer_id = users.id
    LEFT JOIN applications ON jobs.id = applications.job_id
    GROUP BY jobs.id
    ORDER BY app_count DESC
    LIMIT 10
");
$popular_jobs = $app_stmt->fetchAll();

// 3. Include the global website header
include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>System Reports</h2>
        <a href="index.php" style="color: var(--text-light); text-decoration: none;">&larr; Back to Dashboard</a>
    </div>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px; background: var(--white); padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
            <h3>Jobs by Category</h3>
            <hr style="margin: 1rem 0; border: 0; border-top: 1px solid #eee;">
            
            <ul style="list-style: none;">
                <?php foreach($categories as $cat): ?>
                    <li style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; border-bottom: 1px dashed #ccc; padding-bottom: 0.5rem;">
                        <strong><?php echo htmlspecialchars($cat['category']); ?></strong>
                        <span><?php echo $cat['total']; ?> jobs</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div style="flex: 1; min-width: 400px; background: var(--white); padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
            <h3>Most Popular Postings (Applications)</h3>
            <hr style="margin: 1rem 0; border: 0; border-top: 1px solid #eee;">
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="text-align: left; color: var(--text-light); border-bottom: 2px solid #eee;">
                    <th style="padding-bottom: 8px;">Job Title</th>
                    <th style="padding-bottom: 8px;">Employer</th>
                    <th style="padding-bottom: 8px;">Applications</th>
                </tr>
                
                <?php foreach($popular_jobs as $job): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 0;"><?php echo htmlspecialchars($job['title']); ?></td>
                        <td style="padding: 8px 0; color: var(--text-light); font-size: 0.9rem;"><?php echo htmlspecialchars($job['employer']); ?></td>
                        <td style="padding: 8px 0; font-weight: bold; color: var(--primary-color); text-align: center;"><?php echo $job['app_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
    </div>
</main>

<?php include '../includes/footer.php'; ?>