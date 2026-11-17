<?php
// jobs/index.php
require_once '../config/db_connect.php';

// Helper function to calculate "Time Ago"
function timeAgo($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);           // value 60 is seconds
    $hours   = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400);        // value 86400 is 24 hours * 60 * 60

    if($seconds <= 60) { return "Just now"; }
    else if($minutes <= 60) { return "$minutes mins ago"; }
    else if($hours <= 24) { return "$hours hours ago"; }
    else if($days <= 7) { return "$days days ago"; }
    else { return date('M j, Y', $time_ago); }
}

// Basic search filtering logic
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';

// Build the query dynamically based on search inputs
$query = "
    SELECT jobs.*, users.name AS employer_name 
    FROM jobs 
    JOIN users ON jobs.employer_id = users.id 
    WHERE jobs.status = 'approved'
";
$params = [];

if (!empty($search)) {
    $query .= " AND (jobs.title LIKE ? OR jobs.category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($location)) {
    $query .= " AND jobs.location LIKE ?";
    $params[] = "%$location%";
}

$query .= " ORDER BY jobs.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$active_jobs = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<style>
    /* Modern UI Overrides */
    body { background-color: #f4f7f6; }
    
    .page-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .page-header h2 {
        font-size: 2.4rem;
        color: var(--text-dark);
        font-weight: 800;
    }
    
    /* Search Box */
    .modern-search-box {
        background: var(--white);
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        max-width: 900px;
        margin: 0 auto 3rem auto;
        display: flex;
        gap: 1rem;
    }
    .modern-search-box input {
        flex: 1;
        padding: 12px 20px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 1rem;
    }
    .modern-search-box button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 35px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
    }

    /* Horizontal List Layout */
    .job-list-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        max-width: 1000px;
        margin: 0 auto;
    }
    .list-job-card {
        background: #ffffff;
        padding: 20px 30px;
        border-radius: 4px;
        border: 1px solid #eaeaea;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .list-job-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    
    /* Card Elements */
    .job-logo {
        width: 60px;
        height: 60px;
        background: #f1f5f9;
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 25px;
        flex-shrink: 0;
    }
    .job-details {
        flex: 1;
    }
    .job-company {
        color: #007bff;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }
    .job-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #222;
        margin-bottom: 4px;
    }
    .job-location {
        color: #555;
        font-size: 0.95rem;
    }
    
    /* Pill Tags */
    .job-tags {
        display: flex;
        gap: 10px;
        margin: 0 30px;
    }
    .tag {
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        color: #fff;
    }
    .tag-1 { background-color: #ffca28; color: #333; } /* Yellow */
    .tag-2 { background-color: #007bff; } /* Blue */
    .tag-3 { background-color: #333333; } /* Dark */

    /* Right Side (Time & Button) */
    .job-action {
        text-align: right;
        min-width: 140px;
    }
    .time-posted {
        display: block;
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 10px;
    }
    .btn-apply-outline {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        padding: 6px 18px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: 0.3s;
        display: inline-block;
    }
    .btn-apply-outline:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    /* "More List" Button */
    .more-list-btn {
        display: block;
        width: 200px;
        margin: 3rem auto;
        text-align: center;
        padding: 10px;
        border: 1px solid #007bff;
        color: #007bff;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .list-job-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }
        .job-logo { margin-bottom: 15px; }
        .job-tags { margin: 15px 0; flex-wrap: wrap; }
        .job-action { text-align: left; width: 100%; margin-top: 10px; }
    }
</style>

<main style="padding: 4rem 5%; min-height: 75vh;">
    
    <div class="page-header">
        <h2>Discover Your Next Career Move</h2>
    </div>

    <form method="GET" action="" class="modern-search-box">
        <input type="text" name="search" placeholder="Search by Job Title or Category..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="text" name="location" placeholder="City or Remote" value="<?php echo htmlspecialchars($location); ?>">
        <button type="submit">Search</button>
    </form>

    <div class="job-list-container">
        <?php if(count($active_jobs) > 0): ?>
            <?php foreach($active_jobs as $job): ?>
                
                <div class="list-job-card">
                    
                    <div class="job-logo">
                        <?php echo strtoupper(substr($job['employer_name'], 0, 1)); ?>
                    </div>
                    
                    <div class="job-details">
                        <div class="job-company"><?php echo htmlspecialchars($job['employer_name']); ?></div>
                        <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                        <div class="job-location"><?php echo htmlspecialchars($job['location']); ?></div>
                    </div>
                    
                    <div class="job-tags">
                        <span class="tag tag-1"><?php echo htmlspecialchars($job['job_type']); ?></span>
                        <span class="tag tag-2"><?php echo htmlspecialchars($job['category']); ?></span>
                        <span class="tag tag-3"><?php echo htmlspecialchars($job['salary']); ?></span>
                    </div>
                    
                    <div class="job-action">
                        <span class="time-posted">posted <?php echo timeAgo($job['created_at']); ?></span>
                        <a href="details.php?id=<?php echo $job['id']; ?>" class="btn-apply-outline">View Details</a>
                    </div>
                    
                </div>
                
            <?php endforeach; ?>
            
            <a href="index.php" class="more-list-btn">More List</a>
            
        <?php else: ?>
            <div style="text-align: center; padding: 4rem; background: white; border-radius: 8px;">
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 1rem;">No jobs found matching your current criteria.</p>
                <a href="index.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Clear Search</a>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include '../includes/footer.php'; ?>