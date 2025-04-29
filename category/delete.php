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

    $catId = $_GET['catid'];

    if (empty($catId)) {
        header("Location: category.php");
        exit();
    }

    $sqlCheckRelatedProducts = "SELECT * FROM products WHERE cat_id = '$catId'";
    $resultCheckRelatedProducts = mysqli_query($myConnection, $sqlCheckRelatedProducts);
    if (mysqli_num_rows($resultCheckRelatedProducts) <= 0) {
        $sql = "DELETE FROM categories WHERE id = '$catId'";
        $result = mysqli_query($myConnection, $sql);
    
        if($result) {
            echo "<div class='alert alert-success text-center m-auto w-50'>Category Deleted Successfully</div>";
            header("Location: category.php");
        } else {
            echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to delete category: " . mysqli_error($myConnection) . "</div>";
        }
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
    <div class='alert alert-danger text-center m-auto w-50 my-5'>Cannot delete category because it has related products</div>
    <a href="category.php" class="btn btn-primary text-center m-auto w-50 d-block ">Return back to category list page</a>
</body>
</html>