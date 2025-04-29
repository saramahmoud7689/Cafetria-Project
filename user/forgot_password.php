<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    .error-border {
        border-color: #dc3545;
    }

    .success-border {
        border-color: #28a745;
    }

    .error-message {
        color: red;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    </style>
</head>

<body>
    <div class="bg-light d-flex justify-content-center align-items-center vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-lock text-success" style="font-size: 3rem;"></i>
                                <h1 class="h3 mt-3">Reset Password</h1>
                                <p class="text-muted">Enter your email and new password</p>
                            </div>
                            <form action="reset_password.php" method="POST" id="resetForm">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="email" name="email">
                                        <span id="emailIcon" class="input-group-text text-danger"
                                            style="display: none;">
                                            <i class="bi bi-info-circle"></i>
                                        </span>
                                    </div>
                                    <div id="emailError" class="error-message"></div>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label class="form-label" for="new_password">New Password</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="new_password"
                                            name="new_password">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <span id="passwordIcon" class="input-group-text text-danger"
                                            style="display: none;">
                                            <i class="bi bi-info-circle"></i>
                                        </span>
                                    </div>
                                    <div id="passwordError" class="error-message"></div>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label class="form-label" for="confirm_password">Confirm Password</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="confirm_password"
                                            name="confirm_password">
                                        <button type="button" class="btn btn-outline-secondary"
                                            id="toggleConfirmPassword">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <span id="confirmIcon" class="input-group-text text-danger"
                                            style="display: none;">
                                            <i class="bi bi-info-circle"></i>
                                        </span>
                                    </div>
                                    <div id="confirmError" class="error-message"></div>
                                </div>

                                <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show mb-3">
                                    <?php
                                        if ($_GET['error'] === 'email_not_found') {
                                            echo "Email not found in our system.";
                                        } elseif ($_GET['error'] === 'password_mismatch') {
                                            echo "Passwords do not match.";
                                        } elseif ($_GET['error'] === 'password_too_short') {
                                            echo "Password must be at least 8 characters.";
                                        } elseif ($_GET['error'] === 'password_pattern') {
                                            echo "Password must meet complexity requirements.";
                                        } elseif ($_GET['error'] === 'unknown_error') {
                                            echo "An error occurred. Please try again.";
                                        }
                                        ?>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-success mb-3">
                                    Password has been reset successfully!
                                </div>
                                <?php endif; ?>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-success">Reset Password</button>
                                </div>

                                <div class="text-center">
                                    <small>Remember your password? <a href="login.php">Login</a></small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        let isValid = true;
        const emailField = document.getElementById('email');
        const newPasswordField = document.getElementById('new_password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const emailIcon = document.getElementById('emailIcon');
        const passwordIcon = document.getElementById('passwordIcon');
        const confirmIcon = document.getElementById('confirmIcon');
        const email = emailField.value.trim();
        const newPassword = newPasswordField.value;
        const confirmPassword = confirmPasswordField.value;

        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('confirmError').textContent = '';

        emailField.classList.remove('error-border', 'success-border');
        newPasswordField.classList.remove('error-border', 'success-border');
        confirmPasswordField.classList.remove('error-border', 'success-border');
        emailIcon.style.display = 'none';
        passwordIcon.style.display = 'none';
        confirmIcon.style.display = 'none';

        if (!email) {
            document.getElementById('emailError').textContent = 'Email is required';
            emailField.classList.add('error-border');
            emailIcon.style.display = 'inline';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email';
            emailField.classList.add('error-border');
            emailIcon.style.display = 'inline';
            isValid = false;
        } else {
            emailField.classList.add('success-border');
        }

        if (!newPassword) {
            document.getElementById('passwordError').textContent = 'Password is required';
            newPasswordField.classList.add('error-border');
            passwordIcon.style.display = 'inline';
            isValid = false;
        } else if (newPassword.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters';
            newPasswordField.classList.add('error-border');
            passwordIcon.style.display = 'inline';
            isValid = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(newPassword)) {
            document.getElementById('passwordError').textContent =
                'Password must contain at least one uppercase, one lowercase, one number and one special character';
            newPasswordField.classList.add('error-border');
            passwordIcon.style.display = 'inline';
            isValid = false;
        } else {
            newPasswordField.classList.add('success-border');
        }

        if (!confirmPassword) {
            document.getElementById('confirmError').textContent = 'Please confirm your password';
            confirmPasswordField.classList.add('error-border');
            confirmIcon.style.display = 'inline';
            isValid = false;
        } else if (newPassword !== confirmPassword) {
            document.getElementById('confirmError').textContent = 'Passwords do not match';
            confirmPasswordField.classList.add('error-border');
            confirmIcon.style.display = 'inline';
            isValid = false;
        } else if (newPassword) {
            confirmPasswordField.classList.add('success-border');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    document.getElementById('email').addEventListener('input', function() {
        const email = this.value.trim();
        const emailError = document.getElementById('emailError');
        const emailIcon = document.getElementById('emailIcon');

        this.classList.remove('error-border', 'success-border');
        emailError.textContent = '';
        emailIcon.style.display = 'none';

        if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            this.classList.add('success-border');
        }
    });

    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const passwordError = document.getElementById('passwordError');
        const passwordIcon = document.getElementById('passwordIcon');

        this.classList.remove('error-border', 'success-border');
        passwordError.textContent = '';
        passwordIcon.style.display = 'none';

        if (password && password.length >= 8 &&
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
            this.classList.add('success-border');
        }
    });

    document.getElementById('confirm_password').addEventListener('input', function() {
        const confirmPassword = this.value;
        const newPassword = document.getElementById('new_password').value;
        const confirmError = document.getElementById('confirmError');
        const confirmIcon = document.getElementById('confirmIcon');

        this.classList.remove('error-border', 'success-border');
        confirmError.textContent = '';
        confirmIcon.style.display = 'none';

        if (confirmPassword && newPassword === confirmPassword) {
            this.classList.add('success-border');
        }
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('new_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' :
            '<i class="bi bi-eye-slash-fill"></i>'
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('confirm_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' :
            '<i class="bi bi-eye-slash-fill"></i>'
    });
    </script>
</body>

</html>