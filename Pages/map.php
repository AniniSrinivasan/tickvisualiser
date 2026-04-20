<?php
require_once __DIR__ . '/../functions/search-functions.php';

if (!isset($densityData)) {
    $search = $_GET['search'] ?? '';
    $densityData = getDashboardMapDensity($conn, $search);
}
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
