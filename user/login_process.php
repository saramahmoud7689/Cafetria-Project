<?php
include_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($myConnection, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($myConnection, $query);

    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_picture'] = $user['profile_picture'];

        if ($user['role'] == 'admin') {
            header("Location: home.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>