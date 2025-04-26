<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../connect.php';

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

        if (empty($_FILES['image'])) {  
            $errors['image'] = "Image is required";
            echo "<div class='alert alert-danger text-center m-auto w-50'>Product Image is required </div>";
        }

        if (empty($errors)) {
            $name = mysqli_real_escape_string($myConnection, $_POST['name']);
            $price = mysqli_real_escape_string($myConnection, $_POST['price']);
            $avalability = mysqli_real_escape_string($myConnection, $_POST['avalability']);
            $cat_id = mysqli_real_escape_string($myConnection, $_POST['cat_id']);
            $image = $_FILES['image'];
            $errors = [];


            $img_name = $_FILES['image']['name'];
            $img_size = $_FILES['image']['size'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $img_ex = strtolower(end(explode('.', $img_name)));
            $allowed_exs = array("jpg", "jpeg", "png"); 
            
            if ($img_size > 2097152) {  
                $errors[] = "Sorry, your file is too large.";
                echo "<div class='alert alert-danger text-center m-auto w-50'>Product Image must be less than 2 MB </div>";
            } 
            
            if (in_array($img_ex, $allowed_exs) == false) {
                $errors[] = "Extension not allowed, please choose a JPEG, JPG or  PNG file.";
                echo "<div class='alert alert-danger text-center m-auto w-50'>Product Image must be a JPEG, JPG or  PNG file </div>";
            }

            if (empty($errors)) {
                $new_img_name = "../images/".$img_name;
                move_uploaded_file($tmp_name, $new_img_name);
                $sql = "INSERT INTO `products` (`name`, `price`, `avalability`, `cat_id`, `image`) VALUES ('$name', $price, '$avalability', $cat_id, '$new_img_name')";
                $result = mysqli_query($myConnection, $sql);
                if($result) {
                    echo "<div class='alert alert-success text-center m-auto w-50'>Product Added Successfully</div>";
                    header("Location: listproducts.php");
                } else {
                    $errors['add'] = "Failed to add product: " . mysqli_error($myConnection);
                    echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to add product: " . mysqli_error($myConnection) . "</div>";
                }
            }
        }else {
            echo "<div class='alert alert-danger text-center m-auto w-50'>Failed to add product: " . mysqli_error($myConnection) . "</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="page-container row">
        <aside class="col-md-2 col-12">
                <div class="d-flex flex-column p-3">
                    <h4 class="text-center mb-4">Admin Panel</h4>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="listproducts.php" class="nav-link">
                                <i class="fas fa-home me-2"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="listproducts.php" class="nav-link">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-users me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-shopping-cart me-2"></i> Manual Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-list-check me-2"></i> Checks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-shield me-2"></i> Admin
                            </a>
                        </li>
                    </ul>
                </div>
        </aside>
        <main class="col-md-10 col-12">
            <h1 class="text-center">Add Product</h1>
            <form method="POST" class="m-4" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price">
                </div>
                <div>
                    <label for="avalability" class="form-label">Availability</label>
                    <select class="form-select mb-3" name="avalability" id="avalability">
                        <option value="true">Available</option>
                        <option value="false">Not Available</option>
                    </select>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <a href="../category/category.php"><button type="button" class="btn btn-primary mx-3">Add New Category</button></a>
                    </div>
                    
                    <select class="form-select mb-3" name="cat_id" id="category">
                        <?php  
                            include_once '../connect.php';
                            $getAllCategories = "SELECT * FROM categories";
                            $result = mysqli_query($myConnection, $getAllCategories);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>
                <button type="submit" class="btn btn-primary" name="subBtn">Add Product</button>
                <a href="listproducts.php" class="btn btn-danger">Cancel</a>
            </form>
        </main>
    </div>
    
    
</body>
</html>