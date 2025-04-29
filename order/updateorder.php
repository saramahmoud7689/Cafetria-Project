<?php
    session_start();
    include_once '../connect.php';

    if (!isset($_SESSION['user_name'])) {
        header("Location: ../user/login.php");
        exit();
    }

    //TODO to check if user is the owner of the order

    $order_id = intval($_GET['orderid']);
    $status = $_GET['status'];
    $user_session = $_SESSION['user_id'];
    $query = "SELECT * FROM orders WHERE id = $order_id";
    $myorder = mysqli_query($myConnection, $query);
    $order = mysqli_fetch_assoc($myorder);
    echo $order['user_id'];
    echo $user_session;
    if ($order['user_id'] != $user_session) {
        // header("Location: ../unauthorized.php");
        echo "should login";
        // exit();
    }

    if ( $_SESSION['role'] !== 'admin' ) {
        // header("Location: ../unauthorized.php");
        // exit();
        echo "should be admin";
    }
    
 

    
    // echo "order id : ".$order_id;
    // echo "statue : ".$status;
    
    //TODO update don't work

    // $stmt = mysqli_prepare($myConnection, "UPDATE orders SET status = ? WHERE id = ?");
    // mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    // mysqli_stmt_execute($stmt);
    // mysqli_stmt_close($stmt);


    $getQuery = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $getResult = mysqli_query($myConnection, $getQuery);
    if ($getResult) {
        header("Location: adminlistorders.php");
    } else {
        echo "Failed to update order status.";
    }

?>
