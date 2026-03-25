<?php
require_once('../functions/db_connect.php');

//SQL
//STR_TO_DATE(si.date_time, '%Y-%m-%dT%H:%i:%s')
$query = 'SELECT (MONTHNAME(si.date_time)) AS month, COUNT(si.sighting_id) AS sighting_count
FROM sighting si
GROUP BY month';
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
