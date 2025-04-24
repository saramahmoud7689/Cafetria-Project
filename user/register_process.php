<?php
include_once '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    $name = isset($_POST['name']) ? mysqli_real_escape_string($myConnection, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($myConnection, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
    $room = isset($_POST['room']) ? mysqli_real_escape_string($myConnection, $_POST['room']) : '';
    $ext = isset($_POST['ext']) ? mysqli_real_escape_string($myConnection, $_POST['ext']) : '';
    
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
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($room)) {
        $errors[] = "Room is required";
    }
    
    if (empty($ext)) {
        $errors[] = "Ext. is required";
    } elseif (!preg_match('/^\d+$/', $ext)) {
        $errors[] = "Ext. must be a number";
    }
    
    if (!isset($_FILES['profile-picture']) || $_FILES['profile-picture']['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Profile picture is required";
    }
    
    if (!empty($errors)) {
        header("Location: Register.php?error=" . urlencode(implode(', ', $errors)));
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
            header("Location: Register.php?error=File is not an image");
            exit();
        }
        
        if ($_FILES['profile-picture']['size'] > 2000000) {
            header("Location: Register.php?error=File is too large (max 2MB)");
            exit();
        }
        
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            header("Location: Register.php?error=Only JPG, JPEG, PNG & GIF files are allowed");
            exit();
        }
        
        if (!move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
            header("Location: Register.php?error=Error uploading file");
            exit();
        }
        
        $profile_picture = $target_file;
    }

    $userType = 'user';
    $checkAdmin = mysqli_query($myConnection, "SELECT COUNT(*) as count FROM users WHERE user_type='admin'");
    $adminCount = mysqli_fetch_assoc($checkAdmin)['count'];
    if ($adminCount == 0) {
        $userType = 'admin';
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (name, email, password, room, ext, profile_picture, user_type) 
            VALUES ('$name', '$email', '$hashed_password', '$room', '$ext', '$profile_picture', '$userType')";
    
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