<?php
// jobs/apply.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';

// Only seekers can apply for jobs
requireRole('seeker');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$job_id = $_GET['id'];
$seeker_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if already applied
$check_stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
$check_stmt->execute([$job_id, $seeker_id]);
if ($check_stmt->fetch()) {
    $error = "You have already applied for this position.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)) {
    $cover_letter = trim($_POST['cover_letter']);
    
    // File Upload Handling
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['cv']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create a unique file name to prevent overwriting
            $new_filename = uniqid() . '_' . $seeker_id . '.' . $filetype;
            $upload_dir = '../assets/uploads/';
            $dest_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest_path)) {
                // Insert into DB
                $stmt = $pdo->prepare("INSERT INTO applications (job_id, seeker_id, cover_letter, cv_path) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$job_id, $seeker_id, $cover_letter, $new_filename])) {
                    $success = "Your application has been submitted successfully!";
                } else {
                    $error = "Database error. Could not submit application.";
                }
            } else {
                $error = "Error uploading your CV.";
            }
        } else {
            $error = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
        }
    } else {
        $error = "Please upload your CV.";
    }
}

include '../includes/header.php'; 
?>

<style>
    /* Modern UI Overrides for Application Page */
    .apply-wrapper {
        padding: 4rem 5%;
        min-height: 75vh;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .back-link-container {
        width: 100%;
        max-width: 650px;
        margin-bottom: 1.5rem;
    }
    
    .back-link-container a {
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }
    
    .back-link-container a:hover {
        color: var(--primary-color);
    }
    
    .apply-card {
        background: var(--white);
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        width: 100%;
        max-width: 650px;
        border-top: 4px solid var(--primary-color);
    }
    
    .apply-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .apply-header h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .apply-header p {
        color: #64748b;
        font-size: 1.05rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.8rem;
        font-size: 0.95rem;
    }
    
    /* Modern File Upload Zone */
    .file-upload-box {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        padding: 2rem 1.5rem;
        border-radius: 8px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .file-upload-box:hover {
        border-color: var(--primary-color);
        background-color: #f1f5f9;
    }
    
    .file-upload-box input[type="file"] {
        width: 100%;
        cursor: pointer;
        color: #475569;
    }
    
    .form-control {
        width: 100%;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
        color: #1e293b;
        resize: vertical;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    
    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 16px;
        width: 100%;
        border-radius: 8px;
        font-size: 1.15rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }
    
    .btn-submit:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.2);
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 2rem;
        font-weight: 600;
        text-align: center;
    }
    .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #4ade80; }
</style>

<main class="apply-wrapper">
    
    <div class="back-link-container">
        <a href="details.php?id=<?php echo htmlspecialchars($job_id); ?>">&larr; Back to Job Details</a>
    </div>

    <div class="apply-card">
        
        <div class="apply-header">
            <h2>Submit Your Application</h2>
            <p>Upload your resume and tell the employer why you are a great fit.</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div style="margin-top: 15px;">
                    <a href="../seeker/my_applications.php" class="btn-submit" style="display:inline-block; text-decoration:none;">Track My Application</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Upload CV (PDF, DOC, DOCX)</label>
                    <div class="file-upload-box">
                        <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover Letter (Optional)</label>
                    <textarea name="cover_letter" class="form-control" rows="8" placeholder="Introduce yourself, explain your relevant experience, and highlight why you are the best candidate for this role..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit Application</button>
            </form>
        <?php endif; ?>
        
    </div>
</main>

<?php include '../includes/footer.php'; ?>