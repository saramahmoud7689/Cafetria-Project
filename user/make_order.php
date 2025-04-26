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
                    include_once "../connect.php";
                    $query = "SELECT * FROM products";
                    $myproducts = mysqli_query($myConnection, $query);
                    // print_r($myproducts);
                    $order =[
                        ["name" => "product1","quantity" => 2],
                        ["name" => "product2","quantity" => 1]
                    ];
                    $total = 0;

                    while($product = mysqli_fetch_assoc($myproducts)){
                        $productQuantity = 0 ;
                        // || $order[0]['name'].quantity;

                        $pname= $product['name'];
                        $pprice= $product['price'];
                        // print_r($pname);
                        // echo $pname;

                        echo "<div class='d-flex justify-content-between mb-2'>
                            <span>$pname</span>  <!-- product name -->
                            <div> 
                                <button class='btn btn-sm btn-outline-secondary'>-</button> <!-- plus quantity --> 
                                <input type='text' value='$productQuantity' size='1' readonly> <!-- product quantity -->
                                <button class='btn btn-sm btn-outline-secondary'>+</button> <!-- minus quantity -->
                                <button class='btn btn-sm btn-danger'>X</button>
                                EGP $pprice <!-- product price -->
                                
                            </div>
                        </div>";
                    }
                       
                ?>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="room" class="form-label">Room</label>
                <select class="form-select" id="room" name="room"><!-- options from users.room -->
                    <option value="">Select Room</option>
                    <option value="Application1">Application1</option>
                    <option value="Application2">Application2</option>
                    <option value="Cloud">Cloud</option>
                </select>
            </div>
            <h4>Total: EGP 
                <?php
                    $total = 0;
                    foreach($order as $item){
                        $total += $item['price'];
                    }
                    echo $total;
                ?>
            </h4> <!-- total price of order -->
            <button class="btn btn-primary w-100">Confirm</button>
        </div>

        <!-- Right Product Section -->
        <div class="col-md-8">
            <!-- at admin view -->
            <!-- <div class="mb-3">
                <label for="user" class="form-label">Add to user</label>
                <select class="form-select" id="user" name="user">  !-- options from users.name --
                    <option selected>Islam Askar</option>
                    <option>Other User</option>
                </select>
            </div> -->

            <!-- at user view -->
             <h4>latest order</h4>
             <!-- show latest order -->

            <div class="row">
                <?php


                $query = "SELECT * FROM products";
                $myproducts = mysqli_query($myConnection, $query);
                

                while($product = mysqli_fetch_assoc($myproducts)){
                    $pname = $product['name'];
                    $pimage = $product['image'];
                    $pprice = $product['price'];
                    $pid = $product['id'];
                
                    echo "<a href='cart.php?orderitem=$pid' class='btn col-3 text-center mb-4'>";
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
