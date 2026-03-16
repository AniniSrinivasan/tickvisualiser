<?php

// reference: https://www.tutorialspoint.com/php/php_read_file.htm

$csvRows = [];
$uploadErrorMessage = '';
$uploadSuccessMessage = '';
$uploadedFilesThisRequest = [];

$uploadDirectory = __DIR__ . '/../upload-files'; // sets the directory where uploaded CSV files will be stored

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// checks directory exists and is writable
function ensureUploadDirectory(string $directory): bool
{
    echo $directory;
    if (is_dir($directory)) {
        return is_writable($directory);
    }

    return mkdir($directory, 0775, true);
}

//reads csv, extracts the header and converts the other rows to assoc array
function parseCsvFile(string $filePath): array
{
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

    while (($data = fgetcsv($handle)) !== false) {
        if (count(array_filter($data, fn($value) => trim((string) $value) !== '')) === 0) {
            continue;
        }

        $rowAssoc = [];
        foreach ($header as $index => $columnName) {
            $rowAssoc[$columnName] = $data[$index] ?? '';
        }

        $rows[] = [
            'id' => $rowAssoc['id'] ?? '',
            'date' => $rowAssoc['date'] ?? '',
            'location' => $rowAssoc['location'] ?? '',
            'species' => $rowAssoc['species'] ?? '',
            'latinName' => $rowAssoc['latinName'] ?? '',
        ];
    }

    fclose($handle);

    return [
        'rows' => $rows
    ];
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
            $storedFileName = time() . '-' . preg_replace('/[^A-Za-z0-9._-]/', '-', basename($fileName));
            $destinationPath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedFileName;

            if (!move_uploaded_file($tmpName, $destinationPath)) {
                $uploadErrorMessage = 'Unable to save uploaded file into upload-files folder.';
            } else {
                $uploadedFilesThisRequest[] = [
                    'original_name' => $fileName,
                    'stored_name' => $storedFileName,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ];

                $parsedData = parseCsvFile($destinationPath);
                $csvRows = $parsedData['rows'];

                $uploadSuccessMessage = '1 file uploaded successfully.';
            }
        }
    }
}
?>