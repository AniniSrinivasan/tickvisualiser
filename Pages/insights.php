<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Tick Insights">
    <title>Insights • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <!-- chart.js for plotting the trend graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../script/script.js" defer></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <!-- <h1> General insights for understanding ticks in the UK</h1> -->
        </div>
        <h1> General insights for understanding ticks in the UK</h1>
    </section>

    <!-- main dashboard -->
    <main class="dashboard-container" role="main">
        <div class="insight-grid">
            <div class="top-row">
                <!-- Add css -->
                <div class="dashboard-card">
                    <h2>Typical Habitats</h2><br>
                    <p>Ticks in the UK are most commonly found in damp, shady areas with tall or dense vegetation.</p>
                    <p>They are common in urban parks, gardens, and along shaded walking paths.</p>
                </div>
                <div class="dashboard-card">
                    <h2>Seasonal Activity</h2><br>
                    <p>Most active from April to October, peaking between April and July, as they thrive in warm, humid conditions.</p>
                    <p>Activity reduces in winter but they can appear if temperatures are above freezing.</p>
                </div>
                <div class="dashboard-card">
                    <h2>Commonly found ticks</h2><br>
                    <?php
                    require_once('../functions/db_connect.php');
                    $result = mysqli_query($conn, "
                        SELECT s.species_name, COUNT(si.sighting_id) AS sighting_count
                        FROM species s
                        LEFT JOIN sighting si ON s.species_id = si.species_id
                        GROUP BY s.species_id
                        ORDER BY sighting_count DESC
                        LIMIT 5
                    ");// Get top 5 most sighted species
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<p>- " . $row['species_name'] . "</p>";
                    }
                    ?>
                </div>
                <div class="dashboard-card">
                    <h2>Prevention habits</h2><br>
                    <p>-Keep to footpaths and avoid walking through dense vegetation.</p>
                    <p>-Cover your skin and tuck your trousers into your socks.</p>
                    <p>-Wearing light coloured clothing will make ticks easier to spot and brush off.</P>
                    <p>-Using insect repellent containing DEET can reduce the chances of getting bitten.</p>
                    </p>
                </div>
                <div class="dashboard-card">
                    <h2>Tick removal guide</h2><br>
                    <p>-Use fine-tipped tweezers or special tick removers; both are available in most pharmacies.</p>
                    <p>-Grasp the tick as close to the skin as possible and slowly pull upwards. Avoid crushing or squeezing the tick.</p>
                    <p>-Clean the bite with antiseptic or soap and water.</p>
                </div>
            </div>
            <div class="bottom-row">
                <div class="dashboard-card">
                    <!-- Bar graph -->
                    <h2>Graph:</h2>
                    <canvas id="BarChart"></canvas>
                </div>
                <div class="dashboard-card">
                    <!-- Line graph -->
                    <h2>Graph:</h2>
                    <canvas id="trendChart"></canvas>
                    
                </div>
            </div>
        </div>
    </main>
</body>

</html>