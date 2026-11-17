<?php
// admin/approve_jobs.php

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
$error_message = '';

// --- Handle Approve/Reject button submissions (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // FORM VALIDATION 1: Verify CSRF token for security
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Security token mismatch. Action denied.";
    } 
    // FORM VALIDATION 2: Check if job ID and action are provided
    else if (!isset($_POST['job_id']) || empty($_POST['job_id']) || !isset($_POST['action'])) {
        $error_message = "Invalid form submission data.";
    } 
    else {
        // Sanitize and validate Job ID (ensure it's an integer)
        $job_id = filter_var($_POST['job_id'], FILTER_VALIDATE_INT);
        $action = $_POST['action'];

        // FORM VALIDATION 3: Verify the action is either 'approve' or 'reject'
        if ($job_id === false || !in_array($action, ['approve', 'reject'])) {
            $error_message = "Invalid parameters provided.";
        } else {
            // Determine the status update
            $new_status = ($action === 'approve') ? 'approved' : 'rejected';
            
            // Use Prepared Statements to securely update the job status
            $stmt = $pdo->prepare("UPDATE jobs SET status = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $job_id])) {
                $message = "Job ID $job_id has been successfully $new_status.";
            } else {
                $error_message = "Database update failed. Please try again.";
            }
        }
    }
}

// --- FETCH PENDING JOBS ---
// Get all jobs with status 'pending' joined with employer names
$stmt = $pdo->query("
    SELECT jobs.*, users.name AS employer_name 
    FROM jobs 
    JOIN users ON jobs.employer_id = users.id 
    WHERE jobs.status = 'pending' 
    ORDER BY created_at ASC
");
$pending_jobs = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%;">
    <h2>Pending Job Approvals</h2>
    
    <?php if($message): ?>
        <div style="color: green; margin-bottom: 1rem; font-weight: bold; background: #e6f4ea; padding: 10px; border-radius: 4px; border-left: 5px solid green;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div style="color: red; margin-bottom: 1rem; font-weight: bold; background: #fce8e6; padding: 10px; border-radius: 4px; border-left: 5px solid red;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if(count($pending_jobs) > 0): ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: var(--white); box-shadow: var(--box-shadow);">
                <tr style="background-color: #333; color: var(--white); text-align: left;">
                    <th style="padding: 12px;">Employer</th>
                    <th style="padding: 12px;">Job Title</th>
                    <th style="padding: 12px;">Category</th>
                    <th style="padding: 12px;">Date Posted</th>
                    <th style="padding: 12px;">Action</th>
                </tr>
                
                <?php foreach($pending_jobs as $job): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php echo htmlspecialchars($job['employer_name']); ?></td>
                        <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($job['title']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($job['category']); ?></td>
                        <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                        
                        <td style="padding: 12px;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                
                                <button type="submit" name="action" value="approve" style="background: green; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: bold;">Approve</button>
                                <button type="submit" name="action" value="reject" style="background: red; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: bold;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No jobs are currently awaiting approval.</p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>