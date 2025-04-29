<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../user/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include_once("../connect.php");

if (isset($_GET["userid"])) {
    $id = mysqli_real_escape_string($myConnection, $_GET["userid"]);

    $checkQuery = "SELECT * FROM users WHERE id = $id";
    $checkResult = mysqli_query($myConnection, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $query = "DELETE FROM users WHERE id = $id";
        $result = mysqli_query($myConnection, $query);

        if ($result) {
            $_SESSION['success_message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting user: " . mysqli_error($myConnection);
        }
    } else {
        $_SESSION['error_message'] = "User not found.";
    }
}

header("Location: listAllUsers.php");
exit();
?>