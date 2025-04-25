<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
     //Let's check if the user is logged in
    //  session_start();
    //  if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    //      header("Location: login.php");
    //      exit();
    //  }

    
    include "../connect.php";
    $numQuery = "SELECT COUNT(id) as total FROM products";
    $numResult = mysqli_query($myConnection, $numQuery);
    $numData = mysqli_fetch_assoc($numResult);
    $totalProducts = $numData['total'];

    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max(1, $page); 
    $offset = ($page - 1) * $limit;

    $query = "SELECT * FROM products LIMIT $limit OFFSET $offset";
    $result = mysqli_query($myConnection, $query);

    
    $numOfPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-center">All Products (<?php  echo $totalProducts?>)</h2>
        <a href="addproduct.php" class="btn btn-primary mb-4">Add New Product</a>
        <table class="table table-bordered table-striped text-center"> 
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                $i = ($page - 1) * $limit + 1;
                while($productInfo = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $i++ . "</td>";
                    echo "<td><img src='".$productInfo["image"]."' width='100' height='100'></td>";
                    echo "<td>".$productInfo["name"]."</td>";
                    echo "<td>".$productInfo["price"]."</td>";
                    echo "<td>";
                    if($productInfo["avalability"] ===  'true' ) { echo "Available"; } else { echo "Not Available"; };
                    echo "</td>";
                    echo "<td>";
                        $getCat = "SELECT * FROM categories WHERE id = '$productInfo[cat_id]'";
                        $getCatResult = mysqli_query($myConnection, $getCat);
                        $cat = mysqli_fetch_assoc($getCatResult);
                        echo $cat['name'];
                    echo "</td>";
                    echo "<td>
                            <a href='deleteproduct.php?productid={$productInfo['id']}' class='btn btn-danger btn-sm'>Delete</a>
                            <a href='updateproduct.php?productid={$productInfo['id']}' class='btn btn-warning btn-sm'>Update</a>
                          </td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
        <nav aria-label="Page navigation example" class="my-4">
            <ul class="pagination pagination-lg justify-content-center">
                <li class="page-item <?php  echo ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?php  echo $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">Previous</span>
                    </a>
                </li>
                
                <?php for($i = 1; $i <= $numOfPages; $i++): ?>
                    <li class="page-item <?php  echo ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?php  echo ($page >= $numOfPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?php  echo $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>
</html> 