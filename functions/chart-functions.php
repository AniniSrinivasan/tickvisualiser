<?php
require_once('../functions/db_connect.php');

//SQL
$query = "SELECT s.species_name,s.species_latin_name, COUNT(si.row_num) AS sighting_count
FROM species s
LEFT JOIN sighting si ON s.species_id = si.species_id
GROUP BY s.species_id,s.species_latin_name";
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
