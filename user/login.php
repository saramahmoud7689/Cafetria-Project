<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    .form-control.error-border {
        border-color: red;
    }

    .login-card {
        max-width: 450px;
    }

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
                        <div class="text-center mb-4">
                            <i class="bi bi-person-circle text-success" style="font-size: 3rem;"></i>
                            <h3 class="text-center mb-4">Login</h3>
                        </div>
                        <form id="loginForm" action="login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" id="email" name="email">
                                    <span id="emailIcon" class="input-group-text text-danger" style="display: none;">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </div>
                                <div id="emailError" class="error-message"></div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <span id="passwordIcon" class="input-group-text text-danger" style="display: none;">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                </div>
                                <div id="passwordError" class="error-message"></div>
                            </div>

                            <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger mb-3">
                                <?php
                                    if ($_GET['error'] === 'invalid_credentials') {
                                        echo "Invalid email or password.";
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
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';

        document.getElementById('email').classList.remove('error-border');
        document.getElementById('password').classList.remove('error-border');
        document.getElementById('emailIcon').style.display = 'none';
        document.getElementById('passwordIcon').style.display = 'none';

        if (!email) {
            document.getElementById('emailError').textContent = 'Email is required';
            document.getElementById('email').classList.add('error-border');
            document.getElementById('emailIcon').style.display = 'inline';
            isValid = false;
        } else if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            document.getElementById('emailError').textContent = 'Invalid email format';
            document.getElementById('email').classList.add('error-border');
            document.getElementById('emailIcon').style.display = 'inline';
            isValid = false;
        }

        if (!password) {
            document.getElementById('passwordError').textContent = 'Password is required';
            document.getElementById('password').classList.add('error-border');
            document.getElementById('passwordIcon').style.display = 'inline';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    document.getElementById('email').addEventListener('input', function() {
        const email = this.value.trim();
        const emailError = document.getElementById('emailError');

        this.classList.remove('error-border');
        emailError.textContent = '';
        document.getElementById('emailIcon').style.display = 'none';

        if (email && !/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            document.getElementById('emailIcon').style.display = 'inline';
        }
    });

    document.getElementById('password').addEventListener('input', function() {
        const password = this.value.trim();
        const passwordError = document.getElementById('passwordError');

        this.classList.remove('error-border');
        passwordError.textContent = '';
        document.getElementById('passwordIcon').style.display = 'none';

    });

    setTimeout(function() {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 5000);

    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' :
            '<i class="bi bi-eye-slash-fill"></i>';
    });
    </script>
</body>

</html>