<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="text-center mb-4">Reset Password</h1>
                            <form action="reset_password.php" method="POST" id="resetForm">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input class="form-control" type="text" id="email" name="email">
                                    <div id="emailError" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="new_password">New Password</label>
                                    <input class="form-control" type="password" id="new_password" name="new_password">
                                    <div id="passwordError" class="error-message"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="confirm_password">Confirm Password</label>
                                    <input class="form-control" type="password" id="confirm_password"
                                        name="confirm_password">
                                    <div id="confirmError" class="error-message"></div>
                                </div>

                                <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger mb-3">
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
        const email = document.getElementById('email').value.trim();
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('confirmError').textContent = '';

        if (!email) {
            document.getElementById('emailError').textContent = 'Email is required';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email';
            isValid = false;
        }

        if (!newPassword) {
            document.getElementById('passwordError').textContent = 'Password is required';
            isValid = false;
        } else if (newPassword.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters';
            isValid = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(newPassword)) {
            document.getElementById('passwordError').textContent =
                'Password must contain at least one uppercase, one lowercase, one number and one special character';
            isValid = false;
        }

        if (!confirmPassword) {
            document.getElementById('confirmError').textContent = 'Please confirm your password';
            isValid = false;
        } else if (newPassword !== confirmPassword) {
            document.getElementById('confirmError').textContent = 'Passwords do not match';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
    </script>
</body>

</html>