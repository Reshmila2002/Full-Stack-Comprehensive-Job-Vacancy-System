<?php
// auth/login.php
require_once '../config/db_connect.php';
session_start();
$error = '';

// Note: You will need to update this PHP block to handle registration INSERT logic 
// instead of SELECT login logic since the form has been upgraded to a registration form.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Setup session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } elseif ($user['role'] === 'employer') {
            header("Location: ../employer/index.php");
        } else {
            header("Location: ../seeker/index.php");
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
include '../includes/header.php'; 
?>

<style>
    /* Modern UI Overrides */
    .login-wrapper {
        padding: 5rem 5%;
        min-height: 75vh;
        background-color: #f4f7f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        background: var(--white);
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 500px; /* Widened slightly for the registration fields */
        border-top: 4px solid var(--primary-color);
    }
    .login-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .login-header h2 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    .login-header p {
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
    .form-group input, .form-group select {
        width: 100%;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: var(--primary-color);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    .checkbox-group input {
        width: auto;
    }
    .btn-submit {
        background: var(--primary-color);
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
    }
    .btn-submit:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.2);
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
    .error-text {
        color: #b91c1c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
        font-weight: 500;
    }
</style>

<main class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Register Account</h2>
            <p>Create your Career Link profile.</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validateRegistration(this)">
            
            <div class="form-group">
                <label>Student Name</label>
                <input type="text" name="studentName" placeholder="John Doe">
                <span id="nameError" class="error-text"></span>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="text" name="email" placeholder="name@example.com">
                <span id="emailError" class="error-text"></span>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="07XXXXXXXX">
                <span id="phoneError" class="error-text"></span>
            </div>
            
            <div class="form-group">
                <label>Course</label>
                <select name="course">
                    <option value="">Select a Course</option>
                    <option value="IT">Information Technology</option>
                    <option value="CS">Computer Science</option>
                    <option value="SE">Software Engineering</option>
                </select>
                <span id="courseError" class="error-text"></span>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••">
                <span id="passwordError" class="error-text"></span>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword" placeholder="••••••••">
                <span id="confirmPasswordError" class="error-text"></span>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="terms" id="terms">
                <label for="terms" style="margin:0; font-weight:normal; font-size:0.95rem;">I agree to the terms and conditions</label>
            </div>
            <span id="termsError" class="error-text" style="margin-top:-15px; margin-bottom:15px;"></span>
            
            <button type="submit" class="btn-submit">Register</button>
        </form>
    </div>
</main>

<script>
function validateRegistration(form) {
    // Clear all previous errors before running checks
    document.querySelectorAll('.error-text').forEach(el => el.innerHTML = '');
    let isValid = true;

    // 1. Validating the Name
    if (form.studentName.value === "") {
        document.getElementById("nameError").innerHTML = "Name cannot be empty.";
        isValid = false;
    }

    // 2. Validating the Email
    const emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!emailRegex.test(form.email.value)) {
        document.getElementById("emailError").innerHTML = "Please enter a valid email address.";
        isValid = false;
    }

    // 3. Validating the Phone Number (SL Format)
    const phoneRegex = /^07\d{8}$/;
    if (!phoneRegex.test(form.phone.value)) {
        document.getElementById("phoneError").innerHTML = "Must be a valid SL number (e.g., 0712345678).";
        isValid = false;
    }

    // 4. Validating the Course (Select Dropdown)
    if (form.course.value === "") {
        document.getElementById("courseError").innerHTML = "Please select a course.";
        isValid = false;
    }

    // 5. Validating the Password (Capital & Special Character)
    const pass = form.password.value;
    const hasCapital = /[A-Z]/.test(pass);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(pass);
    
    if (pass.length < 6 || !hasCapital || !hasSpecial) {
        document.getElementById("passwordError").innerHTML = "Password must be at least 6 characters, contain 1 uppercase letter and 1 special character.";
        isValid = false;
    }

    // 6. Validating the Confirm Password
    if (form.confirmPassword.value === "" || form.confirmPassword.value !== pass) {
        document.getElementById("confirmPasswordError").innerHTML = "Passwords do not match.";
        isValid = false;
    }

    // 7. Validating the Terms (Checkbox)
    if (form.terms.checked === false) {
        document.getElementById("termsError").innerHTML = "You must accept the terms and conditions.";
        isValid = false;
    }

    // The Final Step
    return isValid;
}
</script>

<?php include '../includes/footer.php'; ?>