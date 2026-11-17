<?php
// employer/manage_jobs.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('employer');

// Ensure session is started for CSRF token handling (if not already handled in auth_check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$employer_id = $_SESSION['user_id'];
$message = '';
$is_error = false;

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Job Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job_id'])) {
    
    // 1. CSRF Validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Security token validation failed. Action blocked.";
        $is_error = true;
    } else {
        // 2. Input Validation (Ensure it's a valid integer)
        $delete_id = filter_input(INPUT_POST, 'delete_job_id', FILTER_VALIDATE_INT);
        
        if ($delete_id === false || $delete_id === null) {
            $message = "Invalid job ID provided.";
            $is_error = true;
        } else {
            // 3. Authorization Check & Execution (Row ownership validated in the query)
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND employer_id = ?");
            
            if ($stmt->execute([$delete_id, $employer_id])) {
                // 4. Verification (Ensure a row was actually deleted)
                if ($stmt->rowCount() > 0) {
                    $message = "Job listing deleted successfully.";
                } else {
                    $message = "Job not found or you do not have permission to delete it.";
                    $is_error = true;
                }
            } else {
                $message = "A database error occurred while trying to delete the job.";
                $is_error = true;
            }
        }
    }
}

// Fetch all jobs for this employer
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$employer_id]);
$jobs = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Manage Your Job Listings</h2>
        <a href="index.php" style="color: var(--text-light); text-decoration: none;">&larr; Back to Dashboard</a>
    </div>

    <?php if($message): ?>
        <div style="background: <?php echo $is_error ? '#ffe6e6' : '#e6ffe6'; ?>; 
                    color: <?php echo $is_error ? 'red' : 'green'; ?>; 
                    padding: 10px; 
                    margin: 1rem 0; 
                    border: 1px solid <?php echo $is_error ? 'red' : 'green'; ?>; 
                    border-radius: 4px;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if(count($jobs) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem; background: var(--white); box-shadow: var(--box-shadow);">
            <tr style="background-color: var(--primary-color); color: var(--white); text-align: left;">
                <th style="padding: 12px;">Job Title</th>
                <th style="padding: 12px;">Date Posted</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
            <?php foreach($jobs as $job): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($job['title']); ?></td>
                    <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                    <td style="padding: 12px;"><?php echo ucfirst(htmlspecialchars($job['status'])); ?></td>
                    <td style="padding: 12px;">
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this job? This cannot be undone.');">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="delete_job_id" value="<?php echo htmlspecialchars($job['id']); ?>">
                            <button type="submit" style="background: red; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="margin-top: 2rem; color: var(--text-light);">You have no active job listings to manage.</p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>