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

function getUserByEmail(mysqli $conn, string $user_email)
{
    $user_email = trim($user_email);

    if ($user_email === '') {
        return null;
    }

    $sql = "SELECT user_email, f_name, l_name, role_id
            FROM users
            WHERE user_email = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user;
}

function getRoles(mysqli $conn)
{
    $sql = "SELECT role_id, role_name FROM roles ORDER BY role_name";
    return $conn->query($sql);
}

?>