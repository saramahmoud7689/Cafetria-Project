<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../user/login.php");
    exit();
}

if ( $_SESSION['role'] !== 'admin' ) {
    header("Location: ../unauthorized.php");
    exit();
}

include_once("../connect.php"); 

if(isset($_GET["userid"])) {
    $id = mysqli_real_escape_string($myConnection, $_GET["userid"]);
    $query = "DELETE FROM users WHERE id = $id";
    $result = mysqli_query($myConnection, $query);
    
    if(!$result) {
        die("Error: " . mysqli_error($myConnection));
    }
}

header("Location: listAllUsers.php");
exit();
?>