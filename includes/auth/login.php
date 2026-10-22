<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin | Login</title>

    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../../assets/css/animate.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function validatePassword(event) {
            const passwordInput = event.target;
            const pattern = /^[a-zA-Z0-9#@\$]*$/; // Allows only specified characters
            const password = passwordInput.value;

            // Check if password contains only allowed characters
            if (!pattern.test(password)) {
                alert("Invalid charecter found. use @, #, $, Avoid other special charecters.");
                // Remove the last character
                passwordInput.value = password.replace(/[^a-zA-Z0-9#@\$]/g, "");
            }
        }

        // Event listener to validate password on each keyup
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('keyup', validatePassword);
        });
    </script>

</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <p>Login in. To see it in action.</p>
            <form class="m-t" role="form" action="login_validation.php" method="POST">
                <div class="form-group">
                    <input type="text" id="user_name" name="user_name" class="form-control" placeholder="Username" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b" onsubmit="return validatePassword()">Login</button>

                <!-- <a href="forgot_password.php"><small>Forgot password?</small></a> -->
            </form>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="../../assets/js/jquery-3.1.1.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>

</body>

</html>
