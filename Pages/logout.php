<?php
include('../functions/session.php');
session_destroy();
header("Location: login.php");
exit();
?> 