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
                    require_once('../functions/error.php');
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
                    <div class="more-info">
                        <a href="insights.php#more-info">Want more info?</a>
                    </div>  
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
                    <canvas id="MonthlyTrendChart"></canvas>
                </div>
            </div>
            <div class="extra-info" id="more-info">
                <div class="dashboard-card">
                    <div class="card-text">
                        <b>Passerine tick</b>
                        <p>Dermacentor frontalis<br>
                        Generally found in birds nest in woodland, parks and gardens.<br>
                        Feeds on songbirds (passerines) like thrushes, warblers, and blackbirds.</p>
                    </div>
                    <div class="card-image">
                        <img src="../TickImages/PasserineTick.jpg" alt="Passerine tick">
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-text">
                        <b>Fox/Badger tick</b>
                        <p>Ixodes canisuga<br>
                        Commonly found in areas with high populations of foxes and badgers.<br>
                        Feeds on foxes, cats, dogs, horses, badgers and sheep.</p>
                    </div>
                    <div class="card-image">
                        <img src="../TickImages/FoxBadgerTick.jpg" alt="Fox/Badger tick">
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-text">
                        <b>Southern rodent tick</b>
                        <p>Ixodes acuminatus<br>
                        Typically found in grassy and wooded areas, Primarily in southern England<br>
                        It specializes in parasitizing small rodents within their burrows and nests.</p>
                    </div>
                    <div class="card-image">
                        <img src="../TickImages/SouthernRodentTick.jpg" alt="Southern rodent tick">
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-text">
                        <b>Tree-hole tick</b>
                        <p>Ixodes arboricola<br>
                        Commonly found in tree holes and other sheltered areas<br>
                        Infests cavity-nesting birds.</p>
                    </div>
                    <div class="card-image">
                        <img src="../TickImages/TreeHoleTick.jpg" alt="Tree-hole tick">
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-text">
                        <b>Marsh tick</b>
                        <p>Ixodes apronophorus<br> 
                           Typically found in marshy and humid areas<br>
                           Feeds on large animals such as dogs, cattle, sheep, foxes.
                        </p>
                    </div>
                    <div class="card-image">
                        <img src="../TickImages/MarshTick.jpg" alt="Marsh tick">
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>