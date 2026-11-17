<?php
// employer/index.php

require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';

// Ensure the user has the 'employer' role
requireRole('employer');

// 1. Validate session integrity
if (!isset($_SESSION['user_id']) || !filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT)) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$employer_id = (int) $_SESSION['user_id'];

// 2. Fetch jobs using a safe prepared statement
try {
    $stmt = $pdo->prepare("
        SELECT id, title, location, job_type, status, created_at
        FROM jobs
        WHERE employer_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$employer_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $jobs = [];
}

include '../includes/header.php';
?>

<style>
    /* ... (Keep your existing CSS as it is well-structured) ... */
    .dashboard-wrapper { padding: 4rem 5%; max-width: 1200px; margin: auto; min-height: 75vh; background-color: #f8fafc; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1rem; }
    .dashboard-header h1 { font-size: 2.8rem; color: #1e293b; font-weight: 800; margin: 0; }
    .header-actions { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
    .btn-outline { background: transparent; color: var(--primary-color, #007bff); border: 2px solid var(--primary-color, #007bff); padding: 12px 24px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-decoration: none; transition: all 0.3s ease; }
    .btn-outline:hover { background: var(--primary-color, #007bff); color: white; }
    .btn-primary { background: var(--primary-color, #007bff); color: white; padding: 14px 28px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2); transition: all 0.3s ease; border: none; }
    .btn-primary:hover { background: #0056b3; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0, 86, 179, 0.3); }
    .section-title { font-size: 1.5rem; color: #334155; margin-bottom: 1.5rem; font-weight: 700; }
    .table-card { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow-x: auto; }
    .modern-table { width: 100%; border-collapse: collapse; text-align: left; }
    .modern-table th { background-color: #ffffff; padding: 20px 30px; color: #64748b; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #f1f5f9; font-weight: 800; }
    .modern-table td { padding: 24px 30px; border-bottom: 1px solid #f1f5f9; color: #475569; vertical-align: middle; font-size: 1.05rem; }
    .modern-table tr:last-child td { border-bottom: none; }
    .modern-table tbody tr:hover { background-color: #f8fafc; }
    .job-title { font-weight: 800; color: #1e293b; }
    .badge { padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-pending { background-color: #fef9c3; color: #ca8a04; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .action-link { color: var(--primary-color, #007bff); font-weight: 700; text-decoration: none; transition: color 0.3s; }
    .action-link:hover { color: #0056b3; text-decoration: underline; }
    .empty-state { padding: 4rem 2rem; text-align: center; color: #64748b; }
</style>

<main class="dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Employer', ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <div class="header-actions">
            <a href="manage_profile.php" class="btn-outline">Manage Profile</a>
            <a href="post_job.php" class="btn-primary">+ Post a New Job</a>
        </div>
    </div>

    <h3 class="section-title">Your Job Postings</h3>

    <div class="table-card">
        <?php if (!empty($jobs)): ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): 
                        // Row-level validation
                        $job_id = filter_var($job['id'] ?? 0, FILTER_VALIDATE_INT);
                        $status = strtolower(trim($job['status'] ?? 'pending'));
                        $date_raw = $job['created_at'] ?? '';
                        $formatted_date = ($date_raw && strtotime($date_raw)) ? date('M d, Y', strtotime($date_raw)) : 'N/A';
                    ?>
                        <tr>
                            <td class="job-title"><?php echo htmlspecialchars($job['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($job['location'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($job['job_type'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span class="badge <?php echo ($status === 'approved') ? 'badge-approved' : 'badge-pending'; ?>">
                                    <?php echo ($status === 'approved') ? 'Approved' : 'Pending'; ?>
                                </span>
                            </td>
                            <td style="color: #64748b;"><?php echo $formatted_date; ?></td>
                            <td>
                                <?php if ($job_id): ?>
                                    <a href="view_applicants.php?job_id=<?php echo $job_id; ?>" class="action-link">View Applicants &rarr;</a>
                                <?php else: ?>
                                    <span style="color:red;">Invalid Job</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p style="font-size:1.2rem; margin-bottom:0.5rem;">You haven't posted any jobs yet.</p>
                <p>Click the "Post a New Job" button above to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>