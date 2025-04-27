<?php
session_start();
include('../connect.php');

if (isset($_POST['confirm_order'])) {

    $notes = mysqli_real_escape_string($myConnection, $_POST['notes']);
    $room = mysqli_real_escape_string($myConnection, $_POST['room']);
    $total_cost = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // make sure you store logged-in user id in session
    $order_date = date('Y-m-d H:i:s');
    $status = 'Processing'; 
 
    $insertOrderQuery = "INSERT INTO orders (user_id, order_date, status, notes, total_cost, room)
                         VALUES ($user_id, '$order_date', '$status', '$notes', $total_cost, '$room')";

    if (mysqli_query($myConnection, $insertOrderQuery)) {

        $order_id = mysqli_insert_id($myConnection);

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $product_id = $item['id'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $insertDetailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price)
                                      VALUES ($order_id, $product_id, $quantity, $price)";
                mysqli_query($myConnection, $insertDetailQuery);
            }
        }

        unset($_SESSION['cart']);
        unset($_SESSION['total']);

        header('Location: make_order.php');
        exit();

    } else {
        echo "Error placing order: " . mysqli_error($myConnection);
    }
} else {
    echo "Invalid access.";
}
?>
