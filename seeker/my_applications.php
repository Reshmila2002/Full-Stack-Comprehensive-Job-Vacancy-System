<?php
// seeker/my_applications.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('seeker');

$seeker_id = $_SESSION['user_id'];
$message = '';

// Handle Delete (Withdraw Application)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_app_id'])) {
    // Basic validation: ensure the ID is not empty and numeric
    $delete_id = trim($_POST['delete_app_id']);
    
    if (!empty($delete_id)) {
        // Ensure the application belongs to this specific seeker before deleting
        $del_stmt = $pdo->prepare("DELETE FROM applications WHERE id = ? AND seeker_id = ?");
        if ($del_stmt->execute([$delete_id, $seeker_id])) {
            // Check if any row was actually deleted
            if ($del_stmt->rowCount() > 0) {
                $message = "Application successfully withdrawn.";
            } else {
                $message = "Application not found or unauthorized action.";
            }
        } else {
            $message = "Error withdrawing application.";
        }
    }
}

// Fetch user's applications
$stmt = $pdo->prepare("
    SELECT applications.*, jobs.title, jobs.location, users.name AS employer_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON jobs.employer_id = users.id
    WHERE applications.seeker_id = ?
    ORDER BY applications.applied_at DESC
");
$stmt->execute([$seeker_id]);
$applications = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<style>
    /* Modern UI Overrides for My Applications Page */
    .dashboard-wrapper {
        padding: 5rem 5%;
        max-width: 1400px; 
        margin: auto;
        min-height: 75vh;
    }
    
    .dashboard-header {
        margin-bottom: 2.5rem;
    }
    
    .dashboard-header h2 {
        font-size: 2.4rem;
        color: #1e293b;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    .dashboard-header p {
        color: #64748b;
        font-size: 1.1rem;
    }

    /* Modern Table Card */
    .table-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        overflow-x: auto;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    
    .modern-table th {
        background-color: #f8fafc;
        padding: 20px 30px; 
        color: #64748b;
        font-size: 1rem; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
    }
    
    .modern-table td {
        padding: 24px 30px; 
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
        font-size: 1.1rem; 
    }
    
    .modern-table tr:last-child td {
        border-bottom: none;
    }
    
    .modern-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .modern-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    
    .job-title-link {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.2rem; 
        text-decoration: none;
        transition: color 0.3s;
    }
    .job-title-link:hover {
        color: #004494;
        text-decoration: underline;
    }

    /* Status Badges */
    .status-badge {
        padding: 8px 18px; 
        border-radius: 25px;
        font-size: 0.95rem; 
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-applied { background-color: #e0f2fe; color: #0284c7; } 
    .status-reviewed { background-color: #fef9c3; color: #ca8a04; } 
    .status-hired { background-color: #dcfce7; color: #16a34a; }   
    .status-rejected { background-color: #fee2e2; color: #dc2626; } 
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .btn-edit {
        background-color: #fef08a;
        color: #854d0e;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: background 0.3s;
    }
    .btn-edit:hover { background-color: #fde047; }
    
    .btn-delete {
        background-color: #fee2e2;
        color: #b91c1c;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-delete:hover { background-color: #fca5a5; }

    .empty-state {
        padding: 5rem 2rem;
        text-align: center;
        color: #64748b;
    }
    .btn-browse {
        background: var(--primary-color);
        color: white;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        transition: all 0.3s ease;
        display: inline-block;
        margin-top: 1rem;
    }
    .btn-browse:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 86, 179, 0.3);
        color: white;
    }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #4ade80; padding: 15px; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; }
</style>

<main class="dashboard-wrapper">
    <div class="dashboard-header">
        <h2>My Applications</h2>
        <p>Track the status of your submitted job applications.</p>
    </div>

    <?php if($message): ?>
        <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="table-card">
        <?php if(count($applications) > 0): ?>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Date Applied</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($applications as $app): ?>
                        <tr>
                            <td>
                                <a href="../jobs/details.php?id=<?php echo htmlspecialchars($app['job_id']); ?>" class="job-title-link">
                                    <?php echo htmlspecialchars($app['title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($app['employer_name']); ?></td>
                            <td style="color: #64748b;">
                                <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                            </td>
                            <td>
                                <?php $statusClass = 'status-' . strtolower($app['status']); ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($app['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_application.php?id=<?php echo htmlspecialchars($app['id']); ?>" class="btn-edit">Edit</a>
                                    
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to withdraw this application? This action cannot be undone.');" style="margin: 0;">
                                        <input type="hidden" name="delete_app_id" value="<?php echo htmlspecialchars($app['id']); ?>">
                                        <button type="submit" class="btn-delete">Withdraw</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p style="font-size: 1.2rem; margin-bottom: 0.5rem;">You haven't applied to any jobs yet.</p>
                <p style="margin-bottom: 1.5rem;">Start exploring opportunities to land your dream job.</p>
                <a href="../jobs/index.php" class="btn-browse">Browse Jobs</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>