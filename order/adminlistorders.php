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

include_once '../connect.php';

$rowsPerPage = 10; // Number of rows per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $rowsPerPage;

$countQuery = "SELECT COUNT(*) as total FROM orders";
$countResult = mysqli_query($myConnection, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

$getQuery = "
    SELECT orders.id AS order_id, orders.order_date, orders.status, orders.room, users.name 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    LIMIT $offset, $rowsPerPage
";

$getResult = mysqli_query($myConnection, $getQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dropdown-menu {
            min-width: 200px;
        }
        .action-dropdown .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
                        <a href="../product/listproducts.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="../product/listproducts.php" class="nav-link">Products</a>
                    </li>
                    <li class="nav-item">
                        <a href="../admin/listAllUsers.php" class="nav-link">Users</a>
                    </li>
                    <li class="nav-item">
                        <a href="../order/adminlistorders.php" class="nav-link active">Orders</a>
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
            <div class="container-fluid p-4">
                <h1 class="text-center mb-4">Orders History</h1>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Order Date</th>
                                <th>Name</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$getResult): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Error fetching orders: <?= mysqli_error($myConnection) ?></td>
                                </tr>
                            <?php elseif (mysqli_num_rows($getResult) == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No orders found.</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($orderInfo = mysqli_fetch_assoc($getResult)): ?>
                                    <?php $orderId = $orderInfo["order_id"]; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($orderInfo['order_date']) ?></td>
                                        <td><?= htmlspecialchars($orderInfo['name']) ?></td>
                                        <td><?= htmlspecialchars($orderInfo['room']) ?></td>
                                        <td>
                                            <span class="badge 
                                                <?= $orderInfo['status'] === 'processing' ? 'bg-warning text-dark' : '' ?>
                                                <?= $orderInfo['status'] === 'out for delivery' ? 'bg-info text-dark' : '' ?>
                                                <?= $orderInfo['status'] === 'done' ? 'bg-success' : '' ?>
                                                <?= $orderInfo['status'] === 'cancelled' ? 'bg-danger' : '' ?>">
                                                <?= htmlspecialchars($orderInfo['status']) ?>
                                            </span>
                                        </td>
                                        <td class="action-dropdown">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $orderId ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $orderId ?>">
                                                    <?php if ($orderInfo["status"] === "processing"): ?>
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#outModal<?= $orderId ?>">
                                                                <i class="fas fa-truck me-2"></i> Out for delivery
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $orderId ?>">
                                                                <i class="fas fa-times-circle me-2"></i> Cancel
                                                            </a>
                                                        </li>
                                                    <?php elseif ($orderInfo["status"] === "out for delivery"): ?>
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#processModal<?= $orderId ?>">
                                                                <i class="fas fa-undo me-2"></i> Back to Processing
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#doneModal<?= $orderId ?>">
                                                                <i class="fas fa-check-circle me-2"></i> Mark as Done
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $orderId ?>">
                                                                <i class="fas fa-times-circle me-2"></i> Cancel
                                                            </a>
                                                        </li>
                                                    <?php elseif (in_array($orderInfo["status"], ["done", "cancelled"])): ?>
                                                        <li>
                                                            <span class="dropdown-item text-muted">
                                                                <i class="fas fa-info-circle me-2"></i> No actions available
                                                            </span>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>

                                            <!-- Modals -->
                                            <?php if ($orderInfo["status"] === "processing"): ?>
                                                <!-- Out for Delivery Modal -->
                                                <div class="modal fade" id="outModal<?= $orderId ?>" tabindex="-1" aria-labelledby="outModalLabel<?= $orderId ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="outModalLabel<?= $orderId ?>">Confirm Status Change</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to mark this order as "Out for Delivery"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <a href="updateorder.php?orderid=<?= $orderId ?>&status=<?= urlencode("out for delivery") ?>" class="btn btn-primary">Confirm</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Cancel Modal -->
                                                <div class="modal fade" id="cancelModal<?= $orderId ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?= $orderId ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="cancelModalLabel<?= $orderId ?>">Confirm Cancellation</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to cancel this order?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <a href="updateorder.php?orderid=<?= $orderId ?>&status=cancelled" class="btn btn-danger">Confirm</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($orderInfo["status"] === "out for delivery"): ?>
                                                <!-- Processing Modal -->
                                                <div class="modal fade" id="processModal<?= $orderId ?>" tabindex="-1" aria-labelledby="processModalLabel<?= $orderId ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="processModalLabel<?= $orderId ?>">Confirm Status Change</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to return this order to "Processing" status?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <a href="updateorder.php?orderid=<?= $orderId ?>&status=processing" class="btn btn-warning">Confirm</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Done Modal -->
                                                <div class="modal fade" id="doneModal<?= $orderId ?>" tabindex="-1" aria-labelledby="doneModalLabel<?= $orderId ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="doneModalLabel<?= $orderId ?>">Confirm Completion</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to mark this order as "Done"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <a href="updateorder.php?orderid=<?= $orderId ?>&status=done" class="btn btn-success">Confirm</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>