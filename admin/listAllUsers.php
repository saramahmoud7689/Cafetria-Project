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

include_once("../connect.php");

$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$errorMessage = '';
if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$sql = "SELECT * FROM users";
$result = mysqli_query($myConnection, $sql);

if (!$result) {
    die("Error: " . mysqli_error($myConnection));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List All Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .profile-pic {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #dee2e6;
    }

    .table-container {
        margin: 20px auto;
        max-width: 1200px;
    }

    .action-links a {
        margin: 0 5px;
    }

    .no-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 12px;
    }
    </style>
</head>

<body class="bg-light">
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

        <main class="col-12 col-md-10">
            <div class="table-container">
                <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this user?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success text-center w-50 mx-auto">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger text-center w-50 mx-auto">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
                <div class="alert alert-success text-center w-50 mx-auto">
                    User added successfully!
                </div>
                <?php endif; ?>


                <h2 class="text-center my-4">All Users</h2>
                <a href="addUser.php" class="btn btn-primary mb-4">Add New User</a>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Room No.</th>
                                        <th>Profile Picture</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($userInfo = mysqli_fetch_assoc($result)):
                                        $profilePath = '';
                                        if (!empty($userInfo["profile_picture"])) {
                                            $basePath = "C:/xampp/htdocs/Cafetria-Project/user/uploads/";
                                            $fileName = basename($userInfo["profile_picture"]);
                                            $fullPath = $basePath . $fileName;
                                            if (file_exists($fullPath)) {
                                                $profilePath = "../user/uploads/" . $fileName;
                                            }
                                        }
                                        ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($userInfo["name"]); ?></td>
                                        <td><?php echo htmlspecialchars($userInfo["email"]); ?></td>
                                        <td><?php echo htmlspecialchars($userInfo["room"]); ?></td>
                                        <td>
                                            <?php if (!empty($profilePath)): ?>
                                            <img src="<?php echo htmlspecialchars($profilePath); ?>" class="profile-pic"
                                                alt="Profile Picture"
                                                onerror="this.onerror=null;this.src='../assets/default-profile.jpg'">
                                            <?php else: ?>
                                            <div class="no-image">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-links">
                                            <a href="update.php?userid=<?php echo htmlspecialchars($userInfo["id"]); ?>"
                                                class="btn btn-sm btn-primary">Update</a>
                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteModal"
                                                data-userid="<?php echo htmlspecialchars($userInfo["id"]); ?>">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-userid');
            confirmDeleteBtn.href = "delete.php?userid=" + userId;
        });
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