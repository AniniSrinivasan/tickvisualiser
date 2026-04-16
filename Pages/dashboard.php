<?php
include('../functions/session.php');
// print_r($_SESSION);
// checkUserLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Main Dashboard">
    <title>Dashboard • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <!-- chart.js for plotting the trend graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../script/script.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h2>Discover. Analyse. Protect.</h2>

            <form class="search-form" method="GET" action="">
                <input type="search" name="search" autocomplete="off"
                    placeholder="Search by city or region or tick species..."
                    value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </div>
    </section>

    <!-- main dashboard -->
    <main class="dashboard-container" role="main">
        <div class="dashboard-grid">

            <!-- distribution - map view -->
            <div class="dashboard-card map-card left">
                <h2>UK Tick Distribution</h2>
                <!-- <div class="map-container"> -->
                <?php include 'map.php'; ?>
                <!-- </div> -->
            </div>

            <!-- summary -->
            <div class="right">
                <div class="dashboard-card summary-card">
                    <a href="total-sightings.php">
                        <div class="card-header">
                            <h2>Total Sightings</h2>
                            <span class="year-badge">2026</span>
                        </div>
                        <p class="metric-value">12,540</p>
                        <p class="card-subtext">Cleaned & validated dataset</p>
                    </a>
                </div>
                <br />
                <!-- trend - graph view -->
                <div class="dashboard-card trend-card">
                    <h2>Monthly Trend (6 months)</h2>
                    <canvas id="MonthlyTrendChart" data-range="6"></canvas>
                </div>
                <br />
                <!-- risks - progress bar view -->
                <a href="regional-risk-levels.php">
                    <div class="dashboard-card risk-card">
                        <h2>National Risk Levels</h2>
                        <?php 
                        include_once('../functions/risk-levels-functions.php');
                        $cities = getTicksInCityLimit3($conn);
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
                            <div class="risk-item">
                                <label><?php echo $city; ?></label>
                                <div class="risk-progress-bar">
                                    <div class="risk-progress-fill <?php echo $class; ?>"
                                        style="width: <?php echo $percentage; ?>%; border:thick;">
                                        <?php echo round($percentage); ?>%
                                    </div>
                                </div>
                            </div>
                            <br />
                        <?php
                        }
                        ?>
                            <div class="more-info">
                                <!-- Goes to extra info section -->
                                <a href="regional-risk-levels.php">Want more info?</a>
                            </div>
                    </div>
                </a>
            </div>

        </div>

    </main>

</body>

</html>