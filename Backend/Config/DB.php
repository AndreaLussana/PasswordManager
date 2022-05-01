<?php
include('Config.php');
if(checktbl($conn)){

}else{
    exit();
}

function checktbl($conn){
    $query = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int NOT NULL AUTO_INCREMENT,
        `pwd` varchar(254) NOT NULL,
        `skey` varchar(254) NOT NULL,
        PRIMARY KEY (`id`)
       ); CREATE TABLE IF NOT EXISTS `team` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(254) NOT NULL UNIQUE,
        `admin_id` int DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `admin_id` (`admin_id`)
        ); CREATE TABLE IF NOT EXISTS `element` (
        `id` int NOT NULL AUTO_INCREMENT,
        `user_id` int NOT NULL,
        `name` varchar(50) NOT NULL,
        `email` varchar(254) NOT NULL,
        `pwd` varchar(254) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
        ); CREATE TABLE IF NOT EXISTS `detuser` (
        `id` int NOT NULL AUTO_INCREMENT,
        `user_id` int NOT NULL,
        `team_id` int DEFAULT NULL,
        `privilege` int DEFAULT NULL,
        `googlesign` tinyint(1) NOT NULL,
        `fa` tinyint(1) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `team_id` (`team_id`)
        ); CREATE TABLE IF NOT EXISTS `detelement` (
        `id` int NOT NULL AUTO_INCREMENT,
        `element_id` int NOT NULL,
        `url` varchar(254) DEFAULT NULL,
        `favorite` tinyint(1) DEFAULT NULL,
        `note` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `element_id` (`element_id`)
        ) ;
        ";
    if($conn->multi_query($query)){
        do {
            if ($result = $conn -> store_result()) {
              while ($row = $result -> fetch_row()) {
                printf("%s\n", $row[0]);
              }
                $result -> free_result();
            }
          } while ($conn -> next_result());
          return true;
    }else{
        echo "Error gen tables";
        return false;
    }
}
?>