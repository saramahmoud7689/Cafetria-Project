<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: ../user/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success text-center m-auto my-4 w-50'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}

include "../connect.php";
$numQuery = "SELECT COUNT(id) as total FROM products";
$numResult = mysqli_query($myConnection, $numQuery);
$numData = mysqli_fetch_assoc($numResult);
$totalProducts = $numData['total'];

$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">
            <div class="d-flex flex-column p-3">
                <h4 class="text-center mb-4">Admin Panel</h4>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="../user/home.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="listproducts.php" class="nav-link active">Products</a>
                    </li>
                    <li class="nav-item">
                        <a href="../admin/listAllUsers.php" class="nav-link">Users</a>
                    </li>
                    <li class="nav-item">
                        <a href="../order/adminlistorders.php" class="nav-link">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/make_order.php" class="nav-link">Manual Order</a>
                    </li>
                    <li class="nav-item">
                        <a href="../order/checks.php" class="nav-link">Checks</a>
                    </li>
                    <li class="nav-item">
                        <a href="../category/category.php" class="nav-link">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a href="../user/logout.php" class="nav-link">LogOut</a>
                    </li>
                </ul>
            </div>
        </aside>
        <main class="col-md-10 col-12">
            <h2 class="mb-4 text-center mt-3">All Products (<?php echo $totalProducts ?>)</h2>

            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success text-center w-50 mx-auto">
                <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
            </div>
            <?php endif; ?>

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
                    while ($productInfo = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td><img src='" . $productInfo["image"] . "' width='100' height='100'></td>";
                        echo "<td>" . $productInfo["name"] . "</td>";
                        echo "<td>" . $productInfo["price"] . "</td>";
                        echo "<td>";
                        if ($productInfo["avalability"] === 'true') {
                            echo "Available";
                        } else {
                            echo "Not Available";
                        }
                        ;
                        echo "</td>";
                        echo "<td>";
                        $getCat = "SELECT * FROM categories WHERE id = '$productInfo[cat_id]'";
                        $getCatResult = mysqli_query($myConnection, $getCat);
                        $cat = mysqli_fetch_assoc($getCatResult);
                        echo $cat['name'];
                        echo "</td>";
                        echo "<td>
                                    <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#confirmDeleteModal' data-productid='{$productInfo['id']}'>Delete</button>
                                    <a href='updateproduct.php?productid={$productInfo['id']}' class='btn btn-warning btn-sm'>Update</a>
                                </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example" class="my-4">
                <ul class="pagination pagination-lg justify-content-center">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $numOfPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $numOfPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </main>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var productid = button.getAttribute('data-productid');
        var confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.href = 'deleteproduct.php?productid=' + productid;
    });

    document.addEventListener('DOMContentLoaded', function() {
        var alertBox = document.querySelector('.alert');
        if (alertBox) {
            setTimeout(function() {
                var bsAlert = new bootstrap.Alert(alertBox);
                bsAlert.close();
            }, 5000);
        }
    });
    </script>
</body>

</html>