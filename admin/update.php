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

if (isset($_GET["userid"])) {
    $id = mysqli_real_escape_string($myConnection, $_GET["userid"]);
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($myConnection, $query);

    if (!$result) {
        die("Error: " . mysqli_error($myConnection));
    }

    $row = mysqli_fetch_assoc($result);

    $profilePicture = '';
    if (!empty($row["profile_picture"])) {
        $basePath = "C:/xampp/htdocs/Cafetria-Project/user/uploads/";
        $fullPath = $basePath . basename($row["profile_picture"]);

        if (file_exists($fullPath)) {
            $profilePicture = "../user/uploads/" . basename($row["profile_picture"]);
        }
    }
}

if (isset($_POST["submit"])) {
    $id = mysqli_real_escape_string($myConnection, $_GET["userid"]);
    $name = mysqli_real_escape_string($myConnection, $_POST["name"]);
    $email = mysqli_real_escape_string($myConnection, $_POST["email"]);
    $room = mysqli_real_escape_string($myConnection, $_POST["room"]);
    $role = mysqli_real_escape_string($myConnection, $_POST["role"]);

    if (!empty($_FILES["profile_picture"]["name"])) {
        $targetDir = "C:/xampp/htdocs/Cafetria-Project/user/uploads/";
        $fileName = basename($_FILES["profile_picture"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                $query = "UPDATE users SET name='$name', email='$email', room='$room', role='$role', profile_picture='$fileName' WHERE id='$id'";
            } else {
                die("Error uploading file.");
            }
        } else {
            die('Only JPG, JPEG, PNG, GIF files are allowed.');
        }
    } else {
        $query = "UPDATE users SET name='$name', email='$email', room='$room', role='$role' WHERE id='$id'";
    }

    $result = mysqli_query($myConnection, $query);

    if ($result) {
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: listAllUsers.php");
        exit();
    } else {
        die("Error: " . mysqli_error($myConnection));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .update-form {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background: white;
    }

    .profile-pic-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .no-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #6c757d;
        font-size: 14px;
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
        <main class="col-md-8 col-12 my-4">
            <div class="update-form card">
                <div class="card-body">
                    <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success text-center">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                    <?php endif; ?>

                    <h2 class="text-center mb-4">Update User</h2>
                    <div class="profile-pic-container">
                        <?php if (!empty($profilePicture)): ?>
                        <img src="<?php echo $profilePicture; ?>" class="profile-pic" alt="Profile Picture"
                            onerror="this.onerror=null;this.src='../assets/default-profile.jpg'">
                        <?php else: ?>
                        <div class="no-image">No Image</div>
                        <?php endif; ?>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo htmlspecialchars($row["name"]); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($row["email"]); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="room" class="form-label">Room No.</label>
                            <select class="form-select" id="room" name="room">
                                <option value="Application1"
                                    <?php echo ($row["room"] == "Application1") ? "selected" : ""; ?>>
                                    Application1</option>
                                <option value="Application2"
                                    <?php echo ($row["room"] == "Application2") ? "selected" : ""; ?>>
                                    Application2</option>
                                <option value="Cloud" <?php echo ($row["room"] == "Cloud") ? "selected" : ""; ?>>Cloud
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="user" <?php echo ($row["role"] == "user") ? "selected" : ""; ?>>User
                                </option>
                                <option value="admin" <?php echo ($row["role"] == "admin") ? "selected" : ""; ?>>Admin
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        </div>
                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-primary">Update</button>
                            <a href="listAllUsers.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>