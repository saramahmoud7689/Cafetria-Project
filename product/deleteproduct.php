<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../user/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include_once '../connect.php';
$productid = $_GET['productid'];


$sqlCheckRelatedOrders = "SELECT * FROM order_details WHERE product_id = '$productid'";
$resultCheckRelatedOrders = mysqli_query($myConnection, $sqlCheckRelatedOrders);

if(mysqli_num_rows($resultCheckRelatedOrders) <= 0) {
    $sql = "DELETE FROM products WHERE id = '$productid'";
    $result = mysqli_query($myConnection, $sql);

    if ($result) {
        $_SESSION['success_message'] = "Product Deleted Successfully";
    } else {
        $_SESSION['success_message'] = "Failed to delete product: " . mysqli_error($myConnection);
    }

    header("Location: listproducts.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class='alert alert-danger text-center m-auto w-50 my-5'>Cannot delete product because it has related orders, you can update it to unavailable</div>
    <a href="listproducts.php" class="btn btn-primary text-center m-auto w-50 d-block ">Return back to product list page</a>
</body>
</html>