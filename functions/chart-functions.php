<?php
require_once('../functions/db_connect.php');

//SQL
$query = "SELECT * FROM species";
// $query = "SELECT s.species_id,s.species_name,";
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
