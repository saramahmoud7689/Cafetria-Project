<?php
include_once('../connect.php');

require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        $subject = "Password Reset Confirmation";
        $message = "
        <html>
        <head>
            <title>Password Reset Successful</title>
        </head>
        <body>
            <h2>Password Reset Successful</h2>
            <p>Hello,</p>
            <p>Your password has been successfully reset. If you didn't request this change, please contact our support team immediately.</p>
            <p>Thank you,<br>Your App Team</p>
        </body>
        </html>
        ";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'node.app8133@gmail.com';
            $mail->Password = 'slay zlev kvio bnvi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('node.app8133@gmail.com', 'Cafetria');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            header("Location: forgot_password.php?error=mail_failed");
            exit();
        }

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