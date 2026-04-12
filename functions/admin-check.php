<?php

session_start();

if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if ($_SESSION['role_id']!==2){
    header("Location: dashboard.php");
    exit();
}

?>