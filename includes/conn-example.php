<?php
    $host = 'localhost';
    $port = 3307;
    $db = 'inventory_db';
    $user = 'root';
    $password = 'abc';//replace "abc" with your password!!!

    $conn = mysqli_connect($host, $user, $password, $db, $port);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    //echo "Connected successfully";
?>