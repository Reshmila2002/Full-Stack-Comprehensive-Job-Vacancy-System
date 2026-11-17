<?php
// employer/post_job.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('employer');

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Verify CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed.");
    }

    // 2. Sanitize Inputs
    $title       = trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
    $category    = trim(htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8'));
    $location    = trim(htmlspecialchars($_POST['location'], ENT_QUOTES, 'UTF-8'));
    $salary      = trim(htmlspecialchars($_POST['salary'], ENT_QUOTES, 'UTF-8'));
    $job_type    = $_POST['job_type'];
    $description = trim($_POST['description']); // Keep HTML for formatting, but validate length
    $employer_id = $_SESSION['user_id'];

    // 3. Validation Logic
    $valid_job_types = ['Full-Time', 'Part-Time', 'Internship', 'Contract'];
    
    if (strlen($title) < 5) {
        $error = "Job title must be at least 5 characters long.";
    } elseif (!in_array($job_type, $valid_job_types)) {
        $error = "Invalid job type selected.";
    } elseif (strlen($description) < 50) {
        $error = "Please provide a more detailed description (at least 50 characters).";
    } elseif (empty($title) || empty($category) || empty($location) || empty($salary)) {
        $error = "All fields are required.";
    } else {
        // 4. Secure Database Insertion
        try {
            $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, category, location, salary, job_type, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$employer_id, $title, $category, $location, $salary, $job_type, $description])) {
                $success = "Job posted successfully! It is currently pending admin approval.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage()); // Log error for admin
            $error = "A system error occurred. Please try again later.";
        }
    }
}

include '../includes/header.php';
?>

<main class="post-job-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2>Post a New Job Vacancy</h2>
        </div>
        
        <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="index.php">Return to Dashboard</a></div>
        <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text" name="title" class="form-control" required minlength="5" placeholder="e.g. Senior Software Engineer">
                </div>

                <div class="form-group">
                    <label>Full Job Description</label>
                    <textarea name="description" class="form-control" rows="8" required minlength="50" placeholder="Minimum 50 characters..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit Job for Approval</button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include '../includes/footer.php'; ?>