<?php 
require_once 'config/db_connect.php';

// Fetch the 3 most recent approved jobs to display on the homepage
$stmt = $pdo->query("
    SELECT jobs.*, users.name AS employer_name 
    FROM jobs 
    JOIN users ON jobs.employer_id = users.id 
    WHERE jobs.status = 'approved' 
    ORDER BY jobs.created_at DESC 
    LIMIT 3
");
$recent_jobs = $stmt->fetchAll();

include 'includes/header.php'; 
?>

<main>
    <section class="home-hero">
        <div class="home-hero-content">
            <h1>FIND YOUR <br><span class="highlight-blue">PERFECT JOB</span><br>EASILY</h1>
        </div>
        
        <div class="search-wrapper">
            <form action="jobs/index.php" method="GET" class="hero-search-container">
                <input type="text" name="search" placeholder="Job title or keyword" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <section class="recent-jobs-section">
        <h2 style="margin-bottom: 2rem; color: var(--text-dark);">Recently Posted Vacancies</h2>
        
        <div class="jobs-grid">
            <?php if(count($recent_jobs) > 0): ?>
                <?php foreach($recent_jobs as $job): ?>
                    <div class="job-card">
                        <h3 style="margin-bottom: 0.5rem; color: var(--text-dark);"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem;">
                            <strong><?php echo htmlspecialchars($job['employer_name']); ?></strong> &bull; <?php echo htmlspecialchars($job['location']); ?>
                        </p>
                        <div style="margin-bottom: 1.5rem;">
                            <span class="badge badge-type"><?php echo htmlspecialchars($job['job_type']); ?></span>
                            <span class="badge badge-category"><?php echo htmlspecialchars($job['category']); ?></span>
                        </div>
                        <a href="jobs/details.php?id=<?php echo $job['id']; ?>" class="btn-primary" style="display: block; text-align: center; text-decoration: none;">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; color: var(--text-light);">New job listings will appear here soon once approved.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 4rem;">
            <a href="jobs/index.php" class="btn-primary" style="padding: 12px 30px; font-size: 1.1rem; text-decoration: none;">Browse All Vacancies</a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>