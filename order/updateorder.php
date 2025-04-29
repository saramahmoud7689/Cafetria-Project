<?php
    session_start();
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../user/login.php");
        exit();
    }

    //TODO to check if user is the owner of the order
    if ( $_SESSION['role'] !== 'admin' ) {
        header("Location: ../unauthorized.php");
        exit();
    }
    
    include_once '../connect.php';

    $order_id = $_GET['orderid'];
    $status = $_GET['status'];
    
    $getQuery = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $getResult = mysqli_query($myConnection, $getQuery);
    if ($getResult) {
        header("Location: adminlistorders.php");
    }
?>