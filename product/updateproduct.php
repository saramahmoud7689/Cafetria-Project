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
    
    $productid = $_GET['productid'];
    include_once '../connect.php';
    $errors = array();
    $checkExisting = "SELECT * FROM products WHERE id = '$productid'";
    $checkExistingResult = mysqli_query($myConnection, $checkExisting);
    $productInfo = mysqli_fetch_assoc($checkExistingResult);
    $name = $productInfo['name'];
    $price = $productInfo['price'];
    $avalability = $productInfo['avalability'];
    $cat_id = $productInfo['cat_id'];
    $image = $productInfo['image'];

    if (isset($_POST['subBtn'])) {

        if(empty($_POST['name'])) {
            $errors['name'] = "Name is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Product Name is required </div>";
        } 

        if (empty($_POST['price'])) {
            $errors['price'] = "Price is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Product Price is required </div>";
        }

        if (empty($_POST['avalability'])) {
            $errors['avalability'] = "Avalability is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Product Availability is required </div>";
        }

        if (empty($_POST['cat_id'])) {
            $errors['cat_id'] = "Category is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Product Category is required </div>";
        }

        if (empty($errors)) {
            $name = mysqli_real_escape_string($myConnection, $_POST['name']);
            $price = mysqli_real_escape_string($myConnection, $_POST['price']);
            $avalability = mysqli_real_escape_string($myConnection, $_POST['avalability']);
            $cat_id = mysqli_real_escape_string($myConnection, $_POST['cat_id']);
            
            
            if (!empty($_FILES['image']['name'])) {
                $img_name = $_FILES['image']['name'];
                $img_size = $_FILES['image']['size'];
                $tmp_name = $_FILES['image']['tmp_name'];
                $img_exploded = explode('.', $img_name); 
                $img_ex = strtolower(end($img_exploded)); 
                $allowed_exs = array("jpg", "jpeg", "png"); 
                
                if ($img_size > 2097152) {  
                    $errors[] = "Sorry, your file is too large.";
                    echo "<div class='alert alert-danger text-center m-auto w-50'>Product Image must be less than 2 MB </div>";
                } 
                
                if (!in_array($img_ex, $allowed_exs)) {
                    $errors[] = "Extension not allowed, please choose a JPEG, JPG or PNG file.";
                    echo "<div class='alert alert-danger text-center m-auto w-50'>Product Image must be a JPEG, JPG or PNG file </div>";
                }

                if (empty($errors)) {
                    $new_img_name = "../images/".$img_name;
                    move_uploaded_file($tmp_name, $new_img_name);
                    $image = $new_img_name; 
                }
            }

            if (empty($errors)) {
                $sql = "UPDATE products SET 
                        name = '$name', 
                        price = '$price', 
                        avalability = '$avalability', 
                        cat_id = '$cat_id', 
                        image = '$image' 
                        WHERE id = '$productid'";
                
                $result = mysqli_query($myConnection, $sql);
            if ($result) {
                $_SESSION['success_message'] = "Product Updated Successfully";
                header("Location: listproducts.php");
                exit();
            } else {
                    echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to update product: " . mysqli_error($myConnection) . "</div>";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
        integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous">
    </script>

</head>

<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">
            <div class="d-flex flex-column p-3">
                <h4 class="text-center mb-4">Admin Panel</h4>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="listproducts.php" class="nav-link">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="listproducts.php" class="nav-link">
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
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <main class="col-md-10 col-12">
            <h1 class="text-center">Update Product</h1>
            <form method="POST" class="m-4" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $name ?>">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price" value="<?php echo $price ?>">
                </div>
                <div>
                    <label for="avalability" class="form-label">Availability</label>
                    <span class="text-primary">
                        <?php 
                            echo ($avalability == 'true') ? 'Available' : 'Not Available';
                            echo " (current), you can change it below";
                        ?>
                    </span>
                    <select class="form-select mb-3" name="avalability" id="avalability">
                        <option value="true" <?php echo ($avalability == 'true') ? 'selected' : ''; ?>>Available
                        </option>
                        <option value="false" <?php echo ($avalability == 'false') ? 'selected' : ''; ?>>Not Available
                        </option>
                    </select>
                </div>
                <div>
                    <label for="category" class="form-label">Category: </label>
                    <span class="text-primary">
                        <?php 
                            $getCat = "SELECT * FROM categories WHERE id = '$cat_id'";
                            $result = mysqli_query($myConnection, $getCat);
                            $cat = mysqli_fetch_assoc($result);
                            echo $cat['name'] . " (current), you can change it below";
                        ?>
                    </span>
                    <select class="form-select mb-3" name="cat_id" id="category">
                        <?php  
                            $getAllCategories = "SELECT * FROM categories";
                            $result = mysqli_query($myConnection, $getAllCategories);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $selected = ($row['id'] == $cat_id) ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <img src="../images/<?php echo $image ?>" alt="Product Image" width="100" height="100">
                    <input type="file" class="form-control" id="image" name="image" value="<?php echo $image ?>">
                </div>
                <button type="submit" class="btn btn-primary" name="subBtn">Update Product</button>
                <a href="listproducts.php" class="btn btn-danger">Cancel</a>
            </form>
        </main>
    </div>

</body>

</html>