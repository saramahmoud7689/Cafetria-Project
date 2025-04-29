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
$sql = "DELETE FROM products WHERE id = '$productid'";
$result = mysqli_query($myConnection, $sql);

if ($result) {
    $_SESSION['success_message'] = "Product Deleted Successfully";
} else {
    $_SESSION['success_message'] = "Failed to delete product: " . mysqli_error($myConnection);
}

header("Location: listproducts.php");
exit();
?>