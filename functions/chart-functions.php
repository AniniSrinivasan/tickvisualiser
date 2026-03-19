<?php
require_once('../functions/db_connect.php');

//SQL
$query = "SELECT * FROM species";
$result = $conn->query($query);
$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; 
    }
}
echo json_encode($data);
$conn->close();
?>
