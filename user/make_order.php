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

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); 
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
<div class="page-container row">
    <aside class="col-md-2 col-12">

                    <?php

                        if(isset($_SESSION['user_id'])){
                            $user_session = $_SESSION['user_id'];
                        

                            $query = "SELECT * FROM users WHERE id = $user_session";
                            $myuser = mysqli_query($myConnection, $query);

                            $user = mysqli_fetch_assoc($myuser);

                            if($user['role'] == 'admin'){
                                echo "<div class='d-flex flex-column p-3'>
                                        <h4 class='text-center mb-4'>Admin Panel</h4>
                                        <ul class='nav nav-pills flex-column mb-auto'>
                                            <li class='nav-item'>
                                                <a href='home.php' class='nav-link'>
                                                    Home
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='../product/listproducts.php' class='nav-link'>
                                                    Products
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='../admin/listAllUsers.php' class='nav-link'>
                                                    Users
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='../order/adminlistorders.php' class='nav-link'>
                                                    Orders
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='make_order.php' class='nav-link active'>
                                                Manual Order
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='../order/checks.php' class='nav-link'>
                                                    Checks
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='../category/category.php' class='nav-link'>
                                                    Categories
                                                </a>
                                            </li>
                                            <li class='nav-item'>
                                                <a href='logout.php' class='nav-link'>
                                                    Logout
                                                </a>
                                            </li>
                                        </ul>
                                    </div>";
                                
                            }else{
                                echo "<div class='d-flex flex-column p-3'>
                                        <h4 class='text-center mb-4'>";
                                        echo "Hello, " . $_SESSION['user_name'];
                                        echo"</h4>
                                            <ul class='nav nav-pills flex-column mb-auto'>
                                                <li class='nav-item'>
                                                    <a href='home.php' class='nav-link'>
                                                        Home
                                                    </a>
                                                </li>
                                                <li class='nav-item'>
                                                    <a href='../order/userlistorders.php' class='nav-link'>
                                                        My Orders
                                                    </a>
                                                </li>
                                                <li class='nav-item'>
                                                    <a href='../user/make_order.php' class='nav-link active'>
                                                        Make Order
                                                    </a>
                                                </li>
                                                <li class='nav-item'>
                                                    <a href='../user/logout.php' class='nav-link'>
                                                        Logout
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>";
                            }

                    }else{

                        echo "<div class='d-flex flex-column p-3'>
                                        <h4 class='text-center mb-4'>";
                                        echo "Hello, Guest";
                                        echo"</h4>
                                            <ul class='nav nav-pills flex-column mb-auto'>
                                                <li class='nav-item'>
                                                    <a href='home.php' class='nav-link'>
                                                        Home
                                                    </a>
                                                </li>
                                                <li class='nav-item'>
                                                    <a href='../order/userlistorders.php' class='nav-link'>
                                                        My Orders
                                                    </a>
                                                </li>
                                                <li class='nav-item'>
                                                    <a href='../user/login.php' class='nav-link'>
                                                        Login
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>";
                    }
                    ?>  
                    
                    
    </aside>
    <main class="col-md-10 col-12">
        <div class="container-fluid mt-4">
            <div class="row">
                <!-- Left Cart Section -->
                <div class="col-md-4">
                    <h5>Order Summary</h5>
                    <div id="cart">
                        <?php

                            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $pid = $item['id'];
                                    $productQuantity = $item['quantity'];
                                    $productPrice = $item['price'];
                                    $productName = $item['name'];

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
                    <?php
                        if(isset($_SESSION['user_id'])) {
                            $user_session = $_SESSION['user_id'];
                            $query = "SELECT * FROM users WHERE id = $user_session";
                            $myuser = mysqli_query($myConnection, $query);

                            $user = mysqli_fetch_assoc($myuser);

                            if($user['role'] == 'admin'){
                                echo "<div class='mb-3'>
                                        <h4>Add to user</h4>
                                        <form method='POST' action=''>
                                            <div class='row'>
                                            <select class='form-select' id='user' name='user_id'>
                                                <option>Choose User</option>";

                                $query = "SELECT * FROM users";
                                    $myusers = mysqli_query($myConnection, $query);

                                    while($user = mysqli_fetch_assoc($myusers)){
                                        $name = $user['name'];
                                        $user_id = $user['id']; 

                                        echo "<option value='$user_id'>$name</option>";
                                    }

                                echo "</select>
                                    <button type='submit' class='btn btn-primary mt-2'>Set User</button>";

                                if (isset($_POST['user_id'])) {
                                    $_SESSION['user_id_a'] = $_POST['user_id'];
                                }

                                echo "
                                        </div>
                                    </form>
                                </div>";
                                
                            }else{
                                echo "<h4>latest order</h4>";

                                $user_id = $_SESSION['user_id'];

                                $query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC LIMIT 1";
                                $myorders = mysqli_query($myConnection, $query);
                                
                                if (!$myorders) {
                                    echo "Error: " . mysqli_error($myConnection);
                                } elseif (mysqli_num_rows($myorders) == 0) {
                                    echo "No orders yet.";
                                } else {
                                    $latestOrder = mysqli_fetch_assoc($myorders);
                                
                    
                                    $orderId = $latestOrder['id'];

                                    $query = "SELECT p.name, p.image, p.price, p.id, od.quantity 
                                    FROM products p 
                                    JOIN order_details od ON p.id = od.product_id
                                    JOIN orders o ON o.id = od.order_id
                                    WHERE od.order_id = $orderId
                                    ORDER BY o.order_date DESC";

                                    $myproducts = mysqli_query($myConnection, $query);


                                    while($product = mysqli_fetch_assoc($myproducts)) {
                                        $pname = $product['name'];
                                        $pimage = $product['image'];
                                        $pprice = $product['price'];
                                        $pid = $product['id'];
                                        $quantity = $product['quantity'];  

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
                            }
                        }
                    ?>

                    <div class="row">
                        <h4>Our Products</h4>
                        <?php
                            $limit = 4; 
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $start_from = ($page - 1) * $limit;

                            $query = "SELECT * FROM products WHERE avalability = 'true' LIMIT $start_from, $limit ";
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

                            $query2 = "SELECT COUNT(id) AS total FROM products";
                            $result2 = mysqli_query($myConnection, $query2);
                            $row = mysqli_fetch_assoc($result2);
                            $total_records = $row['total'];
                            $total_pages = ceil($total_records / $limit);

                            echo '<div class="col-12 text-center mt-4">';
                            echo '<nav>';
                            echo '<ul class="pagination justify-content-center">';

                            if ($page > 1) {
                                $prev = $page - 1;
                                echo "<li class='page-item'><a class='page-link' href='?page=$prev' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&laquo;</span></li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                $active = ($i == $page) ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                            }

                            if ($page < $total_pages) {
                                $next = $page + 1;
                                echo "<li class='page-item'><a class='page-link' href='?page=$next' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
                            } else {
                                echo "<li class='page-item disabled'><span class='page-link' aria-hidden='true'>&raquo;</span></li>";
                            }

                            echo '</ul>';
                            echo '</nav>';
                            echo '</div>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
