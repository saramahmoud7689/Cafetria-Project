<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    if ( !isset($_SESSION['user_name']) ) {
        header("Location: ../user/login.php");
        exit();
    }

    if ( $_SESSION['role'] !== 'admin' ) {
        header("Location: ../unauthorized.php");
        exit();
    }



    include_once '../connect.php';

    //TODO to check if user is the owner of the order

    $order_id = intval($_GET['orderid']);
    $status = $_GET['status'];
    $user_session = $_SESSION['user_id'];
    $query = "SELECT * FROM orders WHERE id = $order_id";
    $myorder = mysqli_query($myConnection, $query);
    $order = mysqli_fetch_assoc($myorder);
    echo $order['user_id'];
    echo $user_session;

    $getQuery = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $getResult = mysqli_query($myConnection, $getQuery);
    if ($getResult) {
        echo "Order status updated successfully.";
        header("Location: ../order/adminlistorders.php");
        
    } else {
        echo "Failed to update order status.";
    }

?>
