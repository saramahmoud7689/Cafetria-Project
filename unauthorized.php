<?php
    session_start();
    session_unset();
    session_destroy();
    echo "<h1>You don't have permission to access this page</h1>";
    echo "<a href='user/make_order.php'><button>Home</button></a>";
?>