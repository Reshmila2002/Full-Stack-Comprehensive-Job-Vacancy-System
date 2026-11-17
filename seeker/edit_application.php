<?php
// seeker/edit_application.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('seeker');

if (!isset($_GET['id'])) {
    header("Location: my_applications.php");
    exit();
}

$app_id = $_GET['id'];
$seeker_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current application data
$stmt = $pdo->prepare("
    SELECT applications.*, jobs.title AS job_title 
    FROM applications 
    JOIN jobs ON applications.job_id = jobs.id 
    WHERE applications.id = ? AND applications.seeker_id = ?
");
$stmt->execute([$app_id, $seeker_id]);
$application = $stmt->fetch();

// Security check: Make sure they own this application
if (!$application) {
    die("Application not found or unauthorized access.");
}

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cover_letter = trim($_POST['cover_letter'] ?? '');
    $new_cv_path = $application['cv_path']; // Default to old path

    // If they uploaded a new CV, process it
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['cv']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validation 1: Check File Extension
        if (!in_array($filetype, $allowed)) {
            $error = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
        } 
        // Validation 2: Check File Size (Limit to 5MB)
        elseif ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
            $error = "File is too large. Maximum size allowed is 5MB.";
        } 
        // If it passes simple validations, save it
        else {
            $new_filename = uniqid() . '_' . $seeker_id . '.' . $filetype;
            $dest_path = '../assets/uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest_path)) {
                $new_cv_path = $new_filename;
            } else {
                $error = "Error uploading your new CV.";
            }
        }
    }

    // Update the database if no errors found
    if (empty($error)) {
        $update_stmt = $pdo->prepare("UPDATE applications SET cover_letter = ?, cv_path = ? WHERE id = ? AND seeker_id = ?");
        if ($update_stmt->execute([$cover_letter, $new_cv_path, $app_id, $seeker_id])) {
            $success = "Application successfully updated!";
            // Update local variables so the form displays the newly saved values
            $application['cover_letter'] = $cover_letter;
            $application['cv_path'] = $new_cv_path;
        } else {
            $error = "Failed to update application. Please try again.";
        }
    }
}

include '../includes/header.php'; 
?>

<style>
    .edit-wrapper { padding: 4rem 5%; min-height: 75vh; background-color: #f8fafc; display: flex; flex-direction: column; align-items: center; }
    .back-link-container { width: 100%; max-width: 650px; margin-bottom: 1.5rem; }
    .back-link-container a { color: #64748b; text-decoration: none; font-weight: 600; transition: color 0.3s; }
    .back-link-container a:hover { color: var(--primary-color); }
    .apply-card { background: var(--white); padding: 3rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); width: 100%; max-width: 650px; border-top: 4px solid var(--primary-color); }
    .apply-header { text-align: center; margin-bottom: 2.5rem; }
    .apply-header h2 { font-size: 2.2rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .apply-header p { color: #64748b; font-size: 1.05rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 600; color: #334155; margin-bottom: 0.8rem; font-size: 0.95rem; }
    .file-upload-box { border: 2px dashed #cbd5e1; background-color: #f8fafc; padding: 2rem 1.5rem; border-radius: 8px; text-align: center; transition: all 0.3s ease; }
    .file-upload-box input[type="file"] { width: 100%; cursor: pointer; color: #475569; }
    .form-control { width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc; font-size: 1rem; font-family: inherit; color: #1e293b; resize: vertical; }
    .btn-submit { background: var(--primary-color); color: white; border: none; padding: 16px; width: 100%; border-radius: 8px; font-size: 1.15rem; font-weight: 700; cursor: pointer; margin-top: 1rem; }
    .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 15px; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; text-align: center; }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #4ade80; padding: 15px; border-radius: 8px; margin-bottom: 2rem; font-weight: 600; text-align: center; }
</style>

<main class="edit-wrapper">
    <div class="back-link-container">
        <a href="my_applications.php">&larr; Back to My Applications</a>
    </div>

    <div class="apply-card">
        <div class="apply-header">
            <h2>Edit Application</h2>
            <p>Updating your application for: <br><strong><?php echo htmlspecialchars($application['job_title']); ?></strong></p>
        </div>

        <?php if($error): ?><div class="alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Current CV Uploaded:</label>
                <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; color: #0284c7; font-weight: bold; margin-bottom: 10px;">
                    📄 <?php echo htmlspecialchars($application['cv_path']); ?>
                </div>
                <label>Upload New CV (Only if you want to replace the current one)</label>
                <div class="file-upload-box">
                    <input type="file" name="cv" accept=".pdf,.doc,.docx">
                </div>
            </div>

            <div class="form-group">
                <label>Cover Letter</label>
                <textarea name="cover_letter" class="form-control" rows="8"><?php echo htmlspecialchars($application['cover_letter']); ?></textarea>
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>