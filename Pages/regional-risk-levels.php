<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Regional Risk Levels page">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regional Risk Levels • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js"></script>
</head>
<?php

function numberOfTicksInACity($location_name){
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

    return $row['total_ticks'];
}

?>
<!-- order by location and count -->

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->
    
    <section class="banner-section">
        <div class="content-container">
            <h1>The ticks shown below are categorised by city</h1>
        </div>
    </section>

    <main class="risk levels-container" role="main">
        
    </main>

</body>
</html>