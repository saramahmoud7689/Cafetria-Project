<?php
include_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? mysqli_real_escape_string($myConnection, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($myConnection, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
    $room = isset($_POST['room']) ? mysqli_real_escape_string($myConnection, $_POST['room']) : '';

    $errors = [];

    if (empty($name)) {
        $errors['name_error'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email_error'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email_error'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password_error'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password_error'] = "Password must be at least 8 characters";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors['password_error'] = "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password_error'] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password_error'] = "Passwords do not match";
    }

    if (empty($room)) {
        $errors['room_error'] = "Room is required";
    }

    if (!isset($_FILES['profile-picture']) || $_FILES['profile-picture']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors['profile_picture_error'] = "Profile picture is required";
    }

    if (!empty($errors)) {
        $query_string = http_build_query($errors);
        header("Location: Register.php?" . $query_string);
        exit();
    }

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
            header("Location: Register.php?profile_picture_error=File is not an image");
            exit();
        }

        if ($_FILES['profile-picture']['size'] > 2000000) {
            header("Location: Register.php?profile_picture_error=File is too large (max 2MB)");
            exit();
        }

        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            header("Location: Register.php?profile_picture_error=Only JPG, JPEG, PNG & GIF files are allowed");
            exit();
        }

        if (!move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
            header("Location: Register.php?profile_picture_error=Error uploading file");
            exit();
        }

        $profile_picture = $target_file;
    }

    $role = 'user';
    $checkAdmin = mysqli_query($myConnection, "SELECT COUNT(*) as count FROM users WHERE role='admin'");
    $adminCount = mysqli_fetch_assoc($checkAdmin)['count'];
    if ($adminCount == 0) {
        $role = 'admin';
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password, room, profile_picture, role) 
            VALUES ('$name', '$email', '$hashed_password', '$room', '$profile_picture', '$role')";

    if (mysqli_query($myConnection, $query)) {
        header("Location: login.php?registration=success");
        exit();
    } else {
        header("Location: Register.php?error=" . urlencode("Database error: " . mysqli_error($myConnection)));
        exit();
    }
} else {
    header("Location: Register.php");
    exit();
}
?>