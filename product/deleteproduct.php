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
    
    include_once '../connect.php';
    $productid = $_GET['productid'];
    $sql = "DELETE FROM products WHERE id = '$productid'";
    $result = mysqli_query($myConnection, $sql);

    if($result) {
        echo "<div class='alert alert-success text-center m-auto w-50'>Product Deleted Successfully</div>";
        header("Location: listproducts.php");
    } else {
        echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to delete product: " . mysqli_error($myConnection) . "</div>";
    }

?>