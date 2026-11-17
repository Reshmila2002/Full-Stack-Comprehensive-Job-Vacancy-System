<?php
// employer/view_applicants.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('employer');

if (!isset($_GET['job_id'])) {
    header("Location: index.php");
    exit();
}

$job_id = $_GET['job_id'];
$employer_id = $_SESSION['user_id'];
$message = '';
$allowed_statuses = ['applied', 'reviewed', 'hired', 'rejected'];

// Security Check: Verify this job actually belongs to the logged-in employer
$check_job = $pdo->prepare("SELECT title FROM jobs WHERE id = ? AND employer_id = ?");
$check_job->execute([$job_id, $employer_id]);
$job = $check_job->fetch();

if (!$job) {
    die("Unauthorized access or job not found.");
}

// Handle Status Updates with Validation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['app_id'], $_POST['status'])) {
    $app_id = filter_var($_POST['app_id'], FILTER_VALIDATE_INT);
    $new_status = $_POST['status'];

    if ($app_id && in_array($new_status, $allowed_statuses)) {
        // Secure UPDATE with ownership check via JOIN
        $update_stmt = $pdo->prepare("
            UPDATE applications a
            JOIN jobs j ON a.job_id = j.id
            SET a.status = ?
            WHERE a.id = ? AND j.employer_id = ?
        ");
        
        if ($update_stmt->execute([$new_status, $app_id, $employer_id])) {
            $message = "Applicant status updated to " . htmlspecialchars($new_status) . ".";
        } else {
            $message = "Failed to update status.";
        }
    } else {
        $message = "Invalid input provided.";
    }
}

// Fetch all applications for this specific job
$stmt = $pdo->prepare("
    SELECT applications.*, users.name AS applicant_name, users.email AS applicant_email 
    FROM applications 
    JOIN users ON applications.seeker_id = users.id 
    WHERE applications.job_id = ? 
    ORDER BY applications.applied_at DESC
");
$stmt->execute([$job_id]);
$applicants = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--text-light); text-decoration: none;">&larr; Back to Dashboard</a>
        <h2 style="margin-top: 1rem;">Applicants for: <span style="color: var(--primary-color);"><?php echo htmlspecialchars($job['title']); ?></span></h2>
    </div>

    <?php if($message): ?>
        <div style="background: #e0f2fe; padding: 10px; border-radius: 4px; color: #0369a1; margin-bottom: 1rem; font-weight: bold; border: 1px solid #bae6fd;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if(count($applicants) > 0): ?>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach($applicants as $app): ?>
                <div style="background: var(--white); padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border-left: 5px solid var(--primary-color);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($app['applicant_name']); ?></h3>
                            <p style="color: var(--text-light); margin-bottom: 1rem;">
                                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($app['applicant_email']); ?>"><?php echo htmlspecialchars($app['applicant_email']); ?></a><br>
                                <strong>Applied On:</strong> <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                            </p>
                        </div>
                        
                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                            <select name="status" style="padding: 6px; border-radius: 4px; cursor: pointer;">
                                <?php foreach($allowed_statuses as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $app['status'] == $status ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-primary" style="padding: 6px 12px; cursor: pointer;">Update</button>
                        </form>
                    </div>

                    <?php if(!empty($app['cover_letter'])): ?>
                        <div style="background: #f8fafc; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                            <strong>Cover Letter:</strong>
                            <p style="white-space: pre-wrap; margin-top: 0.5rem; font-size: 0.95rem; color: #475569;"><?php echo htmlspecialchars($app['cover_letter']); ?></p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <a href="../assets/uploads/<?php echo htmlspecialchars($app['cv_path']); ?>" target="_blank" class="btn-primary" style="background-color: #28a745;">Download CV</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: var(--white); padding: 2rem; text-align: center; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
            <p style="color: var(--text-light);">No one has applied for this position yet.</p>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>