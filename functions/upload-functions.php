<?php

require_once('../functions/db_connect.php');
// reference: https://www.tutorialspoint.com/php/php_read_file.htm
date_default_timezone_set('Europe/London');

$csvRows = [];
$uploadErrorMessage = '';
$uploadSuccessMessage = '';
$uploadedFilesThisRequest = [];

$uploadDirectory = __DIR__ . '/../upload-files'; // sets the directory where uploaded CSV files will be stored
$storedFiles = getStoredFiles($uploadDirectory);

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// checks directory exists and is writable
function ensureUploadDirectory(string $directory): bool {
    // echo $directory;
    if (is_dir($directory)) {
        return is_writable($directory);
    }

    return mkdir($directory, 0775, true);
}

//reads csv, extracts the header and converts the other rows to assoc array
function parseCsvFile(string $filePath, $conn): array {
    $rows = [];

    if (!is_readable($filePath)) {
        return ['rows' => $rows];
    }

    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        return ['rows' => $rows];
    }

    // first row will be the header row
    $header = fgetcsv($handle);

    if ($header === false) {
        fclose($handle);
        return ['rows' => $rows];
    }

    $header = array_map(static fn($item) => trim((string) $item), $header);

    
    $batchSize = 20;

    // while (($data = fgetcsv($handle)) !== false) {
    //     if (count(array_filter($data, fn($value) => trim((string)$value) !== '')) === 0) {
    //         continue;
    //     }
    
    //     $rowAssoc = [];
    //     foreach ($header as $index => $columnName) {
    //         $rowAssoc[$columnName] = $data[$index] ?? '';
    //     }
    
    //     $rows[] = [
    //         'ID' => $rowAssoc['ID'] ?? '',
    //         'DATE_TIME' => $rowAssoc['DATE_TIME'] ?? '',
    //         'LOCATION' => $rowAssoc['LOCATION'] ?? '',
    //         'SPECIES' => $rowAssoc['SPECIES'] ?? '',
    //         'LATINNAME' => $rowAssoc['LATINNAME'] ?? '',
    //     ];
    
    //     if (count($rows) === $batchSize) {
    //         batchInsertToMySQL($conn, $rows);
    //         $rows = [];
    //     }
    // }
    
    // // Insert remaining records
    // if (!empty($rows)) {
    //     batchInsertToMySQL($conn, $rows);
    // }
    
    fclose($handle);
    
    // Fetch all records back from database
    $result = $conn->query("SELECT ID, DATE_TIME, LOCATION, SPECIES, LATINNAME FROM Tick_Sightings");
    
    if (!$result) {
        die("Fetch failed: " . $conn->error);
    }
    
    $rows = [];
    
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'ID' => $row['ID'] ?? '',
            'DATE_TIME' => $row['DATE_TIME'] ?? '',
            'LOCATION' => $row['LOCATION'] ?? '',
            'SPECIES' => $row['SPECIES'] ?? '',
            'LATINNAME' => $row['LATINNAME'] ?? '',
        ];
    }
    
    return [
        'rows' => $rows
    ];
    



}


   function batchInsertToMySQL($conn, $rows)
{
    if (empty($rows)) {
        return;
    }

    $placeholders = [];
    $values = [];
    $types = "";

    foreach ($rows as $row) {
        $placeholders[] = "(?, ?, ?, ?, ?)";
        $values[] = $row['ID'];
        $values[] = $row['DATE_TIME'];
        $values[] = $row['LOCATION'];
        $values[] = $row['SPECIES'];
        $values[] = $row['LATINNAME'];
        $types .= "sssss";
    }

    $sql = "INSERT INTO Tick_Sightings (ID, DATE_TIME, LOCATION, SPECIES, LATINNAME)
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


   


function getStoredFiles(string $directory): array
{
    if (!is_dir($directory)) {
        return [];
    }
    $files = glob($directory . DIRECTORY_SEPARATOR . '*.csv') ?: [];
    $items = [];
    foreach ($files as $path) {
        $storedName = basename($path);

        $items[] = [
            'stored_name' => $storedName,
            'display_name' => $storedName,
            'uploaded_at' => date('Y-m-d H:i:s', filemtime($path) ?: time()),
            'path' => $path,
        ];
    }
    usort($items, static fn($a, $b) => strcmp($b['stored_name'], $a['stored_name']));
    return $items;
}

// loads previously uploaded file
if (isset($_GET['uploaded-file-select']) && $_GET['uploaded-file-select'] !== '') {
    $selectedFile = basename((string) $_GET['uploaded-file-select']);
    $selectedPath = $uploadDirectory . DIRECTORY_SEPARATOR . $selectedFile;

    if (is_file($selectedPath) && preg_match('/\.csv$/i', $selectedFile)) {
        $parsedData = parseCsvFile($selectedPath, $conn);
        $csvRows = $parsedData['rows'];
    }
}

// validates the uploaded file, saves it in upload-files, parses the csv to get displayed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_files'])) {
    $file = $_FILES['csv_files'];

    // making sure upload folder exists and is writable
    if (!ensureUploadDirectory($uploadDirectory)) {
        $uploadErrorMessage = 'Upload folder could not be created or is not writable.';
    } else {
        $fileName = trim((string) ($file['name'] ?? ''));
        $tmpName = $file['tmp_name'] ?? '';
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error !== UPLOAD_ERR_OK) {
            $uploadErrorMessage = 'There was a problem uploading the file.';
        } elseif (!preg_match('/\.csv$/i', $fileName)) { // making sure its csv
            $uploadErrorMessage = 'Only CSV files can be attached.';
        } elseif ($tmpName === '') {
            $uploadErrorMessage = 'No file was uploaded.';
        } else {
            // creating unique file names - make sure we don't overwrite files
            $originalBaseName = pathinfo($fileName, PATHINFO_FILENAME);
            $sanitisedBaseName = preg_replace('/[^A-Za-z0-9_-]/', '-', $originalBaseName);
            $timestamp = date('Ymd_His');
            $uniqueId = substr(bin2hex(random_bytes(6)), 0, 12);

            $storedFileName = $sanitisedBaseName . '_' . $uniqueId . '_' . $timestamp . '.csv';
            $destinationPath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedFileName;

            if (!move_uploaded_file($tmpName, $destinationPath)) {
                $uploadErrorMessage = 'Unable to save uploaded file into upload-files folder.';
            } else {
                $uploadedFilesThisRequest[] = [
                    'original_name' => $fileName,
                    'stored_name' => $storedFileName,
                    // 'uploaded_at' => date('Y-m-d H:i:s'),
                ];

                $parsedData = parseCsvFile($destinationPath, $conn);
                $csvRows = $parsedData['rows'];

                $uploadSuccessMessage = '1 file uploaded successfully.';
            }
        }
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

    return implode('_', $originalParts) . '.' . $extension;
}
?>