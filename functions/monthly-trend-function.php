<?php
require_once('../functions/db_connect.php');

// Check if months parameter exists
// $months = isset($_GET['months']) ? (int)$_GET['months'] : null;
$months = 6;
$query = "SELECT DATE_FORMAT(si.date_time, '%m') AS month_num,
DATE_FORMAT(si.date_time, '%b') AS month,
COUNT(si.row_num) AS sighting_count
FROM sighting si
GROUP BY DATE_FORMAT(si.date_time, '%m')
ORDER BY DATE_FORMAT(si.date_time, '%m');";

$result = $conn->query($query);
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Filter data based on the months parameter if it exists
if ($months !== null) {
    $currentMonth = (int)date('m');
    $filtered = [];
    foreach ($data as $row) {
        $monthNum = (int)$row['month_num'];

        // Handle wrapping across year boundary
        $diff = $currentMonth - $monthNum;
        if ($diff < 0) {
            $diff += 12;
        }
        if ($diff < $months) {
            if ($diff < $currentMonth) {
                $filtered[] = $row;
            }
            // If the month is within the range but falls in the previous year, wrap it to the end of the array
            else{
                $wrapDates[] = $row;}
            }
    }
    // Add wrapped months to the beginning of the filtered array
    if (count($wrapDates) > 0) {
    array_unshift($filtered, ...$wrapDates);
    }

    $data = $filtered;
}

echo json_encode($data);
$conn->close();
?>