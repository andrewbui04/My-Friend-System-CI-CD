<?php
    $host = getenv('DB_HOST') ?: 'mysql-db';  // Get DB_HOST from environment variables
    $user = getenv('DB_USER') ?: 'myuser'; 
    $pswd = getenv('DB_PASS') ?: 'mypassword'; 
    $dbmn = getenv('DB_NAME') ?: 'my_friend_system'; 
?>
