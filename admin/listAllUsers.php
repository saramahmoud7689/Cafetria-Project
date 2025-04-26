<?php
include_once("../connect.php");

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
    <div class="container table-container">
        <h2 class="text-center my-4">All Users</h2>
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
                                    <a href="delete.php?userid=<?php echo htmlspecialchars($userInfo["id"]); ?>"
                                        class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>