<?php
include('../functions/session.php');
session_destroy();
header("Location: dashboard.php");
exit();
?> 