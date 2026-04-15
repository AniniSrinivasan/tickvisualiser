<?php
require_once __DIR__ . '/db_connect.php';

function getTickDensityByLocation($search = null)
{
    global $conn;

    $data = [];

    $sql = "
        SELECT 
            l.location_name AS area_name,
            COUNT(s.row_num) AS tick_count
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        INNER JOIN species sp ON s.species_id = sp.species_id
    ";

    if ($search !== null && trim($search) !== '') {
        $searchSafe = $conn->real_escape_string(trim($search));
        $sql .= "
            WHERE l.location_name LIKE '%$searchSafe%'
               OR sp.species_name LIKE '%$searchSafe%'
               OR sp.species_latin_name LIKE '%$searchSafe%'
        ";
    }

    $sql .= "
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