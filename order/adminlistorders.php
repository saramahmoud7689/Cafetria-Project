<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    if ( !isset($_SESSION['user_name']) ) {
        header("Location: ../user/login.php");
        exit();
    }

    if ( $_SESSION['role'] !== 'admin' ) {
        header("Location: ../unauthorized.php");
        exit();
    }

    include_once '../connect.php';

    $user_id = $_SESSION['user_id'];
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a href="../category/category.php" class="nav-link">
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
        <main class="col-md-8 col-12">
            <h1 class="text-center">Orders History</h1>

            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Order Date</th>
                        <th>Name</th>
                        <th>Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                   $getQuery = "SELECT *, name FROM orders, users WHERE orders.user_id = users.id";
                   $getResult = mysqli_query($myConnection, $getQuery);
                    
                    if(!$getResult) {
                        echo "<tr><td colspan='2'>Error fetching orders: " . mysqli_error($myConnection) . "</td></tr>";
                    } elseif (mysqli_num_rows($getResult) == 0) {
                        echo "<tr><td colspan='2'>No orders found.</td></tr>";
                    } else {
                        $counter = 1;
                        while($orderInfo = mysqli_fetch_assoc($getResult)) {
                            $orderId = $orderInfo["id"];
                            ?>
                            <tr>
                                <td>
                                    <?= $orderInfo['order_date'] ?>
                                </td>
                                <td><?=$orderInfo['name']?></td>
                                <td> <?= $orderInfo['room'] ?> </td>
                                <td>
                                    
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#outModal<?= $orderId ?>">
                                            <?php echo $orderInfo["status"]; ?>
                                        </button>    
                                        
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
                            
                                        
                                        <div class="modal fade" id="cancelModal<?= $orderId ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?= $orderId ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="cancelModalLabel<?= $orderId ?>">Confirm Cancellation</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body fs-5">
                                                        Are you sure you want to cancel this order?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href='updateorder.php?orderid=<?= $orderId ?>&status=cancelled' class='btn btn-danger'>Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="outModal<?= $orderId ?>" tabindex="-1" aria-labelledby="outModalLabel<?= $orderId ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="outModalLabel<?= $orderId ?>"> Update status </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body fs-5">
                                                        Are you sure you want to make this order out for delivery?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href='updateorder.php?orderid=<?= $orderId ?>&status=out for delivery' class='btn btn-warning'>Update</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php if ($orderInfo["status"] === "out for delivery"): ?>
                                    
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#processModal<?= $orderId ?>">
                                            processing
                                        </button>    
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#doneModal<?= $orderId ?>">
                                            Done
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $orderId ?>">
                                            Cancel
                                        </button>
                            
                                        
                                        <div class="modal fade" id="cancelModal<?= $orderId ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?= $orderId ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="cancelModalLabel<?= $orderId ?>">Confirm Cancellation</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body fs-5">
                                                        Are you sure you want to cancel this order?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href='updateorder.php?orderid=<?= $orderId ?>&status=cancelled' class='btn btn-danger'>Update</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="processModal<?= $orderId ?>" tabindex="-1" aria-labelledby="processModalLabel<?= $orderId ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="processModalLabel<?= $orderId ?>"> Update status </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body fs-5">
                                                        Are you sure you want to make this order processing?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href='updateorder.php?orderid=<?= $orderId ?>&status=processing' class='btn btn-warning'>Update</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="doneModal<?= $orderId ?>" tabindex="-1" aria-labelledby="doneModalLabel<?= $orderId ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="doneModalLabel<?= $orderId ?>"> Update status </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body fs-5">
                                                        Are you sure you want to make this order done?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href='updateorder.php?orderid=<?= $orderId ?>&status=done' class='btn btn-success'>Update</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <?php if ($orderInfo["status"] === "done"): ?>
                                            <p> Order is already delivered successfuly, so no action needed </p>
                                    <?php endif; ?>

                                    <?php if ($orderInfo["status"] === "cancelled"): ?>
                                            <p> Order is already cancelled, so no action needed </p>
                                    <?php endif; ?>
                                </td >
                            </tr>
                            <?php
                            $counter++;
                        }
                    }
                    $orderDateFrom = "";
                    $orderDateTo = "";
                ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>