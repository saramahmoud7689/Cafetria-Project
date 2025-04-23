<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include "../connect.php";
    $query = "SELECT * FROM products";
    $result = mysqli_query($myConnection, $query);

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
        <h2 class="mb-4 text-center">All Products</h2>
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
                $i = 1;
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
    </div>
</body>
</html>