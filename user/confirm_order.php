<?php
session_start();
include('../connect.php');

unset($_SESSION['message']);

if (isset($_POST['confirm_order'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = '<div class="alert alert-warning text-center" role="alert">
                                    You must login first! <a href="login.php" class="alert-link">Go to Login</a>.
                                </div>';
    } else {
        $notes = mysqli_real_escape_string($myConnection, $_POST['notes']);
        $room = mysqli_real_escape_string($myConnection, $_POST['room']);
        $total_cost = $_SESSION['total'] ?? 0;
        $order_date = date('Y-m-d H:i:s');
        $status = 'Processing';
        
        //just to test admin order
                $_SESSION['user_id'] = 1;
        $user_session = $_SESSION['user_id'];

        $query = "SELECT * FROM users WHERE id = $user_session";
        $myuser = mysqli_query($myConnection, $query);
        $user = mysqli_fetch_assoc($myuser);

        if ($user['role'] == 'admin') {
            if (!isset($_SESSION['user_id_a']) || empty($_SESSION['user_id_a'])) {
                $_SESSION['message'] = '<div class="alert alert-danger text-center" role="alert">
                                            Please choose a user before confirming the order.
                                        </div>';
            } else {
                $user_id = $_SESSION['user_id_a'];
            }
        } else {
            $user_id = $user_session;
        }

        if (!isset($_SESSION['message'])) {
            if (!empty($_SESSION['cart'])) {
                $insertOrderQuery = "INSERT INTO orders (user_id, order_date, status, notes, total_cost, room)
                                    VALUES ($user_id, '$order_date', '$status', '$notes', $total_cost, '$room')";

                if (mysqli_query($myConnection, $insertOrderQuery)) {
                    $order_id = mysqli_insert_id($myConnection);

                    foreach ($_SESSION['cart'] as $item) {
                        $product_id = $item['id'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];

                        $insertDetailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price)
                                              VALUES ($order_id, $product_id, $quantity, $price)";
                        mysqli_query($myConnection, $insertDetailQuery);
                    }

                    unset($_SESSION['cart']);
                    unset($_SESSION['total']);

                    $_SESSION['message'] = '<div class="alert alert-success text-center" role="alert">
                                                Order placed successfully!
                                            </div>';
                } else {
                    $_SESSION['message'] = '<div class="alert alert-danger text-center" role="alert">
                                                Error placing order: ' . mysqli_error($myConnection) . '
                                            </div>';
                }
            } else {
                $_SESSION['message'] = '<div class="alert alert-warning text-center" role="alert">
                                            Your cart is empty! Please select at least one product.
                                        </div>';
            }
        }
    }
}

header('Location: make_order.php');
exit();
?>