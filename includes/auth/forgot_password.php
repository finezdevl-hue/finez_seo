<?php
session_start();
include_once __DIR__ . '/../../config/db.php';

$feedback = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM tbl_login WHERE email = ?";
    $parameters = [$_POST['email']];
    $types = "s";

    $result = app_exec_getresult($sql, $parameters, $types);

    if ($result && $result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $sql = "UPDATE tbl_login SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
        $parameters = [$token, $expires_at, $_POST['email']];
        $types = "sss";
        $result = app_exec_nonquery($sql, $parameters, $types);

        $reset_link = "http://fineztechnologies.com/reset_password.php?token=" . $token;

        $to = $email;
        $subject = "Password Reset Request";
        $message = "Hello, \n\nClick the link to reset your password (valid for 1 hour):\n" . $reset_link;
        $headers = "From: no-reply@yourwebsite.com";

        if (@mail($to, $subject, $message, $headers)) {
            $feedback = "Password reset instructions sent to your email.";
            $feedback_type = "success";
        } else {
            $feedback = "Failed to send email. Please verify mail configuration.";
            $feedback_type = "danger";
        }
    } else {
        $feedback = "No account found with that email address.";
        $feedback_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Falcon | Forgot Password - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../../assets/css/animate.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="../../assets/css/modern-theme.css" rel="stylesheet">
    <script src="../../assets/js/finez-loader.js"></script>
</head>

<body class="auth-page-body">
    <?php include __DIR__ . '/../loader.php'; ?>

    <div style="width: 100%; max-width: 440px; background: #ffffff; border: 1px solid var(--falcon-border-color); border-radius: var(--falcon-border-radius); box-shadow: var(--falcon-card-shadow); padding: 36px 32px;" class="animated fadeInDown">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: var(--falcon-primary-bg-subtle); color: var(--falcon-primary); display: inline-flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px;">
                <i class="fa fa-key"></i>
            </div>
            <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--falcon-gray-1100); margin-bottom: 4px;">Reset Password</h4>
            <p style="font-size: 0.82rem; color: var(--falcon-gray-600); margin: 0;">Enter your email to receive recovery instructions</p>
        </div>

        <?php if (!empty($feedback)): ?>
            <div class="<?php echo ($feedback_type === 'success') ? 'auth-alert-danger' : 'auth-alert-danger'; ?>" style="<?php echo ($feedback_type === 'success') ? 'background: var(--falcon-success-bg-subtle); border-color: rgba(0, 210, 122, 0.2); color: var(--falcon-success);' : ''; ?>">
                <i class="fa <?php echo ($feedback_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo htmlspecialchars($feedback); ?></span>
            </div>
        <?php endif; ?>

        <form role="form" method="POST" action="">
            <div class="form-group">
                <label for="email">Account Email</label>
                <div class="auth-input-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@company.com" required autofocus>
                    <i class="fa fa-envelope"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary auth-submit-btn">
                <span>Send Reset Link</span>
                <i class="fa fa-paper-plane" style="margin-left: 6px;"></i>
            </button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="../../" style="color: var(--falcon-primary); font-size: 0.8rem; font-weight: 600;">
                <i class="fa fa-arrow-left"></i> Return to Login
            </a>
        </div>
    </div>

    <script src="../../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
</body>

</html>
