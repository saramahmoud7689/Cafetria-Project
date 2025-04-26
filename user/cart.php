<?php
// Start session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if orderitem ID is sent
if (isset($_GET['orderitem'])) {
    $product_id = intval($_GET['orderitem']); // Always sanitize inputs!

    $found = false;

    // Loop through cart to check if product already exists
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            // Product found, increase quantity
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    unset($item); // break reference with last element

    // If not found, add new product with quantity 1
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'quantity' => 1
        ];
    }

    // Redirect to cart page or back
    header('Location: make_order.php');
    exit();
} else {
    echo "No product selected.";
}
?>
