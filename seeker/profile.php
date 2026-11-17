<?php
// seeker/profile.php
require_once '../config/db_connect.php';
require_once '../includes/auth_check.php';
requireRole('seeker');

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validation 1: Check for empty values
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } 
    // Validation 2: Ensure the email is a valid format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } 
    else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            if ($stmt->execute([$name, $email, $user_id])) {
                $success = "Profile updated successfully!";
                // Update session name just in case they changed it
                $_SESSION['name'] = $name; 
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "That email is already in use by another account.";
            } else {
                $error = "An error occurred. Please try again.";
            }
        }
    }
}

// Fetch current data to populate the form
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

include '../includes/header.php'; 
?>

<main style="padding: 4rem 5%; max-width: 600px; margin: auto;">
    <h2>My Profile</h2>
    
    <?php if($error): ?>
        <div style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div style="color: green; margin-bottom: 1rem; font-weight: bold;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="" style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
        <div style="margin-bottom: 1rem;">
            <label>Full Name</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($current_user['name'] ?? ''); ?>" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label>Email Address</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($current_user['email'] ?? ''); ?>" required style="width: 100%; padding: 8px;">
        </div>
        <button type="submit" class="btn-primary" style="width: 100%;">Save Changes</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>