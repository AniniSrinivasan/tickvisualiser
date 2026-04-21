<?php

session_start();
// print_r($_SESSION);

function checkUserLoggedIn(){
    if (!isset($_SESSION['email'])){
        header("Location: login.php");
        exit();
    }
}

function adminCheck(){
    checkUserLoggedIn();

    // echo "<pre>";
    // print_r($_SESSION);
    // echo "</pre>";
    // exit();

    if ($_SESSION['role_id']!=="2"){
        die("Access denied!");
    }
}
?>