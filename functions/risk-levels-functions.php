<?php

function getTotalTicksUK($conn){
    include_once('../functions/db_connect.php');
    $result=$conn->query("SELECT COUNT(*) AS total FROM sighting");
    $row=$result->fetch_assoc();

    return $row['total'] ?? 0;
}

function getTicksInCity( $conn){
    include_once('../functions/db_connect.php');

    $query="SELECT 
        l.location_name, COUNT(s.row_num) 
        AS total_ticks
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        -- WHERE l.location_name=?
        GROUP BY l.location_name 
        ORDER BY total_ticks DESC
        ";

    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row; 
        }
    }
    return $data;
}

function getPercentage($ticks, $totalUK){
    if ($totalUK==0) return 0;

    //if *100 percentage is too small
    return ($ticks/$totalUK)*1000;
}

function getTicksInCityLimit3( $conn){
    include_once('../functions/db_connect.php');

    $query="SELECT 
        l.location_name, COUNT(s.row_num) 
        AS total_ticks
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        -- WHERE l.location_name=?
        GROUP BY l.location_name 
        ORDER BY total_ticks DESC
        LIMIT 3
        ";

    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row; 
        }
    }
    return $data;
}
?>