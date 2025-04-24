<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-50">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm mt-4">
                    <div class="card-body p-3">
                        <h3 class="text-center fs-4 mb-3">User Registration</h3>
                        <form id="registerForm" action="register_process.php" method="POST"
                            enctype="multipart/form-data">

                            <div class="mb-2">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control form-control-sm">
                                <div id="nameError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" id="email" name="email" class="form-control form-control-sm">
                                <div id="emailError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-sm">
                                <div id="passwordError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" id="confirm-password" name="confirm-password"
                                    class="form-control form-control-sm">
                                <div id="confirmPasswordError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="room" class="form-label">Room No.</label>
                                <select name="room" id="room" class="form-select form-select-sm">
                                    <option value="">Select Room</option>
                                    <option value="Application1">Application1</option>
                                    <option value="Application2">Application2</option>
                                    <option value="Cloud">Cloud</option>
                                </select>
                                <div id="roomError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="ext" class="form-label">Ext.</label>
                                <input type="text" id="ext" name="ext" class="form-control form-control-sm">
                                <div id="extError" class="error-message"></div>
                            </div>

                            <div class="mb-2">
                                <label for="profile-picture" class="form-label">Profile Picture</label>
                                <input type="file" id="profile-picture" name="profile-picture"
                                    class="form-control form-control-sm">
                                <div id="profilePictureError" class="error-message"></div>
                            </div>

                            <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger mb-2">
                                <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                            <?php endif; ?>

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
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            let isValid = true;

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const room = document.getElementById('room').value;
            const ext = document.getElementById('ext').value.trim();
            const profilePicture = document.getElementById('profile-picture').value;

            document.getElementById('nameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('confirmPasswordError').textContent = '';
            document.getElementById('roomError').textContent = '';
            document.getElementById('extError').textContent = '';
            document.getElementById('profilePictureError').textContent = '';

            if (!name) {
                document.getElementById('nameError').textContent = 'Name is required';
                isValid = false;
            }

            if (!email) {
                document.getElementById('emailError').textContent = 'Email is required';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').textContent = 'Invalid email format';
                isValid = false;
            }

            if (!password) {
                document.getElementById('passwordError').textContent = 'Password is required';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
                isValid = false;
            }

            if (!confirmPassword) {
                document.getElementById('confirmPasswordError').textContent = 'Please confirm your password';
                isValid = false;
            } else if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                isValid = false;
            }

            if (!room) {
                document.getElementById('roomError').textContent = 'Room is required';
                isValid = false;
            }

            if (!ext) {
                document.getElementById('extError').textContent = 'Ext. is required';
                isValid = false;
            } else if (!/^\d+$/.test(ext)) {
                document.getElementById('extError').textContent = 'Ext. must be a number';
                isValid = false;
            }

            if (!profilePicture) {
                document.getElementById('profilePictureError').textContent = 'Profile picture is required';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>