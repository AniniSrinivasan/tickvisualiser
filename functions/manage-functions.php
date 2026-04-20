<?php
require_once('../functions/db_connect.php');

function getUsers($conn)
{
    $query = "SELECT 
        u.user_email, u.f_name, u.l_name, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id;
        ";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
function deleteUser(mysqli $conn, string $user_email): bool
{
    try {
        $sql = "DELETE FROM users
                WHERE user_email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();

        return $deleted;
    } catch (Throwable $e) {
        return false;
    }
}

function updateUser(mysqli $conn, string $user_email, string $f_name, string $l_name, int $role_id): bool
{
    try {
        if ($user_email === '') {
            throw new RuntimeException('User email is required.');
        }

        $sql = "UPDATE users
                SET f_name = ?, l_name = ?, role_id = ?
                WHERE user_email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $f_name, $l_name, $role_id, $user_email);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected >= 0;
    } catch (Throwable $e) {
        return false;
    }
}

function getUserEmail(mysqli $conn, string $user_email): int
{
    $user_email = trim($user_email);

    if ($user_email === '') {
        throw new RuntimeException('User email is required.');
    }

    $sql = "INSERT INTO users (user_email)
            VALUES (?)
            ON DUPLICATE KEY UPDATE user_email = LAST_INSERT_ID(user_email)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();

    $user_email = (int) $conn->insert_id;
    $stmt->close();

    return $user_email;
}

function getFname(mysqli $conn, string $f_name): int
{
    $f_name = trim($f_name);

    if ($f_name === '') {
        throw new RuntimeException('First name is required.');
    }

    $sql = "INSERT INTO users (f_name)
            VALUES (?)
            ON DUPLICATE KEY UPDATE user_email = LAST_INSERT_ID(user_email)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $f_name);
    $stmt->execute();

    $f_name = (int) $conn->insert_id;
    $stmt->close();

    return $f_name;
}

function getLname(mysqli $conn, string $l_name): int
{
    $l_name = trim($l_name);

    if ($l_name === '') {
        throw new RuntimeException('Last name is required.');
    }

    $sql = "INSERT INTO users (l_name)
            VALUES (?)
            ON DUPLICATE KEY UPDATE user_email = LAST_INSERT_ID(user_email)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $l_name);
    $stmt->execute();

    $l_name = (int) $conn->insert_id;
    $stmt->close();

    return $l_name;
}

function handleUserAjaxRequest(mysqli $conn, string $uploadDirectory): void
{

    $action = trim((string) ($_POST['ajax_action'] ?? ''));

    $showInaccurateOnly = $_POST['show-inaccurate-only'] ?? 0;
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
                $user_email = trim((string) ($_POST['user_email'] ?? ''));
                $f_name = trim((string) ($_POST['f_name'] ?? ''));
                $l_name = trim((string) ($_POST['l_name'] ?? ''));
                $role_name = trim((string) ($_POST['role_name'] ?? ''));

                if ($showInaccurateOnly === '1') {
                    $updated = updateInaccurateSighting(
                                $conn,
                                $rowNum,
                                $recordId,
                                $user_email,
                                $f_name,
                                $l_name,
                                $role_name
                            );
                } else {
                    $updated = updateSighting(
                        $conn,
                        $rowNum,
                        $recordId,
                        $user_email,
                        $f_name,
                        $l_name,
                        $role_name
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

                if ($showInaccurateOnly === '1') {
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
            case 'approve-row':
                $uploadId = (int) ($_POST['upload_id'] ?? 0);
                $rowNum = (int) ($_POST['row_num'] ?? 0);
                $recordId = trim((string) ($_POST['row_id'] ?? ''));
                $user_email = trim((string) ($_POST['user_email'] ?? ''));
                $f_name = trim((string) ($_POST['f_name'] ?? ''));
                $l_name = trim((string) ($_POST['l_name'] ?? ''));
                $role_name = trim((string) ($_POST['role_name'] ?? ''));

                if ($showInaccurateOnly === '1') {
                    $added = addUpdatedInaccurateData(
                        $conn,
                        $rowNum,
                        $recordId,
                        $user_email,
                        $f_name,
                        $l_name,
                        $role_name,
                        $uploadId
                    );
                    if ($added) {
                        deleteInaccurateData($conn, $rowNum);
                    }
                } 
            case 'update-user':
                $user_email = trim((string) ($_POST['user_email'] ?? ''));
                $f_name = trim((string) ($_POST['f_name'] ?? ''));
                $l_name = trim((string) ($_POST['l_name'] ?? ''));
                $role_name = trim((string) ($_POST['role_name'] ?? ''));

                if (!$user_email || !$f_name || !$l_name || !$role_name) {
                    jsonResponse(false, 'All fields are required.', [], 400);
                }

                // Get role_id from role_name
                $query = "SELECT role_id FROM roles WHERE role_name = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $role_name);
                $stmt->execute();
                $result = $stmt->get_result();
                $role = $result->fetch_assoc();
                $stmt->close();

                if (!$role) {
                    jsonResponse(false, 'Invalid role.', [], 400);
                }

                $role_id = $role['role_id'];
                $updated = updateUser($conn, $user_email, $f_name, $l_name, $role_id);

                if (!$updated) {
                    jsonResponse(false, 'Unable to update user.', [], 400);
                }
                jsonResponse(true, 'User updated successfully.');
                break;

            case 'delete-user':
                $user_email = trim((string) ($_POST['user_email'] ?? ''));

                if (!$user_email) {
                    jsonResponse(false, 'User email is required.', [], 400);
                }

                $deleted = deleteUser($conn, $user_email);

                if (!$deleted) {
                    jsonResponse(false, 'Unable to delete user.', [], 400);
                }
                jsonResponse(true, 'User deleted successfully.');
                break;
        }
    } catch (Throwable $e) {
        jsonResponse(false, $e->getMessage() !== '' ? $e->getMessage() : 'Request failed.', [], 500);
    }
}

?>