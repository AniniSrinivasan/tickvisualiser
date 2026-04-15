<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Total-Sightings • Tick Visualiser</title>
    <script src="../script/script.js"></script> 
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>
    <?php
    function totalSightings($total) {
        $total = SELECT * FROM Tick_Sightings;
    }
    ?>
    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Total Sightings in the Database:</h1>
            <h2>
                <?php totalSightings()
                 echo $total; ?>
            </h2> <!-- update to live database data with php -->
        </div>
    </section>

</body>
</html>