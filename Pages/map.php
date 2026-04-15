<?php
require_once __DIR__ . '/../functions/map-functions.php';

$search = $_GET['search'] ?? null;
$densityData = getTickDensityByLocation($search);
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="dashboard-map-wrap">
    <div class="dashboard-map-inner">
        <div id="map"></div>
    </div>
</div>

<script>
    window.densityByName = <?php echo json_encode($densityData, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const map = L.map('map', {
            zoomControl: true
        });

        document.getElementById('map')._leaflet_map = map;

        setTimeout(() => {
            map.invalidateSize(true);
        }, 200);

        window.addEventListener('resize', () => {
            map.invalidateSize(true);
        });
    });
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>