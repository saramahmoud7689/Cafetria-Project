<?php
include_once '../connect.php';

session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../user/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? mysqli_real_escape_string($myConnection, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($myConnection, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
    $room = isset($_POST['room']) ? mysqli_real_escape_string($myConnection, $_POST['room']) : '';
    $role = isset($_POST['role']) ? mysqli_real_escape_string($myConnection, $_POST['role']) : 'user';
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (preg_match('/[اأإء-ي]/u', $email)) {
        $errors[] = "Email cannot contain Arabic characters";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character";
    }

    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (empty($room)) {
        $errors[] = "Room is required";
    }

    if (empty($errors)) {
        $emailCheck = mysqli_query($myConnection, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($emailCheck) > 0) {
            $errors[] = "Email already registered";
        }
    }

    if (empty($errors)) {
        $profile_picture = null;
        if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../user/uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = basename($_FILES['profile-picture']['name']);
            $target_file = $target_dir . uniqid() . '_' . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['profile-picture']['tmp_name']);
            if ($check === false) {
                $errors[] = "File is not an image";
            } elseif ($_FILES['profile-picture']['size'] > 2000000) {
                $errors[] = "File is too large (max 2MB)";
            } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
            } elseif (move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
                $profile_picture = $target_file;
            } else {
                $errors[] = "Error uploading file";
            }
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($profile_picture) {
                $query = "INSERT INTO users (name, email, password, room, profile_picture, role) 
                          VALUES ('$name', '$email', '$hashed_password', '$room', '$profile_picture', '$role')";
            } else {
                $query = "INSERT INTO users (name, email, password, room, role) 
                          VALUES ('$name', '$email', '$hashed_password', '$room', '$role')";
            }

            if (mysqli_query($myConnection, $query)) {
                $successMessage = "User added successfully!";
                header("Location: listAllUsers.php?success=true");
                exit();
            } else {
                $errors[] = "Database error: " . mysqli_error($myConnection);
            }

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add User</title>
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

<body class="bg-light ">
    <div class="page-container row">
        <aside class="col-md-2 col-12">
            <div class="d-flex flex-column p-3">
                <h4 class="text-center mb-4">Admin Panel</h4>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="../user/home.php" class="nav-link">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../product/listproducts.php" class="nav-link">
                            Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../admin/listAllUsers.php" class="nav-link">
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../order/adminlistorders.php" class="nav-link">
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/make_order.php" class="nav-link">
                            Manual Order
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../order/checks.php" class="nav-link">
                            Checks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../category/category.php" class="nav-link">
                            Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/logout.php" class="nav-link">
                            LogOut
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <div class="col-md-8 col-12">
            <div class="card shadow-sm my-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Add User</h3>

                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-3">
                        <?php echo htmlspecialchars(implode('<br>', $errors)); ?>
                    </div>
                    <?php endif; ?>

                    <form id="registerForm" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            <div id="nameError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div id="emailError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <div id="passwordError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="confirm-password" name="confirm-password"
                                    class="form-control">
                                <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <div id="confirmPasswordError" class="error-message"></div>
                            <div class="valid-feedback">Passwords match!</div>
                        </div>

                        <div class="mb-3">
                            <label for="room" class="form-label">Room No.</label>
                            <select name="room" id="room" class="form-select">
                                <option value="">Select Room</option>
                                <option value="Application1"
                                    <?php echo (isset($_POST['room']) && $_POST['room'] == 'Application1') ? 'selected' : ''; ?>>
                                    Application1</option>
                                <option value="Application2"
                                    <?php echo (isset($_POST['room']) && $_POST['room'] == 'Application2') ? 'selected' : ''; ?>>
                                    Application2</option>
                                <option value="Cloud"
                                    <?php echo (isset($_POST['room']) && $_POST['room'] == 'Cloud') ? 'selected' : ''; ?>>
                                    Cloud</option>
                            </select>
                            <div id="roomError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">User Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="user"
                                    <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>
                                    User</option>
                                <option value="admin"
                                    <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>
                                    Admin</option>
                            </select>
                            <div id="roleError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="mb-3">
                            <label for="profile-picture" class="form-label">Profile Picture (Optional)</label>
                            <input type="file" id="profile-picture" name="profile-picture" class="form-control">
                            <div id="profilePictureError" class="error-message"></div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>

                        <div class="text-center mt-2">
                            <button type="submit" name="submit" class="btn btn-success w-50">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let isValid = true;
        const fields = ['name', 'email', 'password', 'confirm-password', 'room', 'role'];

        fields.forEach(field => {
            const input = document.getElementById(field);
            const errorElement = document.getElementById(`${field}Error`);
            const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.nextElementSibling :
                input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
                input.parentElement.nextElementSibling :
                null;

            input.classList.remove('is-valid', 'is-invalid');
            if (validFeedback) validFeedback.style.display = 'none';
            if (errorElement) errorElement.textContent = '';
        });

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const room = document.getElementById('room').value;
        const role = document.getElementById('role').value;

        if (!name) {
            showError('name', 'Name is required');
            isValid = false;
        } else {
            showValid('name');
        }

        if (!email) {
            showError('email', 'Email is required');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Invalid email format');
            isValid = false;
        } else if (/[اأإء-ي]/.test(email)) {
            showError('email', 'Email cannot contain Arabic characters');
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

        if (!role) {
            showError('role', 'Role is required');
            isValid = false;
        } else {
            showValid('role');
        }

        if (!isValid) {
            e.preventDefault();
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
            if (this.id === 'role') validateRole();
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
        } else if (/[اأإء-ي]/.test(email)) {
            showError('email', 'Email cannot contain Arabic characters');
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

    function validateRole() {
        const role = document.getElementById('role').value;
        if (!role) {
            showError('role', 'Role is required');
        } else {
            showValid('role');
        }
    }

    function showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}Error`);
        const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.nextElementSibling :
            input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.parentElement.nextElementSibling :
            null;

        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if (errorElement) errorElement.textContent = message;
        if (validFeedback) validFeedback.style.display = 'none';
    }

    function showValid(fieldId) {
        const input = document.getElementById(fieldId);
        const errorElement = document.getElementById(`${fieldId}Error`);
        const validFeedback = input.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.nextElementSibling :
            input.parentElement.nextElementSibling?.classList?.contains('valid-feedback') ?
            input.parentElement.nextElementSibling :
            null;

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorElement) errorElement.textContent = '';
        if (validFeedback) validFeedback.style.display = 'block';
    }
    </script>
</body>

</html>