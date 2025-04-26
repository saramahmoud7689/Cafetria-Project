<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .error-message {
        color: red;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .alert-success {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        width: 90%;
        max-width: 500px;
        animation: fadeInOut 5s forwards;
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
        }

        10% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <?php if (isset($_GET['reset_success'])): ?>
    <div class="alert alert-success">
        Password has been reset successfully! You can now login.
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Login</h3>
                        <form id="loginForm" action="login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Email</label>
                                <input type="text" id="username" name="username" class="form-control">
                                <div id="usernameError" class="error-message"></div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control">
                                <div id="passwordError" class="error-message"></div>
                            </div>

                            <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger mb-3">
                                <?php
                                    if ($_GET['error'] === 'invalid_credentials') {
                                        echo "Invalid username or password.";
                                    } elseif ($_GET['error'] === 'user_not_found') {
                                        echo "User not found.";
                                    }
                                    ?>
                            </div>
                            <?php endif; ?>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-success">Login</button>
                            </div>

                            <div class="text-center">
                                <small>Don't have an account? <a href="register.php">Register</a></small><br>
                                <small><a href="forgot_password.php">Forgot password?</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        let isValid = true;
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        document.getElementById('usernameError').textContent = '';
        document.getElementById('passwordError').textContent = '';

        if (!username) {
            document.getElementById('usernameError').textContent = 'Email is required';
            isValid = false;
        }

        if (!password) {
            document.getElementById('passwordError').textContent = 'Password is required';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    setTimeout(function() {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 5000);
    </script>
</body>

</html>