<?php
session_start();
include('../functions/db_connect.php');
include('../functions/risk-levels-functions.php');
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

while($row=$result->fetch_assoc()){
    $location_name=$row['location_name'];

    $ticks=getTicksInCity($location_name);
    $percentage=getPercentage($location_name);
}

?>
<!-- order by location and count -->

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->
    
    <section class="banner-section">
        <div class="content-container">
            <h1>Regional Risk Levels</h1>
        </div>
    </section>

    <main class="risk levels-container" role="main">
        <div class="city">
            <label><?php echo $city; ?>(<?php echo $ticks; ?> ticks)</label>

            <div class="risk-progress-bar">
        </div>
    </main>

</body>
</html>