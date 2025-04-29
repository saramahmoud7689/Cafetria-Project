<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../user/login.php");
        exit();
    }

    if ( $_SESSION['role'] !== 'admin' ) {
        header("Location: ../unauthorized.php");
        exit();
    }

    include_once '../connect.php';

    $catId = $_GET['catid'];

    $sql = "DELETE FROM categories WHERE id = '$catId'";
    $result = mysqli_query($myConnection, $sql);

    if($result) {
        echo "<div class='alert alert-success text-center m-auto w-50'>Category Deleted Successfully</div>";
        header("Location: category.php");
    } else {
        echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to delete category: " . mysqli_error($myConnection) . "</div>";
    }
?>