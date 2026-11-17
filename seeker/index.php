<?php
// seeker/index.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('seeker');

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Count total applications made by this user
$app_stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE seeker_id = ?");
$app_stmt->execute([$_SESSION['user_id']]);
$total_applications = $app_stmt->fetchColumn();

include '../includes/header.php'; 
?>

<style>
    /* Custom Dashboard Styles */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color), #004494);
        color: white;
        padding: 3rem 4rem;
        border-radius: var(--border-radius);
        margin-bottom: 3rem;
        box-shadow: 0 10px 20px rgba(0,123,255,0.15);
    }
    .dashboard-header h2 {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }
    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }
    .dash-card {
        background: var(--white);
        padding: 2.5rem 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 4px solid var(--primary-color);
    }
    .dash-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .dash-card h3 {
        color: var(--text-dark);
        margin-bottom: 1rem;
        font-size: 1.4rem;
    }
    .stat-number {
        font-size: 4rem;
        color: var(--primary-color);
        font-weight: 700;
        line-height: 1;
        margin-bottom: 1rem;
    }
    .dash-card p {
        color: var(--text-light);
        margin-bottom: 2rem;
        min-height: 48px;
    }
    .btn-outline {
        display: inline-block;
        padding: 10px 24px;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        text-decoration: none;
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-outline:hover {
        background: var(--primary-color);
        color: white;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
        padding: 12px 24px;
        border-radius: var(--border-radius);
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        transition: background 0.3s;
    }
    .btn-success:hover {
        background-color: #218838;
    }
</style>

<main style="padding: 4rem 5%; max-width: 1000px; margin: auto; min-height: 70vh;">
    
    <div class="dashboard-header">
        <h2>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <p>Manage your career journey and track your job applications from your personal dashboard.</p>
    </div>

    <div class="dashboard-cards">
        
        <div class="dash-card">
            <h3>Total Applications</h3>
            <div class="stat-number"><?php echo $total_applications; ?></div>
            <p>Track the status of the positions you have already applied for.</p>
            <a href="my_applications.php" class="btn-outline">View Application Status</a>
        </div>
        
        <div class="dash-card" style="border-top-color: #28a745;">
            <h3>Find Opportunities</h3>
            <div class="stat-number" style="color: #28a745;">&#128269;</div>
            <p>Browse newly approved vacancies and apply for your dream job.</p>
            <a href="../jobs/index.php" class="btn-success">Search New Jobs</a>
        </div>

    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <a href="profile.php" style="color: var(--text-light); text-decoration: none; font-weight: 500; transition: color 0.3s;">
            <span style="margin-right: 5px;">&#9881;</span> Update My Profile Settings
        </a>
    </div>

</main>

<?php include '../includes/footer.php'; ?>