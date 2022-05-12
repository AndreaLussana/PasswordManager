<?php
/*$host = "localhost";
$username = "lussana";
$password = "";
$database = "my_lussana";*/
$host = "localhost";
$username = "root";
$password = "";
$database = "passwordmanager";
$secret_key = "B&E)H@McQfThWmZq4t7w!z%C*F-JaNdRgUkXn2r5u8x/A?D(G+KbPeShVmYq3s6v";
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