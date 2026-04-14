<?php
require_once('../functions/upload-functions.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$action = trim((string) ($_POST['ajax_action'] ?? ''));

if ($action === 'save-row') {
    $rowNum = (int) ($_POST['row_num'] ?? 0);
    $recordId = trim((string) ($_POST['id'] ?? ''));
    $dateTime = trim((string) ($_POST['date_time'] ?? ''));
    $locationName = trim((string) ($_POST['location_name'] ?? ''));
    $speciesName = trim((string) ($_POST['species_name'] ?? ''));
    $latinName = trim((string) ($_POST['species_latin_name'] ?? ''));

    if ($rowNum <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid row number.'
        ]);
        exit;
    }
//make function to add inaccurate data to database
    $updated = addinaccuratedata(
        $conn,
        $rowNum,
        $recordId,
        $dateTime,
        $locationName,
        $speciesName,
        $latinName
    );

    echo json_encode([
        'success' => $updated,
        'message' => $updated ? 'Row updated successfully.' : 'Unable to update row.'
    ]);
    exit;
}

if ($action === 'delete-row') {
    $rowNum = (int) ($_POST['row_num'] ?? 0);

    if ($rowNum <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid row number.'
        ]);
        exit;
    }
//make function to delete inaccurate data from database
    $deleted = deleteinaccuratedata($conn, $rowNum);

    echo json_encode([
        'success' => $deleted,
        'message' => $deleted ? 'Row deleted successfully.' : 'Unable to delete row.'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Unknown action.'
]);