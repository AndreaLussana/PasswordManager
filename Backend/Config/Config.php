<?php
/*$host = "localhost";
$username = "lussana";
$password = "";
$database = "my_lussana";*/
$host = "localhost";
$username = "root";
$password = "";
$database = "passwordmanager";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
  $conn = new mysqli($host, $username, $password);
  $query = "CREATE DATABASE IF NOT EXISTS passwordmanager;";
    if($conn->query($query)){  
    }else{
        echo "Error database";
    }
}
?>