<?php

function getTotalTicksUK(){
    include_once('../functions/db_connect.php');
    $result=$conn->query("SELECT COUNT(*) AS total FROM sighting");
    $row=$result->fetch_assoc();

    return $row['total'] ?? 0;
}

function getTicksInCity($location_name){
    include_once('../functions/db_connect.php');

    $stmt=$conn->prepare(
        "SELECT 
        l.location_name, COUNT(s.sighting_id) 
        AS total_ticks
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        WHERE l.location_name=?
        GROUP BY l.location_name 
        ");

    $stmt->bind_param("s", $location_name);
    $stmt->execute();
    
    $result=$stmt->get_result();
    $row=$result->fetch_assoc();

    return $row['total_ticks'] ?? 0;
}

function getPercentage($location_name){
    include_once('../functions/db_connect.php');
    $totalUK=getTotalTicksUK();
    $cityTicks=getTicksInCity($location_name);

    if ($totalUK==0) return 0;

    return ($cityTicks/$totalUK)*100;
}
?>