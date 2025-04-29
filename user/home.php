<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">

            <?php
                session_start();
                include_once '../connect.php';
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
                                    <a href='make_order.php' class='nav-link'>
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
                                        <a href='../user/logout.php' class='nav-link'>
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>";
                }
            ?>        
        </aside>
        <main class="col-md-10 col-12">
        <h1>Home Page</h1>
        <p>Welcome to the home page!</p>
        </main>
    </div>
</body>
</html>