<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
session_start();

include_once "../connect.php";

function getProductQuantity($productId) {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] == $productId) {
            return $item['quantity'];
        }
    }
    return 0;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manual Order - Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<!-- <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
    <a class="navbar-brand" href="#">Cafeteria</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Products</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Manual Order</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Checks</a></li>
        </ul>
        <span class="navbar-text me-3">
            Admin
        </span>
        <img src="https://via.placeholder.com/30" class="rounded-circle" alt="Admin">
    </div>
</nav> -->

<!-- Main Content -->
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Cart Section -->
        <div class="col-md-4">
            <h5>Order Summary</h5>
            <div id="cart">
                <?php

                    // Check if cart is not empty
                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $pid = $item['id']; // Product ID from session
                            $productQuantity = $item['quantity']; // Quantity from session
                            $productPrice = $item['price']; // Product price (you can store price with the item when adding to the cart)
                            $productName = $item['name']; // Product name (you can store the name too when adding to the cart)

                            // Display product in cart
                            echo "<div class='d-flex justify-content-between mb-2'>
                                <span>$productName</span>  <!-- product name -->
                                <div> 
                                    <a href='cart.php?action=decrease&orderitem=$pid&price=$productPrice' class='btn btn-sm btn-outline-secondary'>-</a> <!-- minus quantity -->
                                    <input type='text' value='$productQuantity' size='1' readonly> <!-- product quantity -->
                                    <a href='cart.php?action=increase&orderitem=$pid&price=$productPrice' class='btn btn-sm btn-outline-secondary'>+</a> <!-- plus quantity -->
                                    EGP $productPrice <!-- product price -->
                                    <a href='cart.php?action=delete&orderitem=$pid&price=$productPrice' class='btn btn-sm btn-danger'>X</a> <!-- delete item -->
                                </div>
                            </div>";
                        }
                    } else {
                        echo "Your cart is empty.";
                    }
                    // $query = "SELECT * FROM products";
                    // $myproducts = mysqli_query($myConnection, $query);
                    
                    // while($product = mysqli_fetch_assoc($myproducts)) {
                    //     $pname = $product['name'];
                    //     $pprice = $product['price'];
                    //     $pid = $product['id'];
                    //     $productQuantity = getProductQuantity($pid);
                    
                    //     echo "<div class='d-flex justify-content-between mb-2'>
                    //         <span>$pname</span>  <!-- product name -->
                    //         <div> 
                    //             <a href='cart.php?action=decrease&orderitem=$pid&price=$pprice' class='btn btn-sm btn-outline-secondary'>-</a> <!-- minus quantity -->
                    //             <input type='text' value='$productQuantity' size='1' readonly> <!-- product quantity -->
                    //             <a href='cart.php?action=increase&orderitem=$pid&price=$pprice' class='btn btn-sm btn-outline-secondary'>+</a> <!-- plus quantity -->
                    //             EGP $pprice <!-- product price -->
                    //             <a href='cart.php?action=delete&orderitem=$pid&price=$pprice' class='btn btn-sm btn-danger'>X</a> <!-- delete item -->
                    //         </div>
                    //     </div>";
                    // }
                ?>
            </div>
            <form method="POST" action="confirm_order.php">
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="room" class="form-label">Room</label>
                    <select class="form-select" id="room" name="room">
                        <option value="">Select Room</option>
                        <option value="Application1">Application1</option>
                        <option value="Application2">Application2</option>
                        <option value="Cloud">Cloud</option>
                    </select>
                </div>
                <h4>Total: EGP 
                    <?php
                        $total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
                        echo $total;
                    ?>
                </h4>
                <button type="submit" class="btn btn-primary w-100" name="confirm_order">Confirm</button>
            </form>
        </div>

        <!-- Right Product Section -->
        <div class="col-md-8">

        
            <!-- at admin view -->
            <div class="mb-3">
            <h4>Add to user</h4>
            <form method="POST" action="">
                <div class="row">
                <select class="form-select" id="user" name="user_id">
                    <option>Choose User</option>
                    <?php
                        $query = "SELECT * FROM users";
                        $myusers = mysqli_query($myConnection, $query);

                        while($user = mysqli_fetch_assoc($myusers)){
                            $name = $user['name'];
                            $user_id = $user['id']; 

                            echo "<option value='$user_id'>$name</option>";
                        }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary mt-2">Set User</button>
                <?php
                    if (isset($_POST['user_id'])) {
                        $_SESSION['user_id_a'] = $_POST['user_id'];
                    }

                ?>
                </div>
            </form>
            </div>

            <!-- at user view -->
             <h4>latest order</h4>
             <!-- show latest order -->
              <?php
                $query = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 1"; // Assuming 'created_at' is the date column
                $myorders = mysqli_query($myConnection, $query);
                
                if (!$myorders) {
                    echo "Error: " . mysqli_error($myConnection);
                } elseif (mysqli_num_rows($myorders) == 0) {
                    echo "No orders found.";
                } else {
                    $latestOrder = mysqli_fetch_assoc($myorders);
                
                    // print_r($myorders);
                    // print_r($latestOrder);
    
                    $orderId = $latestOrder['id'];

                    $query = "SELECT p.name, p.image, p.price, p.id, od.quantity 
                    FROM products p 
                    JOIN order_details od ON p.id = od.product_id
                    JOIN orders o ON o.id = od.order_id
                    WHERE od.order_id = $orderId
                    ORDER BY o.order_date DESC";

                    $myproducts = mysqli_query($myConnection, $query);


                    // Loop through the products and display them
                    while($product = mysqli_fetch_assoc($myproducts)) {
                        $pname = $product['name'];
                        $pimage = $product['image'];
                        $pprice = $product['price'];
                        $pid = $product['id'];
                        $quantity = $product['quantity'];  // Quantity of the product in this order

                        echo "<a href='cart.php?action=add&orderitem=$pid&price=$pprice&name=$pname&image=$pimage' class='btn col-3 text-center mb-4'>";
                        echo '<div>';
                        echo "<img src='$pimage' alt='Product' class='img-fluid'>";
                        echo "<h5>$pname</h5>";
                        echo "<p class='badge bg-primary'>$$pprice</p>";
                        echo "<p>Quantity: $quantity</p>";
                        echo '</div>';
                        echo '</a>';
                    }
                }
                

                
              ?>

            <div class="row">

            <?php
                $query = "SELECT * FROM products";
                $myproducts = mysqli_query($myConnection, $query);

                while($product = mysqli_fetch_assoc($myproducts)) {
                    $pname = $product['name'];
                    $pimage = $product['image'];
                    $pprice = $product['price'];
                    $pid = $product['id'];

                    echo "<a href='cart.php?action=add&orderitem=$pid&price=$pprice&name=$pname&image=$pimage' class='btn col-3 text-center mb-4'>";
                    echo '<div>';
                    echo "<img src='$pimage' alt='Product' class='img-fluid'>";
                    echo "<h5>$pname</h5>";
                    echo "<p class='badge bg-primary'>$$pprice</p>";
                    echo '</div>';
                    echo '</a>';
                }
            ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
