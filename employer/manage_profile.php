<?php
// employer/manage_profile.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('employer');

$employer_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle Delete (D)
if (isset($_POST['delete_profile'])) {
    $stmt = $pdo->prepare("DELETE FROM company_profiles WHERE employer_id = ?");
    if ($stmt->execute([$employer_id])) {
        $message = "Your company profile has been deleted.";
    }
}

// Handle Create/Update (C/U)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $company_name = trim($_POST['company_name']);
    $industry = trim($_POST['industry']);
    $bio = trim($_POST['bio']);
    $website = trim($_POST['website']);

    if (empty($company_name) || empty($industry) || empty($bio)) {
        $error = "Company Name, Industry, and Bio are required.";
    } else {
        // Check if a profile already exists
        $check = $pdo->prepare("SELECT id FROM company_profiles WHERE employer_id = ?");
        $check->execute([$employer_id]);
        $exists = $check->fetch();

        if ($exists) {
            // Update Existing Profile
            $stmt = $pdo->prepare("UPDATE company_profiles SET company_name = ?, industry = ?, bio = ?, website = ? WHERE employer_id = ?");
            if ($stmt->execute([$company_name, $industry, $bio, $website, $employer_id])) {
                $message = "Company profile updated successfully.";
            }
        } else {
            // Create New Profile
            $stmt = $pdo->prepare("INSERT INTO company_profiles (employer_id, company_name, industry, bio, website) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$employer_id, $company_name, $industry, $bio, $website])) {
                $message = "Company profile created successfully.";
            }
        }
        
        // Keep the core 'users' table in sync with the updated company name
        $update_user = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $update_user->execute([$company_name, $employer_id]);
        $_SESSION['name'] = $company_name; // Update the session so the navbar changes immediately
    }
}

// 1. Fetch user data from the 'users' table (to get default name and email from registration)
$user_stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->execute([$employer_id]);
$user_data = $user_stmt->fetch();

// 2. Read current profile data (if they have already created one)
$stmt = $pdo->prepare("SELECT * FROM company_profiles WHERE employer_id = ?");
$stmt->execute([$employer_id]);
$profile = $stmt->fetch();

// 3. Set the default values
// If a profile exists, use that name. If it's empty, grab the name from registration!
$default_company_name = $profile ? $profile['company_name'] : $user_data['name'];
$default_email = $user_data['email'];

include '../includes/header.php'; 
?>

<style>
    .profile-wrapper { padding: 4rem 5%; min-height: 75vh; background-color: #f4f7f6; }
    .form-card { background: var(--white); padding: 3rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; border-top: 4px solid var(--primary-color); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
    .form-control { width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc; font-size: 1rem; font-family: inherit; }
    .btn-submit { background: var(--primary-color); color: white; border: none; padding: 14px; width: 100%; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-top: 1rem; }
    .btn-danger { background: #dc2626; color: white; border: none; padding: 14px; width: 100%; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-top: 1rem; }
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; text-align: center; }
    
    /* Responsive Row Setup */
    .form-row { display: flex; gap: 1.5rem; }
    .form-row .form-group { flex: 1; }
    @media (max-width: 768px) { .form-row { flex-direction: column; gap: 0; } }
</style>

<main class="profile-wrapper">
    <div class="form-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="color: #1e293b; margin: 0;">Manage Company Profile</h2>
            <a href="index.php" style="color: #64748b; text-decoration: none;">&larr; Back to Dashboard</a>
        </div>

        <?php if($error): ?><div class="alert" style="background: #fee2e2; color: #b91c1c;"><?php echo $error; ?></div><?php endif; ?>
        <?php if($message): ?><div class="alert" style="background: #dcfce7; color: #15803d;"><?php echo $message; ?></div><?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($default_company_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Account Email (Read-Only)</label>
                    <!-- Email is displayed but locked to prevent accidental login issues -->
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($default_email); ?>" readonly style="background-color: #e2e8f0; color: #64748b; cursor: not-allowed;">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Industry Sector</label>
                    <input type="text" name="industry" class="form-control" value="<?php echo htmlspecialchars($profile['industry'] ?? ''); ?>" placeholder="e.g. Technology, Finance" required>
                </div>
                
                <div class="form-group">
                    <label>Website URL (Optional)</label>
                    <input type="text" name="website" class="form-control" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>" placeholder="https://www.company.com">
                </div>
            </div>

            <div class="form-group">
                <label>About the Company</label>
                <textarea name="bio" class="form-control" rows="6" required placeholder="Describe your company culture, mission, and what you do..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="save_profile" class="btn-submit">Save Profile</button>
        </form>

        <?php if($profile): ?>
            <hr style="margin: 3rem 0; border: 0; border-top: 1px solid #e2e8f0;">
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete your public company profile? Your job postings will remain active.');">
                <button type="submit" name="delete_profile" class="btn-danger">Delete Company Profile</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>