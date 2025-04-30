<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .error-message {
        color: red;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .valid-feedback {
        display: none;
        color: #28a745;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .is-valid {
        border-color: #28a745 !important;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .input-group-text.error-icon {
        display: none;
        background-color: transparent;
        border-color: #dc3545;
        color: #dc3545;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-50">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm mt-4">
                    <div class="card-body p-3">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-plus text-success" style="font-size: 3rem;"></i>
                            <h2 class="h3 mt-3">Create Account</h2>
                        </div>
                        <form id="registerForm" action="register_process.php" method="POST"
                            enctype="multipart/form-data">

                            <div class="mb-2">
                                <label for="name" class="form-label">Name</label>
                                <div class="input-group">
                                    <input type="text" id="name" name="name" class="form-control form-control-sm">
                                </div>
                                <div id="nameError" class="error-message">
                                    <?php if (isset($_GET['name_error'])) echo htmlspecialchars($_GET['name_error']); ?>
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <input type="text" id="email" name="email" class="form-control form-control-sm">
                                </div>
                                <div id="emailError" class="error-message">
                                    <?php if (isset($_GET['email_error'])) echo htmlspecialchars($_GET['email_error']); ?>
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>

                            <div class="mb-2 position-relative">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-sm">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                </div>
                                <div id="passwordError" class="error-message">
                                    <?php if (isset($_GET['password_error'])) echo htmlspecialchars($_GET['password_error']); ?>
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>

                            <div class="mb-2 position-relative">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="confirm-password" name="confirm-password"
                                        class="form-control form-control-sm">
                                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                                <div id="confirmPasswordError" class="error-message">
                                    <?php if (isset($_GET['confirm_password_error'])) echo htmlspecialchars($_GET['confirm_password_error']); ?>
                                </div>
                                <div class="valid-feedback">Passwords match!</div>
                            </div>

                            <div class="mb-2">
                                <label for="room" class="form-label">Room No.</label>
                                <div class="input-group">
                                    <select name="room" id="room" class="form-select form-select-sm">
                                        <option value="">Select Room</option>
                                        <option value="Application1">Application1</option>
                                        <option value="Application2">Application2</option>
                                        <option value="Cloud">Cloud</option>
                                    </select>
                                </div>
                                <div id="roomError" class="error-message">
                                    <?php if (isset($_GET['room_error'])) echo htmlspecialchars($_GET['room_error']); ?>
                                </div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>

                            <div class="mb-2">
                                <label for="profile-picture" class="form-label">Profile Picture (Optional)</label>
                                <input type="file" id="profile-picture" name="profile-picture"
                                    class="form-control form-control-sm">
                                <div class="valid-feedback">Looks good!</div>
                            </div>

                            <div class="text-center mt-2">
                                <button type="submit" name="submit" class="btn btn-success btn-sm w-50">Submit</button>
                            </div>

                            <div class="text-center mt-2">
                                <small>Already have an account? <a href="login.php">Login</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let isValid = true;
        const fields = ['name', 'email', 'password', 'confirm-password', 'room'];

        fields.forEach(field => {
            const input = document.getElementById(field);
            const errorElement = document.getElementById(`${field}Error`);
            const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.nextElementSibling :
                input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.parentElement.nextElementSibling :
                null;
            const errorIcon = input.nextElementSibling?.classList?.contains('error-icon') ?
                input.nextElementSibling :
                input.parentElement.querySelector('.error-icon');

            input.classList.remove('is-valid', 'is-invalid');
            if (errorIcon) errorIcon.style.display = 'none';
            if (validFeedback) validFeedback.style.display = 'none';
            if (errorElement) errorElement.textContent = '';
        });

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const room = document.getElementById('room').value;

        if (!name) {
            showError('name', 'Name is required');
            isValid = false;
        } else {
            showValid('name');
        }

        if (!email) {
            showError('email', 'Email is required');
            isValid = false;
        } else if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showError('email', 'Invalid email format');
            isValid = false;
        } else {
            showValid('email');
        }

        if (!password) {
            showError('password', 'Password is required');
            isValid = false;
        } else if (password.length < 8) {
            showError('password', 'Password must be at least 8 characters');
            isValid = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
            showError('password',
                'Password must contain at least one uppercase, one lowercase, one number and one special character'
            );
            isValid = false;
        } else {
            showValid('password');
        }

        if (!confirmPassword) {
            showError('confirm-password', 'Please confirm your password');
            isValid = false;
        } else if (password !== confirmPassword) {
            showError('confirm-password', 'Passwords do not match');
            isValid = false;
        } else {
            showValid('confirm-password');
        }

        if (!room) {
            showError('room', 'Room is required');
            isValid = false;
        } else {
            showValid('room');
        }

        if (!isValid) {
            e.preventDefault();
        }

        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorElement = document.getElementById(`${fieldId}Error`);
            const errorIcon = input.nextElementSibling?.classList?.contains('error-icon') ?
                input.nextElementSibling :
                input.parentElement.querySelector('.error-icon');

            input.classList.add('is-invalid');
            if (errorElement) errorElement.textContent = message;
            if (errorIcon) errorIcon.style.display = 'flex';
        }

        function showValid(fieldId) {
            const input = document.getElementById(fieldId);
            const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.nextElementSibling :
                input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.parentElement.nextElementSibling :
                null;

            input.classList.add('is-valid');
            if (validFeedback) validFeedback.style.display = 'block';
        }
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' :
            '<i class="bi bi-eye-slash-fill"></i>';
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('confirm-password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' :
            '<i class="bi bi-eye-slash-fill"></i>';
    });

    document.querySelectorAll('#registerForm input, #registerForm select').forEach(input => {
        input.addEventListener('input', function() {
            if (this.id === 'name') validateName();
            if (this.id === 'email') validateEmail();
            if (this.id === 'password') validatePassword();
            if (this.id === 'confirm-password') validateConfirmPassword();
            if (this.id === 'room') validateRoom();
        });
    });

    function validateName() {
        const name = document.getElementById('name').value.trim();
        if (!name) {
            showError('name', 'Name is required');
        } else {
            showValid('name');
        }
    }

    function validateEmail() {
        const email = document.getElementById('email').value.trim();
        if (!email) {
            showError('email', 'Email is required');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Invalid email format');
        } else {
            showValid('email');
        }
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        if (!password) {
            showError('password', 'Password is required');
        } else if (password.length < 8) {
            showError('password', 'Password must be at least 8 characters');
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
            showError('password',
                'Password must contain at least one uppercase, one lowercase, one number and one special character');
        } else {
            showValid('password');
            if (document.getElementById('confirm-password').value) {
                validateConfirmPassword();
            }
        }
    }

    function validateConfirmPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        if (!confirmPassword) {
            showError('confirm-password', 'Please confirm your password');
        } else if (password !== confirmPassword) {
            showError('confirm-password', 'Passwords do not match');
        } else {
            showValid('confirm-password');
        }
    }

    function validateRoom() {
        const room = document.getElementById('room').value;
        if (!room) {
            showError('room', 'Room is required');
        } else {
            showValid('room');
        }
    }

    function showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}Error`);
        const errorIcon = input.nextElementSibling?.classList?.contains('error-icon') ?
            input.nextElementSibling :
            input.parentElement.querySelector('.error-icon');
        const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.nextElementSibling :
            input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.parentElement.nextElementSibling :
            null;

        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if (errorElement) errorElement.textContent = message;
        if (errorIcon) errorIcon.style.display = 'flex';
        if (validFeedback) validFeedback.style.display = 'none';
    }

    function showValid(fieldId) {
        const input = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}Error`);
        const errorIcon = input.nextElementSibling?.classList?.contains('error-icon') ?
            input.nextElementSibling :
            input.parentElement.querySelector('.error-icon');
        const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.nextElementSibling :
            input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.parentElement.nextElementSibling :
            null;

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorElement) errorElement.textContent = '';
        if (errorIcon) errorIcon.style.display = 'none';
        if (validFeedback) validFeedback.style.display = 'block';
    }
    </script>
</body>

</html>