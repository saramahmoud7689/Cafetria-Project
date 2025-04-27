<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['total'])) {
    $_SESSION['total'] = 0;
}

if (isset($_GET['orderitem']) && isset($_GET['price']) && isset($_GET['action'])) {
    $product_id = intval($_GET['orderitem']);
    $product_price = intval($_GET['price']);
    $action = $_GET['action'];
    $product_name = $_GET['name'];
    $product_image = $_GET['image'];

    if ($action == 'add') {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += 1; 
                $_SESSION['total'] += $product_price;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $product_name,
                'image' => $product_image,
                'price' => $product_price,
                'quantity' => 1
            ];
            $_SESSION['total'] += $product_price;
        }

    } else {
        foreach ($_SESSION['cart'] as $key => &$item) {
            if ($item['id'] == $product_id) {
                if ($action == 'increase') {
                    $item['quantity'] += 1;
                    $_SESSION['total'] += $product_price;
                } elseif ($action == 'decrease') {
                    if ($item['quantity'] > 1) {
                        $item['quantity'] -= 1;
                        $_SESSION['total'] -= $product_price;
                    } else {
                        unset($_SESSION['cart'][$key]);
                        $_SESSION['total'] -= $product_price;
                    }
                } elseif ($action == 'delete') {
                    $_SESSION['total'] -= $product_price * $item['quantity'];
                    unset($_SESSION['cart'][$key]);
                }
                break;
            }
        }
    }

    header('Location: make_order.php');
    exit();
} else {
    echo "No product selected or wrong action.";
}
?>
