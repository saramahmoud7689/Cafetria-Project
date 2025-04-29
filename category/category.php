<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../user/login.php");
        exit();
    }

    if ( $_SESSION['role'] !== 'admin' ) {
        header("Location: ../unauthorized.php");
        exit();
    }
    
    include_once '../connect.php';

    $errors = array();
    $catName = '';

    if(isset($_POST['submit'])) {
        
        if(empty($_POST['name'])) {
            $errors['name'] = "Name is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Category Name is required </div>";
        }
        if(strlen($_POST['name']) < 3 && !empty($_POST['name'])) {
            $errors['name'] = "Name is too short";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Category Name lenght should be more than or equal 3 characters </div>";
        } 
        
        if(empty($errors)) {
            $catName = mysqli_real_escape_string($myConnection, $_POST['name']);
        }

        $checkExisting = "SELECT * FROM categories WHERE name = '$catName'";
        $checkExistingResult = mysqli_query($myConnection, $checkExisting);
        
        if (mysqli_num_rows($checkExistingResult) > 0) {
            $errors['name'] = "Category already exists";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Category Name already exists </div>";
        } 
        if(empty($errors)) {
            
            
            $insertQuery = "INSERT INTO categories (name) VALUES ('$catName')";
            $result = mysqli_query($myConnection, $insertQuery);
            if($result) {
                echo "<div class='alert alert-success text-center m-auto w-50'>Category Added Successfully</div>";
                $catName = ""; 
                header("Location: category.php");
            } else {
                $errors['add'] = "Failed to add category: " . mysqli_error($myConnection);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { margin: 0; padding: 0; }
        form { 
            margin: 0 auto;
            width: 50%;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">
                <div class="d-flex flex-column p-3">
                    <h4 class="text-center mb-4">Admin Panel</h4>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="../user/home.php" class="nav-link">
                                 Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../product/listproducts.php" class="nav-link">
                               Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../admin/listAllUsers.php" class="nav-link">
                                 Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../order/adminlistorders.php" class="nav-link">
                                Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../user/make_order.php" class="nav-link">
                               Manual Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../order/checks.php" class="nav-link">
                                 Checks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../category/category.php" class="nav-link active">
                                 Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../user/logout.php" class="nav-link">
                             LogOut
                            </a>
                        </li>
                    </ul>
                </div>
        </aside>
        <main class="col-md-10 col-12">
            <h1 class="text-center">Category Management</h1>
            <form method="POST" class="d-flex justify-content-between align-items-baseline">
                <div class="mb-3 col-md-6">
                    <input type="text" class="form-control p-2" id="name" name="name">
                </div>
                <button type="submit" class="btn btn-primary col-md-4 text-center p-2" name="submit">Add Category</button>
            </form>

            <table class="table table-bordered table-striped text-center m-auto w-50 my-5"> 
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $getQuery = "SELECT * FROM categories";
                        $getResult = mysqli_query($myConnection, $getQuery);
                        
                        if ($getResult) {
                            $i = 1; 
                            while($catInfo = mysqli_fetch_assoc($getResult)) {
                                echo "<tr>";
                                echo "<td>" . $i++ . "</td>";
                                echo "<td>" . htmlspecialchars($catInfo["name"]) . "</td>";
                                echo "<td>
                                        <a href='delete.php?catid={$catInfo['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                        <a href='update.php?catid={$catInfo['id']}' class='btn btn-warning btn-sm'>Update</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Error fetching categories: " . mysqli_error($myConnection) . "</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
    
</body>
</html>