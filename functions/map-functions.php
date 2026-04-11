<?php
require_once __DIR__ . '/db_connect.php';

function getTickDensityByLocation()
{
    global $conn;

    $data = [];

    $sql = "
        SELECT 
            l.location_name AS area_name,
            COUNT(s.row_num) AS tick_count
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        GROUP BY l.location_name
        ORDER BY l.location_name
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[$row['area_name']] = (int) $row['tick_count'];
        }
    }

    return $data;
}