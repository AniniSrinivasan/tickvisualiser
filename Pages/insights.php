<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Tick Insights">
    <title>Insights • Tick Visualiser</title>
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
            <!-- <h1> General insights for understanding ticks in the UK</h1> -->
        </div>
        <h1> General insights for understanding ticks in the UK</h1>
    </section>

    <!-- main dashboard -->
    <main class="dashboard-container" role="main">
        <div class="dashboard-grid">
            <div class="top-row">
                <!-- Add css -->
                <div class="dashboard-card">
                    <h2>Typical Habitats</h2>
                </div>
                <div class="dashboard-card">
                    <h2>Seasonal Activity</h2>
                </div>
                <div class="dashboard-card">
                    <h2>Commonly found ticks</h2>
                </div>
                <div class="dashboard-card">
                    <h2>Prevention habits</h2>
                </div>
                <div class="dashboard-card">
                    <h2>Tick removal guide</h2>
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