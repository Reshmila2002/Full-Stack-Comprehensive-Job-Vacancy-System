<?php
// jobs/details.php
require_once '../config/db_connect.php';

// 1. Validate ID: Ensure it exists and is an integer
$job_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if (!$job_id) {
    header("Location: index.php");
    exit();
}

// 2. Fetch Job Details
$stmt = $pdo->prepare("
    SELECT jobs.*, users.name AS employer_name, users.email AS employer_email
    FROM jobs 
    JOIN users ON jobs.employer_id = users.id 
    WHERE jobs.id = ? AND jobs.status = 'approved'
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

// 3. Handle missing job
if (!$job) {
    include '../includes/header.php';
    echo '<main class="job-details-wrapper"><div class="job-content-card"><h3>Job not found</h3><p>The job posting you are looking for is no longer available or does not exist.</p><a href="index.php">&larr; Back to Job Board</a></div></main>';
    include '../includes/footer.php';
    exit();
}

include '../includes/header.php'; 
?>

<style>
    /* Modern UI Overrides for Job Details Page */
    body { background-color: #f8fafc; }
    
    .job-details-wrapper {
        padding: 4rem 5%;
        max-width: 1000px;
        margin: auto;
        min-height: 75vh;
    }
    
    .job-header-card {
        background: var(--white);
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border-top: 4px solid var(--primary-color);
        margin-bottom: 2rem;
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    
    .company-logo-large {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: #f1f5f9;
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .job-header-info { flex: 1; min-width: 280px; }
    .job-header-info h2 { font-size: 2.2rem; color: #0f172a; margin-bottom: 0.5rem; font-weight: 800; line-height: 1.2; }
    .job-header-meta { font-size: 1.1rem; color: #64748b; margin-bottom: 1.5rem; }
    .badge-container { display: flex; gap: 12px; flex-wrap: wrap; }
    .job-tag { padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }
    .tag-type { background: #e0f2fe; color: #0284c7; }
    .tag-salary { background: #dcfce7; color: #16a34a; }
    .tag-category { background: #fef9c3; color: #ca8a04; }
    
    .job-action-top { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .date-posted { font-size: 0.9rem; color: #94a3b8; font-weight: 500; }

    .btn-apply-main {
        background: var(--primary-color);
        color: white;
        padding: 14px 36px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    .btn-apply-main:hover { background: #0056b3; color: white; transform: translateY(-2px); }

    .job-content-card { background: var(--white); padding: 3rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; }
    .job-content-card h3 { font-size: 1.4rem; color: #0f172a; margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.8rem; font-weight: 700; }
    .job-description-text { font-size: 1.05rem; line-height: 1.8; color: #334155; white-space: pre-wrap; }
    .login-prompt-box { background: #f8fafc; padding: 2.5rem; border-radius: 8px; text-align: center; border: 1px dashed #cbd5e1; margin-top: 3rem; }
</style>

<main class="job-details-wrapper">
    <a href="index.php" style="color: #64748b; text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 1.5rem;">&larr; Back to Job Board</a>

    <div class="job-header-card">
        <div class="company-logo-large"><?php echo strtoupper(substr(htmlspecialchars($job['employer_name']), 0, 1)); ?></div>
        
        <div class="job-header-info">
            <h2><?php echo htmlspecialchars($job['title']); ?></h2>
            <div class="job-header-meta">
                <strong><?php echo htmlspecialchars($job['employer_name']); ?></strong> &bull; <?php echo htmlspecialchars($job['location']); ?>
            </div>
            <div class="badge-container">
                <span class="job-tag tag-type"><?php echo htmlspecialchars($job['job_type']); ?></span>
                <span class="job-tag tag-salary"><?php echo htmlspecialchars($job['salary']); ?></span>
                <span class="job-tag tag-category"><?php echo htmlspecialchars($job['category']); ?></span>
            </div>
        </div>
        
        <div class="job-action-top">
            <span class="date-posted">Posted on <?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'seeker'): ?>
                <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn-apply-main">Apply Now</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="job-content-card">
        <h3>About the Role</h3>
        <div class="job-description-text"><?php echo htmlspecialchars($job['description']); ?></div>
        
        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'seeker'): ?>
            <div style="margin-top: 3rem; text-align: center; padding-top: 2rem; border-top: 1px solid #f1f5f9;">
                <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn-apply-main" style="padding: 16px 40px; font-size: 1.2rem;">Apply for this Job</a>
            </div>
        <?php elseif(!isset($_SESSION['user_id'])): ?>
            <div class="login-prompt-box">
                <p>You must be logged in as a job seeker to apply for this position.</p>
                <a href="../auth/login.php" class="btn-apply-main">Login to Apply</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>