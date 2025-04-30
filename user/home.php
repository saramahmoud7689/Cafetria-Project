<?php
session_start();
include "../connect.php";
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">
            <?php
        if (isset($_SESSION['user_id'])) {
            $user_session = $_SESSION['user_id'];
            $query = "SELECT * FROM users WHERE id = $user_session";
            $myuser = mysqli_query($myConnection, $query);
            $user = mysqli_fetch_assoc($myuser);

            echo "<div class='d-flex flex-column p-3'>";
            if ($user['role'] == 'admin') {
                echo "<h4 class='text-center mb-4'>Admin Panel</h4>";
                echo "<p>Hello, " . $_SESSION['user_name'] . "</p>";
                if (!empty($user['profile_picture'])) {
                    echo "<div class='text-center mb-3'>
                            <img src='./{$user['profile_picture']}' alt='Profile Picture' class='rounded-circle' width='100' height='100'>
                            </div>";
                }
                echo "<ul class='nav nav-pills flex-column mb-auto'>
                        <li class='nav-item'><a href='home.php' class='nav-link'>Home</a></li>
                        <li class='nav-item'><a href='../product/listproducts.php' class='nav-link'>Products</a></li>
                        <li class='nav-item'><a href='../admin/listAllUsers.php' class='nav-link'>Users</a></li>
                        <li class='nav-item'><a href='../order/adminlistorders.php' class='nav-link'>Orders</a></li>
                        <li class='nav-item'><a href='make_order.php' class='nav-link'>Manual Order</a></li>
                        <li class='nav-item'><a href='../order/checks.php' class='nav-link'>Checks</a></li>
                        <li class='nav-item'><a href='../category/category.php' class='nav-link'>Categories</a></li>
                        <li class='nav-item'><a href='logout.php' class='nav-link'>Logout</a></li>
                    </ul>";
            } else {
                echo "<h4 class='text-center mb-4'>Hello, " . $_SESSION['user_name'] . "</h4>";
                if (!empty($user['profile_picture'])) {
                    echo "<div class='text-center mb-3'>
                            <img src='./{$user['profile_picture']}' alt='Profile Picture' class='rounded-circle' width='100' height='100'>
                          </div>";
                }
                echo "<ul class='nav nav-pills flex-column mb-auto'>
                        <li class='nav-item'><a href='home.php' class='nav-link active'>Home</a></li>
                        <li class='nav-item'><a href='../order/userlistorders.php' class='nav-link'>My Orders</a></li>
                        <li class='nav-item'><a href='make_order.php' class='nav-link'>Make Order</a></li>
                        <li class='nav-item'><a href='../user/logout.php' class='nav-link'>Logout</a></li>
                    </ul>";
            }
            echo "</div>";
        } else {
            echo "<div class='d-flex flex-column p-3'>
                    <h4 class='text-center mb-4'>Hello, Guest</h4>
                    <ul class='nav nav-pills flex-column mb-auto'>
                        <li class='nav-item'><a href='home.php' class='nav-link'>Home</a></li>
                        <li class='nav-item'><a href='../order/userlistorders.php' class='nav-link'>My Orders</a></li>
                        <li class='nav-item'><a href='../user/login.php' class='nav-link'>Login</a></li>
                    </ul>
                  </div>";
        }
        ?>
        </aside>

        <main class="col-md-10 col-12">
            <div class="container mt-4">
                <h1 class="mb-4 text-center">Welcome to Our Cafeteria</h1>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <img src="../images/coffe.jpeg" class="card-img-top" alt="Coffee">
                            <div class="card-body">
                                <h5 class="card-title">Today's Special</h5>
                                <p class="card-text">Enjoy our freshly brewed cappuccino with a chocolate croissant
                                    combo for just $5!</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <img src="../images/cafe_latee.jpg" class="card-img-top" alt="Menu">
                            <div class="card-body">
                                <h5 class="card-title">Explore the Menu</h5>
                                <p class="card-text">From sandwiches to smoothies — check out our full range of tasty
                                    items.</p>
                                <a href="make_order.php" class="btn btn-primary">View Menu</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <img src="../images/tea.jpeg" class="card-img-top" alt="Offers">
                            <div class="card-body">
                                <h5 class="card-title">Special Offers</h5>
                                <p class="card-text">Sign up to get daily discounts and free drinks on your next visit!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>