<?php
session_start();
include('../connect.php');

if (isset($_POST['confirm_order'])) {

    $notes = mysqli_real_escape_string($myConnection, $_POST['notes']);
    $room = mysqli_real_escape_string($myConnection, $_POST['room']);
    $total_cost = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
    $order_date = date('Y-m-d H:i:s');
    $status = 'Processing'; 

    //TODO
    // if(!$user_id){
    //     //alert to login
    // }

                //just to test admin order
                // $_SESSION['user_id'] = 2;
    $user_session = $_SESSION['user_id'];
                // echo $user_session;
                // echo $_SESSION['user_id'];

    $query = "SELECT * FROM users WHERE id = $user_session";
    $myuser = mysqli_query($myConnection, $query);
            
                // print_r($myuser);
    $user = mysqli_fetch_assoc($myuser);
                // echo $user['role'];
    if($user['role'] == 'admin'){
        // if(!$_SESSION['user_id_a']){
            //TODO alert to choose user then redirect to the same page or break
        // }
        $user_id = $_SESSION['user_id_a'];
                    // echo "admin ".$user_id;
    }else{
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
                    // echo "user".$user_id;
    }
    
    
 
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

        $insertOrderQuery = "INSERT INTO orders (user_id, order_date, status, notes, total_cost, room)
                            VALUES ($user_id, '$order_date', '$status', '$notes', $total_cost, '$room')";

        if (mysqli_query($myConnection, $insertOrderQuery)) {

            $order_id = mysqli_insert_id($myConnection);

            // if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $product_id = $item['id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];

                    $insertDetailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price)
                                        VALUES ($order_id, $product_id, $quantity, $price)";
                    mysqli_query($myConnection, $insertDetailQuery);
                }
            // }

            unset($_SESSION['cart']);
            unset($_SESSION['total']);

            header('Location: make_order.php');
            exit();

        } else {
            echo "Error placing order: " . mysqli_error($myConnection);
        }
    }else{
        //TODO alert on the same page 
        echo "Cart is empty! you should select at least one product";
    }
} else {
    echo "Invalid access.";
}
?>
