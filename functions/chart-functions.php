<?php
require_once('../functions/db_connect.php');

//sql
$query = "SELECT * FROM Species";
$result = $conn->query($query);
$data = [];
while ($row = $result->fetch_assoc()){
    array_push($data,$row);
}
echo json_encode($data);
?>
