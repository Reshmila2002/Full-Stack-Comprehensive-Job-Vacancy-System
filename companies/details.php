<?php
// companies/details.php
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$profile_id = $_GET['id'];

// Get company profile
$stmt = $pdo->prepare("SELECT * FROM company_profiles WHERE id = ?");
$stmt->execute([$profile_id]);
$company = $stmt->fetch();

if (!$company) {
    die("Company profile not found.");
}

// Get all approved jobs posted by this specific employer
$job_stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? AND status = 'approved' ORDER BY created_at DESC");
$job_stmt->execute([$company['employer_id']]);
$jobs = $job_stmt->fetchAll();

include '../includes/header.php'; 
?>

<style>
    .profile-details-wrapper { padding: 4rem 5%; max-width: 1000px; margin: auto; min-height: 75vh; }
    
    .company-header-card { background: var(--primary-color); padding: 4rem 3rem; border-radius: 12px; color: white; text-align: center; margin-bottom: 3rem; box-shadow: 0 10px 30px rgba(0,123,255,0.2); }
    .company-header-card h1 { font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; }
    .company-header-card p { font-size: 1.2rem; opacity: 0.9; }
    .company-link { display: inline-block; margin-top: 1.5rem; color: white; text-decoration: underline; font-weight: 600; }

    .bio-card { background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 3rem; line-height: 1.8; font-size: 1.1rem; color: #334155; }
    
    .jobs-section h3 { font-size: 1.8rem; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; }
    .job-listing { background: white; padding: 1.5rem 2rem; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; transition: transform 0.2s; }
    .job-listing:hover { transform: translateX(5px); border-color: var(--primary-color); }
    .job-listing h4 { font-size: 1.3rem; margin-bottom: 0.3rem; color: #0f172a; }
    .btn-apply-outline { border: 2px solid var(--primary-color); color: var(--primary-color); padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: 0.3s; }
    .btn-apply-outline:hover { background: var(--primary-color); color: white; }
</style>

<main class="profile-details-wrapper">
    <a href="index.php" style="color: #64748b; text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 1.5rem;">&larr; Back to Directory</a>

    <div class="company-header-card">
        <h1><?php echo htmlspecialchars($company['company_name']); ?></h1>
        <p><?php echo htmlspecialchars($company['industry']); ?></p>
        <?php if(!empty($company['website'])): ?>
            <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="company-link">Visit Official Website &rarr;</a>
        <?php endif; ?>
    </div>

    <div class="bio-card">
        <h3 style="color: #0f172a; margin-bottom: 1rem;">About Us</h3>
        <?php echo nl2br(htmlspecialchars($company['bio'])); ?>
    </div>

    <div class="jobs-section">
        <h3>Open Positions at <?php echo htmlspecialchars($company['company_name']); ?></h3>
        
        <?php if(count($jobs) > 0): ?>
            <?php foreach($jobs as $job): ?>
                <div class="job-listing">
                    <div>
                        <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                        <span style="color: #64748b; font-size: 0.95rem;">📍 <?php echo htmlspecialchars($job['location']); ?> &nbsp; | &nbsp; 💼 <?php echo htmlspecialchars($job['job_type']); ?></span>
                    </div>
                    <a href="../jobs/details.php?id=<?php echo $job['id']; ?>" class="btn-apply-outline">View Job</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #64748b; padding: 2rem; text-align: center; background: white; border-radius: 8px;">There are currently no active job postings from this company.</p>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>