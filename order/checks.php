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
     
     
     $limit = 1;
     $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
     $page = max(1, $page); 
     $offset = ($page - 1) * $limit;
 ?>
 
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Checks</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 </head>
 <body>
     <div class="page-container row">
         <aside class="col-md-2 col-12">
                 <div class="d-flex flex-column p-3">
                     <h4 class="text-center mb-4">Admin Panel</h4>
                     <ul class="nav nav-pills flex-column mb-auto">
                         <li class="nav-item">
                             <a href="../product/listproducts.php" class="nav-link">
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
                             <a href="../order/checks.php" class="nav-link active">
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
             <h1 class="text-center">Checks</h1>
 
             
             <form class="d-flex justify-content-between align-items-baseline" method="post">
                 <div class="input-group mb-3 mx-3">
                     <input type="date" class="form-control" id="orderDateFrom" name="orderDateFrom" value="<?= isset($_POST['orderDateFrom']) ? htmlspecialchars($_POST['orderDateFrom']) : '' ?>">
                 </div>
                 <div class="input-group mb-3 mx-3">
                     <input type="date" class="form-control" id="orderDateTo" name="orderDateTo" value="<?= isset($_POST['orderDateTo']) ? htmlspecialchars($_POST['orderDateTo']) : '' ?>">
                 </div>
                 <div class="input-group mb-3 mx-3">
                     <select class="form-select mb-3" name="user_id" id="user_id">
                         <option disabled selected>Select User</option>
                         <?php  
                             $getAllUsers = "SELECT * FROM users";
                             $result = mysqli_query($myConnection, $getAllUsers);
                             while ($row = mysqli_fetch_assoc($result)) {
                                 $selected = (isset($_POST['user_id']) && $_POST['user_id'] == $row['id']) ? 'selected' : '';
                                 echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                             }
                         ?>
                     </select>
                 </div>
                 <button type="submit" class="btn btn-primary" name="submit">Filter</button>
             </form>
 
             <table class="table table-bordered table-striped text-center">
                 <thead class="table-dark">
                     <tr>
                         <th>Orders</th>
                         <th>Total Amount</th>
                     </tr>
                 </thead>
                 <tbody>
                 <?php
                     if (isset($_POST['submit'])) {
                         $orderDateFrom = $_POST['orderDateFrom'];
                         $orderDateTo = $_POST['orderDateTo'];
                         
                         
                         $countQuery = "SELECT COUNT(DISTINCT orders.user_id) as total FROM orders, users WHERE orders.user_id = users.id";
                         $conditions = [];
                         
                         if(isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                             $user_id = $_POST['user_id'];
                             $conditions[] = "orders.user_id = '$user_id'";
                         }
                         
                         if (!empty($orderDateFrom)) {
                             $conditions[] = "order_date >= '$orderDateFrom'";
                         }
                         
                         if (!empty($orderDateTo)) {
                             $conditions[] = "order_date <= '$orderDateTo'";
                         }
                         
                         if (!empty($conditions)) {
                             $countQuery .= " AND " . implode(" AND ", $conditions);
                         }
                         
                         $countResult = mysqli_query($myConnection, $countQuery);
                         $totalRecords = mysqli_fetch_assoc($countResult)['total'];
                         $numOfPages = ceil($totalRecords / $limit);
                         
                         
                         $getQuery = "SELECT orders.user_id, SUM(orders.total_cost) as total_amount, users.name 
                                     FROM orders 
                                     JOIN users ON orders.user_id = users.id";
                                     
                         if (!empty($conditions)) {
                             $getQuery .= " WHERE " . implode(" AND ", $conditions);
                         }
                         
                         $getQuery .= " GROUP BY orders.user_id LIMIT $limit OFFSET $offset";
                         $getResult = mysqli_query($myConnection, $getQuery);
                         
                     } else {
                         
                         $countQuery = "SELECT COUNT(DISTINCT user_id) as total FROM orders";
                         $countResult = mysqli_query($myConnection, $countQuery);
                         $totalRecords = mysqli_fetch_assoc($countResult)['total'];
                         $numOfPages = ceil($totalRecords / $limit);
                         
                         $getQuery = "SELECT orders.user_id, SUM(orders.total_cost) as total_amount, users.name 
                                      FROM orders 
                                      JOIN users ON orders.user_id = users.id 
                                      GROUP BY orders.user_id 
                                      LIMIT $limit OFFSET $offset";
                         $getResult = mysqli_query($myConnection, $getQuery);
                     }   
                     
                     if(!$getResult) {
                         echo "<tr><td colspan='2'>Error fetching orders: " . mysqli_error($myConnection) . "</td></tr>";
                     } elseif (mysqli_num_rows($getResult) == 0) {
                         echo "<tr><td colspan='2'>No orders found.</td></tr>";
                     } else {
                         $counter = ($page - 1) * $limit + 1;
                         while($orderInfo = mysqli_fetch_assoc($getResult)) {
                             $userId = $orderInfo['user_id'];
 
                             $getSubOrder = "SELECT order_date, total_cost FROM `orders` WHERE user_id = $userId";
                             $getSubOrderResult = mysqli_query($myConnection, $getSubOrder);                       
                             ?>
                             <tr>
                                 <td>
                                     <div class="accordion" id="orderAccordion<?= $counter ?>">
                                         <div class="accordion-item border-0">
                                             <h2 class="accordion-header" id="heading<?= $counter ?>">
                                                 <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                         data-bs-target="#collapse<?= $counter ?>" aria-expanded="false" 
                                                         aria-controls="collapse<?= $counter ?>">
                                                     <?= $orderInfo["name"] ?>
                                                 </button>
                                             </h2>
                                             <div id="collapse<?= $counter ?>" class="accordion-collapse collapse" 
                                                 aria-labelledby="heading<?= $counter ?>" data-bs-parent="#orderAccordion<?= $counter ?>">
                                                 <div class="accordion-body">
                                                     <?php 
                                                     while($subOrderData = mysqli_fetch_assoc($getSubOrderResult)) {
                                                         echo '<div class="row">
                                                             <div class="col-md-4"><strong>OrderDate:</strong> ' . $subOrderData["order_date"] . '</div>
                                                             <div class="col-md-4"><strong> | </strong></div>
                                                             <div class="col-md-4"><strong>Total Cost:</strong>  ' . $subOrderData["total_cost"] . ' EGP</div>
                                                         </div>';
                                                     }
                                                     ?>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </td>
                                 <td>
                                     <?= $orderInfo["total_amount"]. ' EGP'?>
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
             <nav aria-label="Page navigation example" class="my-4">
                 <ul class="pagination pagination-lg justify-content-center">
                     <li class="page-item <?php  echo ($page <= 1) ? 'disabled' : '' ?>">
                         <a class="page-link" href="?page=<?php  echo $page - 1 ?>" aria-label="Previous">
                             <span aria-hidden="true">&laquo;</span>
                         </a>
                     </li>
                     
                     <?php for($i = 1; $i <= $numOfPages; $i++): ?>
                         <li class="page-item <?php  echo ($i == $page) ? 'active' : '' ?>">
                             <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                         </li>
                     <?php endfor; ?>
                     
                     <li class="page-item <?php  echo ($page >= $numOfPages) ? 'disabled' : '' ?>">
                         <a class="page-link" href="?page=<?php  echo $page + 1 ?>" aria-label="Next">
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