<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Main Dashboard">
    <title>Dashboard • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <!-- chart.js for plotting the trend graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../script/app.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->
    
    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h2>Discover. Analyse. Protect.</h2>

            <form class="search-form">
                <input type="search" placeholder="Search by city or region or tick species...">
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
                <div class="map-container">
                    Interactive Map Placeholder
                </div>
            </div>

            <!-- summary -->
            <div class="right">
                <div class="dashboard-card summary-card">
                    <div class="card-header">
                        <h2>Total Sightings</h2>
                        <span class="year-badge">2026</span>
                    </div>
                    <p class="metric-value">12,540</p>
                    <p class="card-subtext">Cleaned & validated dataset</p>
                </div>
                <br />
                <!-- trend - graph view -->
                <div class="dashboard-card trend-card">
                    <h2>Monthly Trend</h2>
                    <canvas id="trendChart"></canvas>
                </div>
                <br />
                <!-- risks - progress bar view -->
                <div class="dashboard-card risk-card">
                    <h2>Regional Risk Levels</h2>

                    <div class="risk-item">
                        <label>South East</label>
                        <div class="progress-bar-container">
                            <div class="progress-fill high"></div>
                        </div>
                    </div>

                    <div class="risk-item">
                        <label>Yorkshire</label>
                        <div class="progress-bar-container">
                            <div class="progress-fill medium"></div>
                        </div>
                    </div>

                    <div class="risk-item">
                        <label>Scotland</label>
                        <div class="progress-bar-container">
                            <div class="progress-fill low"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

</body>

</html>