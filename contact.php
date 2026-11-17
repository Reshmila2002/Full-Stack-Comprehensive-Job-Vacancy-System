<?php
// contact.php

// 1. Database එකට සම්බන්ධ වන config ෆයිල් එක ඇතුළත් කර ගැනීම
require_once 'config/db_connect.php';

// සාර්ථක සහ අසාර්ථක පණිවිඩ ගබඩා කිරීමට Variables මුලින්ම හිස්ව තබා ගැනීම
$success = '';
$error = '';

// --- පරිශීලකයා Form එක Submit කල විට (POST Request එකක් ආ විට) ක්‍රියාත්මක වන කොටස ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 2. INPUT SANITIZATION & TRIMMING:
    // trim() මඟින් පරිශීලකයා ඇතුළත් කල දත්ත වල දෙපැත්තේ ඇති අනවශ්‍ය හිස්තැන් (White spaces) ඉවත් කරයි.
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // 3. FORM VALIDATION: ක්ෂේත්‍ර කිසිවක් හිස්ව තිබේදැයි Back-end එකෙන් නැවත පරීක්ෂා කිරීම
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // 4. PREPARED STATEMENTS: SQL Injection ප්‍රහාර වැළැක්වීම සඳහා '?' ලකුණු (Placeholders) භාවිතා කිරීම.
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            
            // ආරක්ෂිතව දත්ත ඩේටාබේස් එකේ 'contact_messages' වගුවට ඇතුළත් කිරීම執行
            if ($stmt->execute([$name, $email, $subject, $message])) {
                $success = "Thank you, $name! Your message has been securely sent to our support team.";
            }
        } catch (PDOException $e) {
            // 5. ERROR HANDLING: ඩේටාබේස් එකේ ගැටලුවක් වුවහොත් පද්ධතිය ක්‍රැෂ් නොවී පරිශීලකයාට මිත්‍රශීලී පණිවිඩයක් පෙන්වීම
            $error = "Failed to send message. Please try again later.";
        }
    }
}

// 6. වෙබ් අඩවියේ පොදු Header (හිස් පුවරුව) කොටස ඇතුළත් කිරීම
include 'includes/header.php'; 
?>

<style>
    .contact-wrapper {
        padding: 5rem 5%;
        min-height: 75vh;
        background-color: #f4f7f6;
    }
    .contact-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .contact-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    .contact-header p {
        color: #64748b;
        font-size: 1.1rem;
    }
    .contact-card {
        background: var(--white);
        padding: 3rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        max-width: 600px;
        margin: 0 auto;
        border-top: 4px solid var(--primary-color); /* කාඩ්පතේ ඉහළ කෙළවරට ප්‍රධාන වර්ණයෙන් සියුම් Border එකක් දැමීම */
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
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    /* පරිශීලකයා Input Box එකක් මත ක්ලික් කල විට (Focus) සිදුවන වෙනස්කම් */
    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1); /* පෙට්ටිය වටා ලස්සන නිල් පැහැති සෙවනැල්ලක් (Glow) ඇති කිරීම */
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
    /* Submit බටන් එක උඩට මවුස් එක ගෙන ගිය විට (Hover) සිදුවන ත්‍රිමාණ වෙනස්කම් */
    .btn-submit:hover {
        background: #0056b3;
        transform: translateY(-2px); /* බටන් එක ස්වල්පයක් ඉහළට එසවීම */
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.2);
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 2rem;
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
</style>

<div class="contact-wrapper">
    <div class="contact-header">
        <h2>Contact Support</h2>
        <p>Having trouble with the platform? Send us a message.</p>
    </div>

    <div class="contact-card">
        <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

        <?php if (empty($success)): ?>
        <form method="POST">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" placeholder="How can we help?" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
            
            <button type="submit" class="btn-submit">Send Message</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>