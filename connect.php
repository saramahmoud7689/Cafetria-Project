<?php
    $serverName = "localhost";
    $userName = "root";
    $password = "";
    $databaseName = "cafteria_db";
    
    $myConnection = mysqli_connect($serverName, $userName, $password, $databaseName);

    if (!$myConnection) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        //  echo "Connected successfully";
    }
?>