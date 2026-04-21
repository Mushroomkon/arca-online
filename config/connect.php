<?php 

/*
    $db_server = "sql202.infinityfree.com";
    $db_username = "if0_40649198";
    $db_password = "9FNk7nBkpJ";
    $db_dbname = "if0_40649198_inventorydb";
    $conn = "";
*/
    $db_server = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_dbname = "inventorydb";
    $conn = "";


    
    $conn = mysqli_connect($db_server, $db_username, $db_password, $db_dbname);
    if (!$conn) {
     die("Connection failed: " . mysqli_connect_error());
    }

?>


