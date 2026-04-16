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
$showInaccurateOnly = (isset($_GET['show-inaccurate-only']) && (int)$_GET['show-inaccurate-only'] === 1) ? 1 : 0;

if ($action === 'save-row') {
    $rowNum = (int) ($_POST['row_num'] ?? 0);
    $recordId = trim((string) ($_POST['row_id'] ?? ''));
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
    if ($showInaccurateOnly === 1) {
        $updated = updateInaccurateSighting(
            $conn,
            $rowNum,
            $recordId,
            $dateTime,
            $locationName,
            $speciesName,
            $latinName
        );
    }
    else {
        // If we're showing all data, we just want to update the existing row
        $updated = updateSighting(
            $conn,
            $rowNum,
            $recordId,
            $dateTime,
            $locationName,
            $speciesName,
            $latinName
        );
    }
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
    if ($showInaccurateOnly === 1) {
        // If we're showing only inaccurate data, we want to delete from the inaccurate table
        $deleted = deleteInaccurateData($conn, $rowNum);
    } else {
        // If we're showing all data, we want to delete from the sightings table
        $deleted = deleteSighting($conn, $rowNum);
    }

    echo json_encode([
        'success' => $deleted,
        'message' => $deleted ? 'Row deleted successfully.' : 'Unable to delete row.'
    ]);
    exit;
}

if ($action === 'approve-row') {
    $rowNum = (int) ($_POST['row_num'] ?? 0);
    $recordId = trim((string) ($_POST['row_id'] ?? ''));
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
        // move the row to the sightings table
        $approved = addUpdatedInaccurateData(
            $conn,
            $rowNum,
            $recordId,
            $dateTime,
            $locationName,
            $speciesName,
            $latinName,
            $uploadId
        );
        if ($approved) {
            // If the row was successfully added to the sightings table, delete it from the inaccurate table
            deleteInaccurateData($conn, $rowNum);
            
        }

    echo json_encode([
        'success' => $approved,
        'message' => $approved ? 'Row approved successfully.' : 'Unable to approve row.'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Unknown action.'
]);