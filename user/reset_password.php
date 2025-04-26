<?php
include_once('../connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($myConnection, $_POST['email']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        header("Location: forgot_password.php?error=password_mismatch");
        exit();
    }

    if (empty($newPassword)) {
        header("Location: forgot_password.php?error=password_empty");
        exit();
    } elseif (strlen($newPassword) < 8) {
        header("Location: forgot_password.php?error=password_too_short");
        exit();
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        header("Location: forgot_password.php?error=password_pattern");
        exit();
    }

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($myConnection, $query);

    if (mysqli_num_rows($result) == 0) {
        header("Location: forgot_password.php?error=email_not_found");
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET password='$hashedPassword' WHERE email='$email'";

    if (mysqli_query($myConnection, $updateQuery)) {
        $subject = "Your password has been reset";
        $message = "Hello,\n\nYour password has been successfully reset. If you didn't request this change, please contact us immediately.";
        $headers = "From: marwa.nasser8133@gmail.com";
        mail($email, $subject, $message, $headers);

        header("Location: login.php?reset_success=1");
        exit();
    } else {
        header("Location: forgot_password.php?error=unknown_error");
        exit();
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
?>