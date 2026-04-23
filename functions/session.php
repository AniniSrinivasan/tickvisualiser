<?php

session_start();

function checkUserLoggedIn(){
    if (!isset($_SESSION['email'])){
        header("Location: login.php");
        exit();
    }
}

function adminCheck(){
    checkUserLoggedIn();

    if ($_SESSION['role_id']!="2"){
        die("Access denied!");
    }
}
?>