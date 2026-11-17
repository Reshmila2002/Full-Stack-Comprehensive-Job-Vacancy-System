<?php
// auth/register_employer.php
require_once '../config/db_connect.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // For employers, 'name' can be the Company Name
    $company_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($company_name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'employer'; 
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$company_name, $email, $hashed_password, $role])) {
                $success = "Company account created! You can now login.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "This email is already registered.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
include '../includes/header.php'; 
?>

<style>
    /* UI Overrides for Employer Registration Page */
    .register-wrapper {
        padding: 5rem 5%;
        min-height: 75vh;
        background-color: #f4f7f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .register-card {
        background: var(--white);
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 500px;
        /* Using a dark slate color to distinguish employer accounts */
        border-top: 4px solid #1e293b; 
    }
    .register-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .register-header h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    .register-header p {
        color: #64748b;
        font-size: 1.05rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    .form-group input {
        width: 100%;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    .form-group input:focus {
        outline: none;
        border-color: #1e293b;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
    }
    .btn-employer {
        background: #1e293b;
        color: white;
        border: none;
        padding: 14px;
        width: 100%;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        display: inline-block;
        text-align: center;
        text-decoration: none;
    }
    .btn-employer:hover {
        background: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 600;
        text-align: center;
    }
    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #f87171;
    }
    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #4ade80;
    }
    .register-footer {
        text-align: center;
        margin-top: 2rem;
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    .register-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }
    .register-footer a:hover {
        color: #004494;
        text-decoration: underline;
    }
    .error-text {
        color: #b91c1c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
        font-weight: 500;
    }
</style>

<main class="register-wrapper">
    <div class="register-card">
        
        <div class="register-header">
            <h2>Employer Registration</h2>
            <p>Create an account to post jobs and find top talent.</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div style="margin-top: 1rem;">
                    <a href="login.php" class="btn-employer">Proceed to Login</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="" onsubmit="return validateEmployerRegistration(this)">
                <div class="form-group">
                    <label>Company Name (or Your Full Name)</label>
                    <input type="text" name="name" placeholder="e.g. Tech Solutions Inc.">
                    <span id="nameError" class="error-text"></span>
                </div>
                
                <div class="form-group">
                    <label>Company Email Address</label>
                    <input type="text" name="email" placeholder="hr@company.com">
                    <span id="emailError" class="error-text"></span>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Create a strong password">
                    <span id="passwordError" class="error-text"></span>
                </div>
                
                <button type="submit" class="btn-employer">Register Company</button>
            </form>
        <?php endif; ?>
        
        <div class="register-footer">
            Looking for a job instead? <a href="register_seeker.php">Register as a Job Seeker</a>.<br>
            Already have an account? <a href="login.php">Login here</a>.
        </div>
        
    </div>
</main>

<script>
function validateEmployerRegistration(form) {
    // Clear previous error messages
    document.querySelectorAll('.error-text').forEach(el => el.innerHTML = '');
    let isValid = true;

    // 1. Validating the Company Name
    if (form.name.value.trim() === "") {
        document.getElementById("nameError").innerHTML = "Company Name cannot be empty.";
        isValid = false;
    }

    // 2. Validating the Email
    const emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!emailRegex.test(form.email.value)) {
        document.getElementById("emailError").innerHTML = "Please enter a valid email address.";
        isValid = false;
    }

    // 3. Validating the Password (Length, Capital & Special Character)
    const pass = form.password.value;
    const hasCapital = /[A-Z]/.test(pass);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(pass);
    
    if (pass.length < 6 || !hasCapital || !hasSpecial) {
        document.getElementById("passwordError").innerHTML = "Password must be at least 6 characters, contain 1 uppercase letter and 1 special character.";
        isValid = false;
    }

    return isValid;
}
</script>

<?php include '../includes/footer.php'; ?>