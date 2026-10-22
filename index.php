<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falcon | Login - Finez SEO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="./css/animate.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/modern-theme.css?v=3.5" rel="stylesheet">

    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./app_js/finez-loader.js"></script>
    <script>
        function validatePassword(event) {
            const passwordInput = event.target;
            const pattern = /^[a-zA-Z0-9#@\$]*$/;
            const password = passwordInput.value;

            if (!pattern.test(password)) {
                passwordInput.value = password.replace(/[^a-zA-Z0-9#@\$]/g, "");
                var alertEl = document.getElementById('pass-warning');
                if (alertEl) {
                    alertEl.style.display = 'block';
                    setTimeout(function() { alertEl.style.display = 'none'; }, 3500);
                }
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
    <?php include './app_common/loader.php'; ?>

    <div class="falcon-auth-container animated fadeInDown">
        <!-- Left Brand Banner (Falcon Card Auth Style) -->
        <div class="falcon-auth-banner">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                    <div style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa fa-line-chart"></i>
                    </div>
                    <span style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.03em;">finez<span style="color: #ffffff; opacity: 0.85;">.seo</span></span>
                </div>

                <h3>Empower Your SEO Campaigns</h3>
                <p>The easiest and cleanest platform to manage client domains, backlink submissions, and team productivity in real time.</p>

                <ul class="falcon-auth-feature-list">
                    <li><i class="fa fa-check"></i> High-speed backlink tracking &amp; audit trails</li>
                    <li><i class="fa fa-check"></i> Clean project &amp; root domain management</li>
                    <li><i class="fa fa-check"></i> Team role permissions &amp; employee directory</li>
                </ul>
            </div>

            <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.7);">
                &copy; <?php echo date('Y'); ?> Finez Technologies. Built with Falcon CRM Design.
            </div>
        </div>

        <!-- Right Form Section -->
        <div class="falcon-auth-form-side">
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--falcon-gray-1100); margin-bottom: 4px;">Account Login</h4>
                <p style="font-size: 0.82rem; color: var(--falcon-gray-600); margin: 0;">Sign in to access your SEO dashboard</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                <div class="auth-alert-danger">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>Invalid username or password. Please try again.</span>
                </div>
            <?php endif; ?>

            <div id="pass-warning" class="auth-alert-danger" style="display: none;">
                <i class="fa fa-warning"></i>
                <span>Special character restricted. Only @, #, $ are permitted.</span>
            </div>

            <form role="form" action="./app_login_manager/login_validation.php" method="POST">
                <div class="form-group">
                    <label for="user_name">Username or Email</label>
                    <div class="auth-input-group">
                        <input type="text" id="user_name" name="user_name" class="form-control" placeholder="Enter username" required autofocus autocomplete="username">
                        <i class="fa fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <label for="password" style="margin: 0;">Password</label>
                        <a href="./app_login_manager/forgot_password.php" style="font-size: 0.75rem; color: var(--falcon-primary); font-weight: 500;">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="auth-input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        <i class="fa fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary auth-submit-btn">
                    <span>Log In</span>
                    <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./js/bootstrap.min.js"></script>
</body>

</html>
