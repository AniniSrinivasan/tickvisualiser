<?php
include("../functions/session.php");
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
<!-- order by location and count -->

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->

    <section class="banner-section">
        <div class="content-container">
            <h1>National Risk Levels</h1>
        </div>
    </section>

    <main class="risk-levels-container" role="main">
        <div class="col-grp">
            <?php

            $cities = getTicksInCity($conn);
            $totalUK = getTotalTicksUK($conn);


            foreach($cities as $row){
                $city = $row['location_name'];
                $ticks = $row['total_ticks'];

                $percentage = getPercentage($ticks, $totalUK);

                if ($percentage < 60) {
                    $class = "low-risk";
                } elseif ($percentage < 75) {
                    $class = "medium-risk";
                } else {
                    $class = "high-risk";
                }

                ?>
                <div class="risk-grid">
                    <div class="risk-card">
                        <div class="city">
                            <label><?php echo $city; ?></label>
                            <label>(<?php echo $ticks; ?> ticks)</label>

                            <div class="risk-progress-bar">
                                <div class="risk-progress-fill <?php echo $class; ?>"
                                    style="width: <?php echo $percentage; ?>%;">
                                    <?php echo round($percentage); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </main>

</body>

</html>