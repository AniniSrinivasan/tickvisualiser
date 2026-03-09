<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tick Visualiser</title>
    <link rel="stylesheet" href="style.css">
    <!-- chart.js for plotting the trend graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="app.js"></script>
</head>

<body>

    <!-- top navigation -->
    <header class="top-nav">
        <div class="nav-container">
            <div class="logo-title">Tick Visualiser</div>

            <!-- default hidden on large screens, only appears on mobile screens -->
            <button class="menu-toggle" aria-label="Toggle Menu">
                ☰
            </button>

            <div class="top-menu">
                <a href="#">Home</a>
                <a href="#">Browse Data</a>
                <a href="#">Insights</a>
                <a href="#">Upload</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </header>

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Discover. Analyse. Protect.</h1>

            <form class="search-form">
                <input type="search" placeholder="Search by city or region or tick species...">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </div>
    </section>

    <!-- main dashboard -->
    <main class="dashboard-container">

        <div class="dashboard-grid">

            <!-- summary -->
            <div class="dashboard-card summary-card">
                <div class="card-header">
                    <h2>Total Sightings</h2>
                    <span class="year-badge">2026</span>
                </div>
                <p class="metric-value">12,540</p>
                <p class="card-subtext">Cleaned & validated dataset</p>
            </div>

            <!-- distribution - map view -->
            <div class="dashboard-card map-card">
                <h2>UK Tick Distribution</h2>
                <div class="map-container">
                    Interactive Map Placeholder
                </div>
            </div>

            <!-- trend - graph view -->
            <div class="dashboard-card trend-card">
                <h2>Monthly Trend</h2>
                <canvas id="trendChart"></canvas>
            </div>

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

    </main>

</body>

</html>