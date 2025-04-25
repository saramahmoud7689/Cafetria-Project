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
                    <?php
                        $query = "SELECT * FROM users";
                        $myusers = mysqli_query($myConnection, $query);

                        // echo $myusers;
                        echo "<option selected>Choose Room</option>";

                        $rooms = [];
                        while($user = mysqli_fetch_assoc($myusers)){
                            // print_r($user['room']);
                            // room.push('$user['room']');
                        }

                        foreach ($rooms as $room){
                            echo "<option>$room</option>";
                        }
                    ?>
                </select>
                <?php
                    print_r($myusers);
                    while($user = mysqli_fetch_assoc($myusers)){
                        // print_r($user['room']);
                        // room.push('$user['room']');
                    }
                ?>
            </div>
            <h4>Total: EGP 55</h4> <!-- total price of order -->
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
                <!-- Example Product -->
                <?php
                $products = [
                    ['name' => 'Tea', 'price' => 5],
                    ['name' => 'Coffee', 'price' => 6],
                    ['name' => 'Nescafe', 'price' => 8],
                    ['name' => 'Cola', 'price' => 10],
                ];
                foreach ($products as $product) {
                // while($product = mysqli_fetch_assoc($myproducts)){
                    echo "<a class='btn col-3 text-center mb-4'>";
                    //href='add_to_order.php?orderitem=$product['id']'
                    // echo "<button  class='col-3 text-center mb-4' >";
                    echo '<div>';
                    // echo "<img src='$product['image']' alt='Product' class='img-fluid'>";
                    echo '<img src="data:image/jpeg;base64,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" alt="Product" class="img-fluid">';
                    echo '<p>' . $product['name'] . '</p>';
                    echo '<span class="badge bg-primary">' . $product['price'] . ' LE</span>';
                    echo '</div>';
                    echo "</a>";
                    // echo '</button>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
