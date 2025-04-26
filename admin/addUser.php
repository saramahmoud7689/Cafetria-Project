<?php
include_once '../connect.php';

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

    if (!isset($_FILES['profile-picture']) || $_FILES['profile-picture']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Profile picture is required";
    }

    if (empty($errors)) {
        $emailCheck = mysqli_query($myConnection, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($emailCheck) > 0) {
            $errors[] = "Email already registered";
        }
    }

    if (empty($errors)) {
        $profile_picture = '';
        if (isset($_FILES['profile-picture'])) {
            $target_dir = "uploads/";
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
            } elseif (!move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
                $errors[] = "Error uploading file";
            } else {
                $profile_picture = $target_file;
            }
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, email, password, room, profile_picture, role) 
                      VALUES ('$name', '$email', '$hashed_password', '$room', '$profile_picture', '$role')";

            if (mysqli_query($myConnection, $query)) {
                header("Location: login.php?registration=success");
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
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div id="emailError" class="error-message"></div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control">
                                <div id="passwordError" class="error-message"></div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" id="confirm-password" name="confirm-password"
                                    class="form-control">
                                <div id="confirmPasswordError" class="error-message"></div>
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
                            </div>

                            <div class="mb-3">
                                <label for="profile-picture" class="form-label">Profile Picture</label>
                                <input type="file" id="profile-picture" name="profile-picture" class="form-control">
                                <div id="profilePictureError" class="error-message"></div>
                            </div>

                            <div class="text-center mt-2">
                                <button type="submit" name="submit" class="btn btn-success w-50">Submit</button>
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

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const room = document.getElementById('room').value;
        const role = document.getElementById('role').value;
        const profilePicture = document.getElementById('profile-picture').value;

        document.getElementById('nameError').textContent = '';
        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('confirmPasswordError').textContent = '';
        document.getElementById('roomError').textContent = '';
        document.getElementById('roleError').textContent = '';
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
        } else if (password.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters';
            isValid = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
            document.getElementById('passwordError').textContent =
                'Password must contain at least one uppercase, one lowercase, one number and one special character';
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