<?php

require_once('../functions/db_connect.php');
date_default_timezone_set('Europe/London');

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

    $sql = "SELECT UPLOADID, UPLOADNAME, UPLOADDATE
            FROM UPLOAD
            ORDER BY UPLOADDATE DESC, UPLOADID DESC";

    $result = $conn->query($sql);

    if (!$result) {
        return [];
    }

    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'upload_id' => (int) $row['UPLOADID'],
            'stored_name' => $row['UPLOADNAME'],
            'display_name' => $row['UPLOADNAME'],
            'uploaded_at' => $row['UPLOADDATE'],
        ];
    }

    return $items;
}

function getRowsByUploadId(mysqli $conn, int $uploadId): array
{
    $rows = [];

    $sql = "SELECT ID, DATE_TIME, LOCATION, SPECIES, LATINNAME
            FROM Tick_Sightings
            WHERE UPLOADID = ?
            ORDER BY TICKID";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $uploadId);
    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'ID' => $row['ID'] ?? '',
            'DATE_TIME' => $row['DATE_TIME'] ?? '',
            'LOCATION' => $row['LOCATION'] ?? '',
            'SPECIES' => $row['SPECIES'] ?? '',
            'LATINNAME' => $row['LATINNAME'] ?? '',
        ];
    }

    $stmt->close();

    return $rows;
}

function updateTickSighting(
    mysqli $conn,
    int $uploadId,
    string $rowId,
    string $dateTime,
    string $location,
    string $species,
    string $latinName
): bool {
    $sql = "UPDATE Tick_Sightings
            SET DATE_TIME = ?, LOCATION = ?, SPECIES = ?, LATINNAME = ?
            WHERE UPLOADID = ? AND ID = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ssssis", $dateTime, $location, $species, $latinName, $uploadId, $rowId);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function deleteTickSighting(mysqli $conn, int $uploadId, string $rowId): bool
{
    $sql = "DELETE FROM Tick_Sightings
            WHERE UPLOADID = ? AND ID = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    // i for int and s for str
    $stmt->bind_param("is", $uploadId, $rowId);
    $stmt->execute();

    $deleted = $stmt->affected_rows > 0;
    $stmt->close();

    return $deleted;
}

function parseCsvFile(string $filePath, mysqli $conn): array
{
    $rows = [];

    if (!is_readable($filePath)) {
        return ['rows' => $rows, 'upload_id' => 0];
    }

    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        return ['rows' => $rows, 'upload_id' => 0];
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        return ['rows' => $rows, 'upload_id' => 0];
    }

    $uploadName = basename($filePath);
    $uploadDate = date('Y-m-d H:i:s');

    $sql = "INSERT INTO UPLOAD (UPLOADNAME, UPLOADDATE) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        fclose($handle);
        return ['rows' => $rows, 'upload_id' => 0];
    }

    $stmt->bind_param("ss", $uploadName, $uploadDate);
    $stmt->execute();
    $uploadId = (int) $stmt->insert_id;
    $stmt->close();

    $header = array_map(static fn($item) => trim((string) $item), $header);

    $batchRows = [];
    $batchSize = 20;

    while (($data = fgetcsv($handle)) !== false) {
        if (count(array_filter($data, fn($value) => trim((string) $value) !== '')) === 0) {
            continue;
        }

        $rowAssoc = [];
        foreach ($header as $index => $columnName) {
            $rowAssoc[$columnName] = $data[$index] ?? '';
        }

        $batchRows[] = [
            'ID' => trim((string) ($rowAssoc['id'] ?? '')),
            'DATE_TIME' => trim((string) ($rowAssoc['date'] ?? '')),
            'LOCATION' => trim((string) ($rowAssoc['location'] ?? '')),
            'SPECIES' => trim((string) ($rowAssoc['species'] ?? '')),
            'LATINNAME' => trim((string) ($rowAssoc['latinName'] ?? '')),
        ];

        if (count($batchRows) >= $batchSize) {
            batchInsertToMySQL($conn, $batchRows, $uploadId);
            $batchRows = [];
        }
    }

    if (!empty($batchRows)) {
        batchInsertToMySQL($conn, $batchRows, $uploadId);
    }

    fclose($handle);

    return [
        'rows' => getRowsByUploadId($conn, $uploadId),
        'upload_id' => $uploadId,
    ];
}

function batchInsertToMySQL(mysqli $conn, array $rows, int $uploadId): void
{
    if (empty($rows)) {
        return;
    }

    $placeholders = [];
    $values = [];
    $types = '';

    foreach ($rows as $row) {
        $placeholders[] = "(?, ?, ?, ?, ?, ?)";
        $values[] = $row['ID'];
        $values[] = $row['DATE_TIME'];
        $values[] = $row['LOCATION'];
        $values[] = $row['SPECIES'];
        $values[] = $row['LATINNAME'];
        $values[] = $uploadId;
        $types .= "sssssi";
    }

    $sql = "INSERT INTO Tick_Sightings (ID, DATE_TIME, LOCATION, SPECIES, LATINNAME, UPLOADID)
            VALUES " . implode(", ", $placeholders);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$values);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
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


if (isset($_GET['uploaded-file-select']) && $_GET['uploaded-file-select'] !== '') {
    $selectedUploadId = (int) $_GET['uploaded-file-select'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['uploaded-file-select']) && $_POST['uploaded-file-select'] !== '') {
        $selectedUploadId = (int) $_POST['uploaded-file-select'];
    }

    if (isset($_POST['edit-row'])) {
        $editingRowId = trim((string) ($_POST['row-id'] ?? ''));
    }

    if (isset($_POST['cancel-row'])) {
        $editingRowId = null;
    }

    if (isset($_POST['save-row'])) {
        $rowId = trim((string) ($_POST['row-id'] ?? ''));
        $dateTime = trim((string) ($_POST['date_time'] ?? ''));
        $location = trim((string) ($_POST['location'] ?? ''));
        $species = trim((string) ($_POST['species'] ?? ''));
        $latinName = trim((string) ($_POST['latin_name'] ?? ''));

        if ($selectedUploadId > 0 && $rowId !== '') {
            if (updateTickSighting($conn, $selectedUploadId, $rowId, $dateTime, $location, $species, $latinName)) {
                $uploadSuccessMessage = 'Row updated successfully.';
                $editingRowId = null;
            } else {
                $uploadErrorMessage = 'Unable to update row.';
                $editingRowId = $rowId;
            }
        }
    }

    if (isset($_POST['reject'])) {
        $rowId = trim((string) ($_POST['row-id'] ?? ''));
    
        if ($selectedUploadId > 0 && $rowId !== '') {
            if (deleteTickSighting($conn, $selectedUploadId, $rowId)) {
                $uploadSuccessMessage = 'Row deleted successfully.';
            } else {
                $uploadErrorMessage = 'Unable to delete row.';
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_files'])) {
    $file = $_FILES['csv_files'];

    if (!ensureUploadDirectory($uploadDirectory)) {
        $uploadErrorMessage = 'Upload folder could not be created or is not writable.';
    } else {
        $fileName = trim((string) ($file['name'] ?? ''));
        $tmpName = $file['tmp_name'] ?? '';
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error !== UPLOAD_ERR_OK) {
            $uploadErrorMessage = 'There was a problem uploading the file.';
        } elseif (!preg_match('/\.csv$/i', $fileName)) {
            $uploadErrorMessage = 'Only CSV files can be attached.';
        } elseif ($tmpName === '') {
            $uploadErrorMessage = 'No file was uploaded.';
        } else {
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
                $uploadErrorMessage = 'Unable to save uploaded file into upload-files folder.';
            } else {
                $uploadedFilesThisRequest[] = [
                    'original_name' => $fileName,
                    'stored_name' => $storedFileName,
                ];

                $parsedData = parseCsvFile($destinationPath, $conn);
                $csvRows = $parsedData['rows'];
                $selectedUploadId = (int) ($parsedData['upload_id'] ?? 0);

                $storedFiles = getStoredFiles($conn);
                $uploadSuccessMessage = '1 file uploaded successfully.';
            }
        }
    }
}


if ($selectedUploadId > 0 && empty($csvRows)) {
    $csvRows = getRowsByUploadId($conn, $selectedUploadId);
}
?>