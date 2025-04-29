<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../user/login.php");
        exit();
    }
    
    include_once '../connect.php';

    $user_id = $_SESSION['user_id'];

    
    $ordersPerPage = 5;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($currentPage < 1) $currentPage = 1;
    $offset = ($currentPage - 1) * $ordersPerPage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History | User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="page-container row">
    <aside class="col-md-2 col-12">
                <div class="d-flex flex-column p-3">
                    <h4 class="text-center mb-4">
                        <?php echo "Hello, " . $_SESSION['user_name']; ?>
                    </h4>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="../user/home.php" class="nav-link active">
                                 Home
                            </a>
                        </li>
                        
                        <li class="nav-item">
                                <a href="../user/make_order.php" class="nav-link">
                                    Make Order
                                </a>
                        </li>

                        <li class="nav-item">
                            <a href="../user/logout.php" class="nav-link">
                               Logout
                            </a>
                        </li>
                    </ul>
                </div>
        </aside>
        <main class="col-md-8 col-12">
            <h1 class="text-center">Orders History</h1>

            <form class="d-flex justify-content-between align-items-baseline" method="post">
                <div class="input-group mb-3 mx-3">
                    <input type="date" class="form-control" id="orderDateFrom" name="orderDateFrom">
                </div>
                <div class="input-group mb-3 mx-3">
                    <input type="date" class="form-control" id="orderDateTo" name="orderDateTo">
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Filter</button>
            </form>

            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Order Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $filterCondition = "user_id = '$user_id'";

                    if (isset($_POST['submit'])) {
                        $orderDateFrom = $_POST['orderDateFrom'];
                        $orderDateTo = $_POST['orderDateTo'];

                        if (empty($orderDateFrom) && !empty($orderDateTo)) {
                            $filterCondition .= " AND order_date <= '$orderDateTo'";
                        } elseif (!empty($orderDateFrom) && empty($orderDateTo)) {
                            $filterCondition .= " AND order_date >= '$orderDateFrom'";
                        } elseif (!empty($orderDateFrom) && !empty($orderDateTo)) {
                            $filterCondition .= " AND order_date BETWEEN '$orderDateFrom' AND '$orderDateTo'";
                        }
                    }

                    // Fetch total orders for pagination
                    $countQuery = "SELECT COUNT(*) as total FROM orders WHERE $filterCondition";
                    $countResult = mysqli_query($myConnection, $countQuery);
                    $totalOrders = mysqli_fetch_assoc($countResult)['total'];
                    $totalPages = ceil($totalOrders / $ordersPerPage);

                    // Fetch limited orders for current page
                    $getQuery = "SELECT * FROM orders WHERE $filterCondition ORDER BY order_date DESC LIMIT $offset, $ordersPerPage";
                    $getResult = mysqli_query($myConnection, $getQuery);

                    if(!$getResult) {
                        echo "<tr><td colspan='2'>Error fetching orders: " . mysqli_error($myConnection) . "</td></tr>";
                    } elseif (mysqli_num_rows($getResult) == 0) {
                        echo "<tr><td colspan='2'>No orders found.</td></tr>";
                    } else {
                        $counter = 1 + $offset;
                        while($orderInfo = mysqli_fetch_assoc($getResult)) {
                            $orderId = $orderInfo["id"];
                            ?>
                            <tr>
                                <td>
                                    <div class="accordion" id="orderAccordion<?= $counter ?>">
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header" id="heading<?= $counter ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse<?= $counter ?>" aria-expanded="false" 
                                                        aria-controls="collapse<?= $counter ?>">
                                                    Order Date: <?= $orderInfo["order_date"] ?>
                                                </button>
                                            </h2>
                                            <div id="collapse<?= $counter ?>" class="accordion-collapse collapse" 
                                                aria-labelledby="heading<?= $counter ?>" data-bs-parent="#orderAccordion<?= $counter ?>">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-4"><strong>Status:</strong> <?= $orderInfo["status"] ?></div>
                                                        <div class="col-md-4"><strong>Total Cost:</strong> $<?= $orderInfo["total_cost"] ?></div>
                                                        <div class="col-md-4"><strong>Room:</strong> <?= $orderInfo["room"] ?></div>
                                                        <div class="col-md-12 mt-2"><strong>Notes:</strong> <?= $orderInfo["notes"] ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($orderInfo["status"] !== "cancelled"): ?>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $orderId ?>">
                                        Cancel
                                    </button>
                                    <?php endif; ?>

                                    <?php if ($orderInfo["status"] === "cancelled"): ?>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        Cancelled
                                    </button>
                                    <?php endif; ?>
                                    

                                    <div class="modal fade" id="cancelModal<?= $orderId ?>" tabindex="-1" aria-labelledby="modalLabel<?= $orderId ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="modalLabel<?= $orderId ?>">Confirm Cancellation</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body fs-5">
                                                    Are you sure you want to cancel this order?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href='cancelorder.php?orderid=<?= $orderId ?>&status=cancelled' class='btn btn-danger'>Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $counter++;
                        }
                    }
                ?>
                </tbody>
            </table>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    
                        <li class="page-item <?= $currentPage <= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                   

                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : '?page=' . ($currentPage + 1) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>


        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
