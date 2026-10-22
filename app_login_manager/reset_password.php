<?php
session_start();
include '../app_common/db_connect.php'; 

$status_msg = '';
$status_type = '';
$can_reset = false;
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

if (!empty($token)) { 
    $sql = "SELECT * FROM tbl_login WHERE reset_token = ? AND reset_token_expires > NOW()"; 
    $parameters = [$token];                    
    $types = "s";
    $result = app_exec_getresult($sql, $parameters, $types);

    if ($result && $result->num_rows > 0) { 
        $can_reset = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) { 
            $new_password = $_POST['password'];

            $update_sql = "UPDATE tbl_login SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?";            
            $update_params = [$new_password, $token];  
            $update_types = "ss";  
            app_exec_nonquery($update_sql, $update_params, $update_types);

            $status_msg = "Password has been reset successfully. You can now login."; 
            $status_type = "success";
            $can_reset = false;
        } 
    } else { 
        $status_msg = "This password reset link is invalid or has expired."; 
        $status_type = "danger";
    } 
} else { 
    $status_msg = "No reset token provided. Please request a new link."; 
    $status_type = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon | Set New Password - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/modern-theme.css" rel="stylesheet">
    <script src="../app_js/finez-loader.js"></script>

    <script>
        function validatePassword(event) {
            const passwordInput = event.target;
            const pattern = /^[a-zA-Z0-9#@\$]*$/;
            const password = passwordInput.value;

            if (!pattern.test(password)) {
                passwordInput.value = password.replace(/[^a-zA-Z0-9#@\$]/g, "");
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('keyup', validatePassword);
            }
        });
    </script>
</head>

<body class="auth-page-body">
    <?php include '../app_common/loader.php'; ?>

    <div style="width: 100%; max-width: 440px; background: #ffffff; border: 1px solid var(--falcon-border-color); border-radius: var(--falcon-border-radius); box-shadow: var(--falcon-card-shadow); padding: 36px 32px;" class="animated fadeInDown">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: var(--falcon-primary-bg-subtle); color: var(--falcon-primary); display: inline-flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px;">
                <i class="fa fa-shield"></i>
            </div>
            <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--falcon-gray-1100); margin-bottom: 4px;">Create New Password</h4>
            <p style="font-size: 0.82rem; color: var(--falcon-gray-600); margin: 0;">Ensure your account uses a secure password</p>
        </div>

        <?php if (!empty($status_msg)): ?>
            <div class="auth-alert-danger" style="<?php echo ($status_type === 'success') ? 'background: var(--falcon-success-bg-subtle); border-color: rgba(0, 210, 122, 0.2); color: var(--falcon-success);' : ''; ?>">
                <i class="fa <?php echo ($status_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo htmlspecialchars($status_msg); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($can_reset): ?>
            <form role="form" method="POST" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="auth-input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autofocus>
                        <i class="fa fa-lock"></i>
                    </div>
                    <small style="color: var(--falcon-gray-500); font-size: 0.75rem;">Allowed characters: letters, numbers, @, #, $</small>
                </div>

                <button type="submit" class="btn btn-primary auth-submit-btn">
                    <span>Update Password</span>
                    <i class="fa fa-check" style="margin-left: 6px;"></i>
                </button>
            </form>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: center;">
            <a href="../index.php" style="color: var(--falcon-primary); font-size: 0.8rem; font-weight: 600;">
                <i class="fa fa-arrow-left"></i> Return to Login
            </a>
        </div>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>
