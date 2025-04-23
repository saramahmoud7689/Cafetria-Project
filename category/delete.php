<?php

    include_once '../connect.php';

    $catId = $_GET['catid'];

    $sql = "DELETE FROM categories WHERE id = '$catId'";
    $result = mysqli_query($myConnection, $sql);

    if($result) {
        echo "<div class='alert alert-success text-center m-auto w-50'>Category Deleted Successfully</div>";
        header("Location: category.php");
    } else {
        echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to delete category: " . mysqli_error($myConnection) . "</div>";
    }
?>