<?php

require_once('../functions/db_connect.php');
date_default_timezone_set('Europe/London');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

$csvRows = [];
$uploadErrorMessage = '';
$uploadSuccessMessage = '';
$uploadedFilesThisRequest = [];
$editingRowId = null;
$selectedUploadId = 0;

$uploadDirectory = __DIR__ . '/../upload-files';
$storedFiles = getStoredFiles($conn);

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function isAjaxRequest(): bool
{
    return (
        isset($_POST['ajax_action']) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    );
}

function jsonResponse(bool $success, string $message = '', array $data = [], int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
    ], $data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ensureUploadDirectory(string $directory): bool
{
    if (is_dir($directory)) {
        return is_writable($directory);
    }

    return mkdir($directory, 0775, true);
}

function getStoredFiles(mysqli $conn): array
{
    $items = [];

    $sql = "SELECT upload_id, upload_name, upload_date
            FROM upload
            ORDER BY upload_date DESC, upload_id DESC
            LIMIT 10";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'upload_id' => (int) $row['upload_id'],
            'stored_name' => $row['upload_name'],
            'display_name' => $row['upload_name'],
            'original_name' => getOriginalFileName($row['upload_name']),
            'uploaded_at' => $row['upload_date'],
        ];
    }

    return $items;
}

function getRowsByUploadId(mysqli $conn, int $uploadId, bool $showInaccurateOnly): array
{
    $rows = [];
    //select from inaccurate_sighting if $showInaccurateOnly is true
    if ($showInaccurateOnly) {
        $sql = "SELECT 
            row_num,
            id,
            date_time,
            city as location_name,
            --  county,
            species as species_name,
            latin_name as species_latin_name
            from inaccurate_sighting
            where upload_id = ?";
            
        } else {
            // select from sighting if $showInaccurateOnly is false (default)
            $sql = "SELECT 
                s.row_num,
                s.id,
                s.date_time,
                l.location_name,
                sp.species_name,
                sp.species_latin_name
            FROM sighting s
            INNER JOIN species sp ON s.species_id = sp.species_id
            INNER JOIN location l ON s.location_id = l.location_id
            WHERE s.upload_id = ?
            ORDER BY s.row_num";
        }


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uploadId);
    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'row_num' => $row['row_num'] ?? '',
            'id' => $row['id'] ?? '',
            'date_time' => $row['date_time'] ?? '',
            'location_name' => $row['location_name'] ?? '',
            // 'county' => $row['county'] ?? '',
            'species_name' => $row['species_name'] ?? '',
            'species_latin_name' => $row['species_latin_name'] ?? '',
        ];
    }

    $stmt->close();

    return $rows;
}

function getOrCreateSpecies(mysqli $conn, string $speciesName, string $latinName): int
{
    $speciesName = trim($speciesName);
    $latinName = trim($latinName);

    if ($speciesName === '' || $latinName === '') {
        throw new RuntimeException('Species name and latin name are required.');
    }

    $sql = "INSERT INTO species (species_name, species_latin_name)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE species_id = LAST_INSERT_ID(species_id)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $speciesName, $latinName);
    $stmt->execute();

    $speciesId = (int) $conn->insert_id;
    $stmt->close();

    if ($speciesId <= 0) {
        throw new RuntimeException('Unable to get species ID.');
    }

    return $speciesId;
}
//Not currently used, but could be used in future if we want to allow new locations to be added through uploads
function getOrCreateLocation(mysqli $conn, string $locationName, string $county = ''): int
{
    $locationName = trim($locationName);

    if ($locationName === '') {
        throw new RuntimeException('Location name is required.');
    }

    $sql = "INSERT INTO location (location_name)
            VALUES (?)
            ON DUPLICATE KEY UPDATE location_id = LAST_INSERT_ID(location_id)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $locationName);
    $stmt->execute();

    $locationId = (int) $conn->insert_id;
    $stmt->close();

    if ($locationId <= 0) {
        throw new RuntimeException('Unable to get location ID.');
    }

    return $locationId;
}
function getLocationId(mysqli $conn, string $locationName): int
{
    $locationName = trim($locationName);

    if ($locationName === '') {
        throw new RuntimeException('Location name is required.');
    }

    $sqlSelect = "SELECT location_id FROM location 
                  WHERE location_name = ? 
                  LIMIT 1";

    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("s", $locationName);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    if ($row = $result->fetch_assoc()) {
        $locationId = (int) $row['location_id'];
        $stmtSelect->close();
        return $locationId;
    }

    $stmtSelect->close();
    throw new RuntimeException('Location not found.');
}
function updateSighting(mysqli $conn,int $rowNum,string $recordId,string $dateTime,string $locationName,string $speciesName,string $latinName
): bool {
    try {
        $speciesId = getOrCreateSpecies($conn, $speciesName, $latinName);
        $locationId = getLocationId($conn, $locationName);
        $normalisedDateTime = normaliseDateTime($dateTime);

        if ($recordId === '' || $normalisedDateTime === null) {
            throw new RuntimeException('Record ID and a valid date/time are required.');
        }

        $sql = "UPDATE sighting
                SET id = ?, date_time = ?, species_id = ?, location_id = ?
                WHERE row_num = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $recordId, $normalisedDateTime, $speciesId, $locationId, $rowNum);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected >= 0;
    } catch (Throwable $e) {
        return false;
    }
}

function deleteSighting(mysqli $conn, int $rowNum): bool
{
    try {
        $sql = "DELETE FROM sighting
                WHERE row_num = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rowNum);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();

        return $deleted;
    } catch (Throwable $e) {
        return false;
    }
}
function updateInaccurateSighting(mysqli $conn,int $rowNum,string $recordId,string $dateTime,string $locationName,string $speciesName,string $latinName
): bool{
    error_log("updateInaccurateSighting CALLED with rowNum: $rowNum");
    try {
        $sql = "UPDATE inaccurate_sighting
                SET id = ?, species = ?, latin_name = ?, city = ?, date_time = ?
                WHERE row_num = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $recordId, $speciesName, $latinName, $locationName, $dateTime, $rowNum);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected >= 0;
    } catch (Throwable $e) {
        error_log("updateInaccurateSighting failed: " . $e->getMessage());
        return false;
    }
}
function addUpdatedInaccurateData(mysqli $conn,int $rowNum,string $recordId,string $dateTime,string $locationName,string $speciesName,string $latinName,int $uploadId
): bool {
    try {
        // Get correct foreign keys
        $speciesId = getOrCreateSpecies($conn, $speciesName, $latinName);
        $locationId = getOrCreateLocation($conn, $locationName);
        $normalisedDateTime = normaliseDateTime($dateTime);

        if ($recordId === '' || $normalisedDateTime === null) {
            throw new RuntimeException('Invalid data.');
        }

        $sql = "INSERT INTO sighting (id, date_time, species_id, location_id, upload_id)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $recordId, $normalisedDateTime, $speciesId, $locationId, $uploadId);
        $stmt->execute();

        $inserted = $stmt->affected_rows > 0;
        $stmt->close();

        return $inserted;

    } catch (Throwable $e) {
        error_log("UPDATE ERROR: " . $e->getMessage());
        return false;
    }
}
function deleteInaccurateData(mysqli $conn, int $rowNum): bool
{
    try {
        $sql = "DELETE FROM inaccurate_sighting
                WHERE row_num = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rowNum);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();

        return $deleted;
    } catch (Throwable $e) {
        return false;
    }
}

function parseCsvFile(string $filePath, mysqli $conn): array
{
    if (!is_readable($filePath)) {
        throw new RuntimeException('Uploaded file is not readable.');
    }

    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        throw new RuntimeException('Unable to open uploaded file.');
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        throw new RuntimeException('CSV file is empty or invalid.');
    }

    $header = array_map(static fn($item) => trim((string) $item), $header);

    $uploadName = basename($filePath);
    $uploadDate = date('Y-m-d H:i:s');

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO upload (upload_name, upload_date) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $uploadName, $uploadDate);
        $stmt->execute();
        $uploadId = (int) $conn->insert_id;
        $stmt->close();

        $insertSightingSql = "INSERT INTO sighting (id, species_id, location_id, date_time, upload_id)
                              VALUES (?, ?, ?, ?, ?)";
        $insertSightingStmt = $conn->prepare($insertSightingSql);

        while (($data = fgetcsv($handle)) !== false) {
            if (count(array_filter($data, fn($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $rowAssoc = [];
            foreach ($header as $index => $columnName) {
                $rowAssoc[$columnName] = $data[$index] ?? '';
            }

            $recordId = trim((string) ($rowAssoc['id'] ?? ''));
            $rawDateTime = trim((string) ($rowAssoc['date_time'] ?? $rowAssoc['date'] ?? ''));
            $locationName = trim((string) ($rowAssoc['location_name'] ?? $rowAssoc['city'] ?? $rowAssoc['location'] ?? ''));
            $county = trim((string) ($rowAssoc['county'] ?? ''));
            $speciesName = trim((string) ($rowAssoc['species_name'] ?? $rowAssoc['species'] ?? ''));
            $latinName = trim((string) ($rowAssoc['species_latin_name'] ?? $rowAssoc['latin_name'] ?? $rowAssoc['latinName'] ?? ''));

            $dateTime = normaliseDateTime($rawDateTime);

            $hasAccurateData = (
                $recordId !== '' &&
                $speciesName !== '' &&
                $latinName !== '' &&
                $locationName !== '' &&
                $dateTime !== null &&
                //string length check for record ID
                strlen($recordId) === 20 
            );

            if (!$hasAccurateData) {
                insertInaccurateSighting(
                    $conn,
                    $recordId,
                    $speciesName,
                    $latinName,
                    $locationName,
                    $rawDateTime,
                    $uploadId
                );
                continue;
            }

            try {
                $speciesId = getOrCreateSpecies($conn, $speciesName, $latinName);
                $locationId = getLocationId($conn, $locationName);

                $insertSightingStmt->bind_param("siisi", $recordId, $speciesId, $locationId, $dateTime, $uploadId);
                $insertSightingStmt->execute();
            } catch (Throwable $e) {
                insertInaccurateSighting(
                    $conn,
                    $recordId,
                    $speciesName,
                    $latinName,
                    $locationName,
                    $rawDateTime,
                    $uploadId
                );
            }
        }

        $insertSightingStmt->close();
        fclose($handle);

        $conn->commit();
        $showInaccurateOnly = isset($_GET['show-inaccurate-only']) && $_GET['show-inaccurate-only'] == '1';
        return [
            'rows' => getRowsByUploadId($conn, $uploadId,$showInaccurateOnly),
            'upload_id' => $uploadId,
        ];
    } catch (Throwable $e) {
        $conn->rollback();
        fclose($handle);
        throw $e;
    }
}

function getOriginalFileName(string $storedName): string
{
    $nameWithoutExt = pathinfo($storedName, PATHINFO_FILENAME);
    $extension = pathinfo($storedName, PATHINFO_EXTENSION);

    $parts = explode('_', $nameWithoutExt);

    if (count($parts) < 3) {
        return $storedName;
    }

    $originalParts = array_slice($parts, 0, -3);

    return implode('_', $originalParts) . ($extension !== '' ? '.' . $extension : '');
}

function normaliseDateTime(?string $value): ?string
{
    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    $value = str_replace('T', ' ', $value);

    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $value);
    if ($dateTime instanceof DateTime && $dateTime->format('Y-m-d H:i:s') === $value) {
        return $dateTime->format('Y-m-d H:i:s');
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return null;
    }

    return date('Y-m-d H:i:s', $timestamp);
}

function insertInaccurateSighting(
    mysqli $conn,
    string $recordId,
    string $species,
    string $latinName,
    string $city,
    string $dateTime,
    int $uploadId
): void {
    $sql = "INSERT INTO inaccurate_sighting (id, species, latin_name, city, date_time, upload_id)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $recordId, $species, $latinName, $city, $dateTime, $uploadId);
    $stmt->execute();
    $stmt->close();
}

function processUpload(mysqli $conn, array $file, string $uploadDirectory): array
{
    if (!ensureUploadDirectory($uploadDirectory)) {
        throw new RuntimeException('Upload folder could not be created or is not writable.');
    }

    $fileName = trim((string) ($file['name'] ?? ''));
    $tmpName = $file['tmp_name'] ?? '';
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('There was a problem uploading the file.');
    }

    if (!preg_match('/\.csv$/i', $fileName)) {
        throw new RuntimeException('Only CSV files can be attached.');
    }

    if ($tmpName === '') {
        throw new RuntimeException('No file was uploaded.');
    }

    $originalBaseName = pathinfo($fileName, PATHINFO_FILENAME);
    $sanitisedBaseName = preg_replace('/[^A-Za-z0-9_-]/', '-', $originalBaseName);
    $timestamp = date('Ymd_His');
    $uniqueId = substr(bin2hex(random_bytes(6)), 0, 12);

    if ($sanitisedBaseName === '' || $sanitisedBaseName === null) {
        $sanitisedBaseName = 'upload';
    }

    $storedFileName = $sanitisedBaseName . '_' . $uniqueId . '_' . $timestamp . '.csv';
    $destinationPath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedFileName;

    if (!move_uploaded_file($tmpName, $destinationPath)) {
        throw new RuntimeException('Unable to save uploaded file into upload-files folder.');
    }

    $parsedData = parseCsvFile($destinationPath, $conn);

    return [
        'selected_upload_id' => (int) ($parsedData['upload_id'] ?? 0),
        'rows' => $parsedData['rows'] ?? [],
        'stored_files' => getStoredFiles($conn),
    ];
}

function handleAjaxRequest(mysqli $conn, string $uploadDirectory): void
{

    $action = trim((string) ($_POST['ajax_action'] ?? ''));
    //get or post 
    $showInaccurateOnly = isset($_GET['show-inaccurate-only']) && $_GET['show-inaccurate-only'] == '1';
    try {
        switch ($action) {
            case 'get-upload-data':
                $uploadId = (int) ($_POST['upload_id'] ?? 0);

                jsonResponse(true, 'Upload data loaded successfully.', [
                    'selected_upload_id' => $uploadId,
                    'rows' => $uploadId > 0 ? getRowsByUploadId($conn, $uploadId, $showInaccurateOnly) : [],
                    'stored_files' => getStoredFiles($conn),
                ]);
                break;

            case 'upload-csv':
                if (!isset($_FILES['csv_files'])) {
                    jsonResponse(false, 'No file was uploaded.', [], 400);
                }

                $result = processUpload($conn, $_FILES['csv_files'], $uploadDirectory);
                jsonResponse(true, '1 file uploaded successfully.', $result);
                break;

            case 'save-row':
                $uploadId = (int) ($_POST['upload_id'] ?? 0);
                $rowNum = (int) ($_POST['row_num'] ?? 0);
                $recordId = trim((string) ($_POST['row_id'] ?? ''));
                $dateTime = trim((string) ($_POST['date_time'] ?? ''));
                $locationName = trim((string) ($_POST['location_name'] ?? ''));
                $speciesName = trim((string) ($_POST['species_name'] ?? ''));
                $latinName = trim((string) ($_POST['species_latin_name'] ?? ''));

                // if ($rowNum <= 0 || $uploadId <= 0) {
                //     jsonResponse(false, 'Invalid row or upload selected.', [], 400);
                // }

                // if (!updateSighting($conn, $rowNum, $recordId, $dateTime, $locationName, $speciesName, $latinName)) {
                //     jsonResponse(false, 'Unable to update row.', [
                //         'selected_upload_id' => $uploadId,
                //         'rows' => getRowsByUploadId($conn, $uploadId,$showInaccurateOnly),
                //     ], 400);
                // }

                // jsonResponse(true, 'Row updated successfully.', [
                //     'selected_upload_id' => $uploadId,
                //     'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                // ]);
                if ($showInaccurateOnly) {
                    $updated = addUpdatedInaccurateData(
                        $conn,
                        $rowNum,
                        $recordId,
                        $dateTime,
                        $locationName,
                        $speciesName,
                        $latinName
                    );
                    if ($updated) {
                        deleteInaccurateData($conn, $rowNum);
                    }
                } else {
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

                if (!$updated) {
                    jsonResponse(false, 'Unable to update row.', [
                        'selected_upload_id' => $uploadId,
                        'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                    ], 400);
                }
                jsonResponse(true, 'Row updated successfully.', [
                    'selected_upload_id' => $uploadId,
                    'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                ]);
                break;

            case 'delete-row':
                $uploadId = (int) ($_POST['upload_id'] ?? 0);
                $rowNum = (int) ($_POST['row_num'] ?? 0);

                // if ($rowNum <= 0 || $uploadId <= 0) {
                //     jsonResponse(false, 'Invalid row or upload selected.', [], 400);
                // }

                // if (!deleteSighting($conn, $rowNum)) {
                //     jsonResponse(false, 'Unable to delete row.', [
                //         'selected_upload_id' => $uploadId,
                //         'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                //     ], 400);
                // }
                if ($showInaccurateOnly) {
                    $deleted = deleteInaccurateData($conn, $rowNum);
                } else {
                    $deleted = deleteSighting($conn, $rowNum);
                }

                if (!$deleted) {
                    jsonResponse(false, 'Unable to delete row.', [
                        'selected_upload_id' => $uploadId,
                        'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                    ], 400);
                }

                jsonResponse(true, 'Row deleted successfully.', [
                    'selected_upload_id' => $uploadId,
                    'rows' => getRowsByUploadId($conn, $uploadId, $showInaccurateOnly),
                ]);
                break;

            default:
                jsonResponse(false, 'Unknown AJAX action.', [], 400);
        }
    } catch (Throwable $e) {
        jsonResponse(false, $e->getMessage() !== '' ? $e->getMessage() : 'Request failed.', [], 500);
    }
}

if (isAjaxRequest()) {
    handleAjaxRequest($conn, $uploadDirectory);
}

if (isset($_GET['uploaded-file-select']) && $_GET['uploaded-file-select'] !== '') {
    $selectedUploadId = (int) $_GET['uploaded-file-select'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_files'])) {
    try {
        $result = processUpload($conn, $_FILES['csv_files'], $uploadDirectory);
        $csvRows = $result['rows'] ?? [];
        $selectedUploadId = (int) ($result['selected_upload_id'] ?? 0);
        $storedFiles = $result['stored_files'] ?? getStoredFiles($conn);
        $uploadSuccessMessage = '1 file uploaded successfully.';
    } catch (Throwable $e) {
        $uploadErrorMessage = $e->getMessage();
    }
}
$showInaccurateOnly =
    (isset($_POST['show-inaccurate_only']) && $_POST['show-inaccurate_only'] == '1')
    || (isset($_GET['show-inaccurate-only']) && $_GET['show-inaccurate-only'] == '1');
if ($selectedUploadId > 0 && empty($csvRows)) {
    $csvRows = getRowsByUploadId($conn, $selectedUploadId, $showInaccurateOnly);
}

?>
